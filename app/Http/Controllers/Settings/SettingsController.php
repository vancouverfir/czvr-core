<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\Settings\HomepageImages;
use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Support\Authorization\PermissionCatalog;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    /*
    Site info
    */
    public function siteInformation(): View
    {
        // Get the settings
        $coreSettings = CoreSettings::find(1);

        // Return the view
        return view('admin.settings.siteinformation', compact('coreSettings'));
    }

    /*
    Save site info
    */
    public function saveSiteInformation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sys_name' => ['required', 'string', 'max:255'],
            'release' => ['required', 'string', 'max:100'],
            'sys_build' => ['required', 'string', 'max:100'],
            'copyright_year' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
        ]);

        CoreSettings::query()->findOrFail(1)->update($data);

        return redirect()->route('settings.siteinformation')->withSuccess('Site information saved successfully.');
    }

    /*
    Emails
    */
    public function emails(): View
    {
        // Get settings
        $coreSettings = CoreSettings::find(1);

        // Return the view
        return view('admin.settings.emails', compact('coreSettings'));
    }

    /*
    Save emails
    */
    public function saveEmails(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'emailfirchief' => ['required', 'email:rfc', 'max:255'],
            'emaildepfirchief' => ['required', 'email:rfc', 'max:255'],
            'emailcinstructor' => ['required', 'email:rfc', 'max:255'],
            'emaileventc' => ['required', 'email:rfc', 'max:255'],
            'emailfacilitye' => ['required', 'email:rfc', 'max:255'],
            'emailwebmaster' => ['required', 'email:rfc', 'max:255'],
        ]);

        CoreSettings::query()->findOrFail(1)->update($data);

        return redirect()->route('settings.emails')->withSuccess('Staff email addresses saved successfully.');
    }

    /*
    Audit log
    */
    public function auditLog(): View
    {
        $entriesall = AuditLogEntry::all();
        $entries = $entriesall->sortByDesc('created_at');

        return view('admin.settings.auditlog', compact('entries'));
    }

    public function banner(): View
    {
        $banner = CoreSettings::find(1);

        return view('admin.settings.banner', compact('banner'));
    }

    public function bannerEdit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bannerMode' => ['required', 'in:hidden,success,danger,warning,info,primary,secondary,light,dark,muted,white'],
            'bannerMessage' => ['nullable', 'string', 'max:500', 'required_unless:bannerMode,hidden'],
            'bannerLink' => ['nullable', 'url:http,https', 'max:2048'],
        ]);

        $mode = $data['bannerMode'] === 'hidden' ? '' : $data['bannerMode'];
        CoreSettings::query()->findOrFail(1)->update([
            'banner' => $mode === '' ? '' : ($data['bannerMessage'] ?? ''),
            'bannerMode' => $mode,
            'bannerLink' => $mode === '' ? '' : ($data['bannerLink'] ?? ''),
        ]);

        return back()->withSuccess('The banner has been updated!');
    }

    public function imagesIndex(): View
    {
        $images = HomepageImages::all();

        return view('admin.settings.homepageimages', compact('images'));
    }

    public function uploadImage(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg|max:2048',
            'nameCredit' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid().'.'.$file->extension();
        $stored = Storage::disk('public')->putFileAs('files/homepageimages', $file, $fileName);

        if (! $stored) {
            return back()->with('error', 'The image could not be stored! Please try again!');
        }

        $url = '/storage/'.$stored;

        $image = new HomepageImages;
        $image->url = $url;
        $image->credit = $request->nameCredit;
        $image->CSS = 'background-size: cover; background-repeat: no-repeat; background-position: center;';
        $image->save();
        Cache::forget('home.background');

        return back()->with('success', 'File uploaded to: <a href="'.$url.'">'.$url.'</a>');
    }

    public function editImage(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nameCredit' => 'required',
        ]);

        $image = HomepageImages::find($id);

        $image->credit = $request->nameCredit;
        $image->CSS = $request->CSS;
        $image->save();
        Cache::forget('home.background');

        return back()->withSuccess('Image edited successfully!');
    }

    public function testImage($id): View
    {
        $image = HomepageImages::where('id', $id)->first();

        return view('admin.settings.testimage', compact('image'));
    }

    public function deleteImage($id): RedirectResponse
    {
        $totalImages = HomepageImages::count();

        if ($totalImages <= 1) {
            return back()->with('error', 'Please add another image before deleting this one!');
        }
        $image = HomepageImages::find($id);

        if (! $image) {
            return back()->with('error', 'Image not found in the database!');
        }

        $urlPath = parse_url($image->url, PHP_URL_PATH);
        $storagePrefix = '/storage/';

        if (! is_string($urlPath) || ! Str::startsWith($urlPath, $storagePrefix)) {
            return back()->with('error', 'The stored image path is invalid!');
        }

        $filePath = ltrim(Str::after($urlPath, $storagePrefix), '/');

        if (! Str::startsWith($filePath, 'files/homepageimages/') || Str::contains($filePath, ['\\', '..'])) {
            return back()->with('error', 'The stored image path is invalid!');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($filePath)) {
            return back()->with('error', 'File not found in storage!');
        }

        if (! $disk->delete($filePath)) {
            return back()->with('error', 'The image file could not be deleted!');
        }

        $image->delete();
        Cache::forget('home.background');

        return back()->withSuccess('Image and file deleted successfully!');
    }

    public function viewRoles(Request $request): View
    {
        $roles = Role::query()->with('permissions')->orderBy('name')->get();
        $permissionGroups = PermissionCatalog::grouped();
        $selectedRole = $roles->firstWhere('id', (int) $request->integer('role')) ?? $roles->first();

        return view('admin.settings.roles', compact('roles', 'permissionGroups', 'selectedRole'));
    }

    public function addRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        $slug = Str::slug($data['name']);
        $slug = $slug !== '' ? $slug : 'role';
        $baseSlug = $slug;
        $suffix = 2;

        while (Role::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);
        $this->syncRolePermissions($role, array_values(array_filter(
            $data['permissions'] ?? [],
            fn (string $slug) => $request->user()->canGrantPermission($slug),
        )));

        return redirect()
            ->route('roles.view', ['role' => $role->id])
            ->withSuccess('Added the role!');
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()->canAssignRole($role), 403, 'Only administrators can change the Staff and Administrator roles!');

        $data = $request->validate([
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        // Permissions the editor does not hold keep their current state on the role
        $requested = $data['permissions'] ?? [];
        $current = $role->permissions()->pluck('slug')->all();
        $slugs = array_values(array_filter(
            PermissionCatalog::slugs(),
            fn (string $slug) => $request->user()->canGrantPermission($slug)
                ? in_array($slug, $requested, true)
                : in_array($slug, $current, true),
        ));

        $this->syncRolePermissions($role, $slugs);

        return back()->withSuccess('Updated the '.$role->name.' permissions!');
    }

    public function deleteRole($id): RedirectResponse
    {
        $role = Role::query()->where('id', $id)->orWhere('name', $id)->firstOrFail();
        abort_if(in_array($role->slug, SystemRoleCatalog::protectedSlugs(), true), 403, 'Built-in roles cannot be deleted.');
        $message = $role->name;
        $role->delete();

        return back()->withSuccess('Deleted the '.$message.' Role!');
    }

    /** @param list<string> $slugs */
    private function syncRolePermissions(Role $role, array $slugs): void
    {
        if (in_array($role->slug, ['admin', 'administrator'], true)) {
            $slugs = PermissionCatalog::slugs();
        }

        $role->permissions()->sync(
            Permission::query()->whereIn('slug', $slugs)->pluck('id'),
        );
    }
}
