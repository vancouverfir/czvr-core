<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\Settings\HomepageImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    /*
    Site info
    */
    public function siteInformation()
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Return the view
        return view('admin.settings.siteinformation', compact('coreSettings'));
    }

    /*
    Save site info
    */
    public function saveSiteInformation(Request $request)
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Save changes
        $coreSettings->sys_name = $request->get('sys_name');
        $coreSettings->release = $request->get('release');
        $coreSettings->sys_build = $request->get('sys_build');
        $coreSettings->copyright_year = $request->get('copyright_year');
        $coreSettings->save();

        //Return the view
        return view('admin.settings.siteinformation', compact('coreSettings'))->with('success', 'Settings saved');
    }

    /*
    Emails
    */
    public function emails()
    {
        //Get settings
        $coreSettings = CoreSettings::find(1);

        //Return the view
        return view('admin.settings.emails', compact('coreSettings'));
    }

    /*
    Save emails
    */
    public function saveEmails(Request $request)
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        //Save changes
        $coreSettings->emailfirchief = $request->get('emailfirchief');
        $coreSettings->emaildepfirchief = $request->get('emaildepfirchief');
        $coreSettings->emailcinstructor = $request->get('emailcinstructor');
        $coreSettings->emaileventc = $request->get('emaileventc');
        $coreSettings->emailfacilitye = $request->get('emailfacilitye');
        $coreSettings->emailwebmaster = $request->get('emailwebmaster');
        $coreSettings->save();

        //Return the view
        return view('admin.settings.emails', compact('coreSettings'))->with('success', 'Emails saved');
    }

    /*
    Audit log
    */
    public function auditLog()
    {
        $entriesall = AuditLogEntry::all();
        $entries = $entriesall->sortByDesc('created_at');

        return view('admin.settings.auditlog', compact('entries'));
    }

    public function banner()
    {
        $banner = CoreSettings::find(1);

        return view('admin.settings.banner', compact('banner'));
    }

    public function bannerEdit(Request $request)
    {
        //Get the settings
        $coreSettings = CoreSettings::find(1);

        if ($request->get('bannerMessage') == null) {
            $bannerMessage = '';
        } else {
            $bannerMessage = $request->get('bannerMessage');
        }

        if ($request->get('bannerLink') == null) {
            $bannerLink = '';
        } else {
            $bannerLink = $request->get('bannerLink');
        }

        if ($request->get('bannerMode') == null) {
            $bannerMode = '';
            $bannerMessage = '';
            $bannerLink = '';
        } else {
            $bannerMode = $request->get('bannerMode');
        }

        $coreSettings->banner = $bannerMessage;
        $coreSettings->bannerMode = $bannerMode;
        $coreSettings->bannerLink = $bannerLink;
        $coreSettings->save();

        return back()->withSuccess('The banner has been updated!');
    }

    public function imagesIndex()
    {
        $images = HomepageImages::all();

        return view('admin.settings.homepageimages', compact('images'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg|max:1048',
            'nameCredit' => 'required',
        ]);

        $fileName = time().'.'.request()->file->getClientOriginalExtension();

        $fileUrl = Storage::url('public/files/homepageimages/'.$fileName);

        Storage::putFileAs(
            'public/files/homepageimages', request()->file, $fileName
        );

        $image = new HomepageImages();
        $image->url = '/storage/files/homepageimages/'.$fileName;
        $image->credit = $request->nameCredit;
        $image->CSS = 'background-size: cover; background-repeat: no-repeat; background-position: center;';
        $image->save();

        return back()->with('success', 'File uploaded to: <a href='.config('app.url').'/storage/files/homepageimages/'.$fileName.'>'.config('app.url').'/storage/files/homepageimages/'.$fileName.'</a>');
    }

    public function editImage(Request $request, $id)
    {
        $this->validate($request, [
            'nameCredit' => 'required',
        ]);

        $image = HomepageImages::find($id);

        $image->credit = $request->nameCredit;
        $image->CSS = $request->CSS;
        $image->save();

        return back()->withSuccess('Image edited successfully!');
    }

    public function testImage($id)
    {
        $image = HomepageImages::where('id', $id)->first();

        return view('admin.settings.testimage', compact('image'));
    }

    public function deleteImage($id)
    {
        $totalImages = HomepageImages::count();

        if ($totalImages <= 1) {
            return back()->with('error', 'Please add another image before deleting this one!');
        }
        $image = HomepageImages::where('id', $id)->first();
        if ($image) {
            $filePath = str_replace('/storage', 'public', $image->url);

            if (! Storage::exists($filePath)) {
                return back()->with('error', 'File not found in storage!');
            }
            Storage::delete($filePath);
            $image->delete();

            return back()->withSuccess('Image and file deleted successfully!');
        }

        return back()->with('error', 'Image not found in the database!');
    }

    public function viewRoles()
    {
        $roles = Role::all();

        return view('admin.settings.roles', compact('roles'));
    }

    public function addRole(Request $request)
    {
        $check = Role::where('name', $request->input('name'))->first();
        if ($check != null) {
            return back()->withError('This role already exists!');
        }
        $role = Role::create(['name' => $request->input('name'), 'protected' => $request->input('secure')]);

        return back()->withSuccess('Added the role!');
    }

    public function deleteRole($id)
    {
        $role = Role::findByName($id);
        $message = $role->name;
        $role->delete();

        return back()->withSuccess('Deleted the '.$message.' Role!');
    }
}
