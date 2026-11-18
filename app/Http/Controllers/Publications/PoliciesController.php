<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessArticlePublishing;
use App\Models\News\News;
use App\Models\Publications\Policy;
use App\Models\Publications\PolicySection;
use App\Models\Settings\AuditLogEntry;
use App\Support\Authorization\PermissionCatalog;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PoliciesController extends Controller
{
    public function index(): View
    {
        if (! Auth::check() || ! Auth::user()->hasPermissionTo(PermissionCatalog::POLICIES_MANAGE)) {
            $policySections = PolicySection::all();

            return view('publications.policies', compact('policySections'));
        } else {
            $policySections = PolicySection::all();
            $nullPolicies = Policy::where('section_id', null)->get();
            $allPolicies = Policy::all();

            return view('publications.policies', compact('nullPolicies', 'policySections', 'allPolicies'));
        }
    }

    public function addPolicy(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'required|integer',
            'details' => 'required|string|max:250',
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'embed' => 'required|integer|in:0,1',
            'staff_only' => 'required|integer|in:0,1',
            'email' => 'required|string|in:none,all,emailcert,newsonly',
            'date' => 'nullable|date',
        ]);

        if ($request->get('staff_only') == 1 && $request->get('email') != 'none') {
            return redirect()->route('policies')->with('error', 'A private policy cannot be released publicly via email or a news article.')->withInput();
        }

        if ($request->get('section') == -1) {
            return back()->with('error', 'You need to select a section!');
        }

        if ($request->get('date')) {
            $date = $request->get('date');
        } else {
            $date = date('Y-m-d');
        }

        $file = $request->file('file');

        $fileName = Str::uuid().'.'.$file->extension();
        $stored = Storage::disk('public')->putFileAs('files/policies', $file, $fileName);

        if (! $stored) {
            return back()->withInput()->with('error', 'The policy file could not be stored!');
        }

        $filePath = Storage::disk('public')->url($stored);

        $policy = new Policy([
            'section_id' => $request->get('section'),
            'name' => $request->get('name'),
            'details' => $request->get('details'),
            'link' => $filePath,
            'embed' => $request->get('embed'),
            'staff_only' => $request->get('staff_only'),
            'author' => Auth::user()->id,
            'releaseDate' => $date,
        ]);

        $policy->save();

        $emailOption = $request->string('email')->toString();

        if ($emailOption !== 'none') {
            $content = "The '".$policy->name."' policy for Vancouver has been released! Read it on the policies page!";

            $news = News::create([
                'title' => 'New Policy: '.$policy->name,
                'content' => $content,
                'summary' => $content,
                'published' => now(),
                'visible' => true,
                'email_level' => $emailOption === 'newsonly' ? News::EMAIL_NONE : News::EMAIL_SUBSCRIBERS,
                'certification' => false,
                'show_author' => false,
                'user_id' => Auth::id(),
                'slug' => Str::slug('new-policy-'.$policy->name.'-'.$policy->id),
            ]);

            if (in_array($emailOption, ['all', 'emailcert'], true)) {
                ProcessArticlePublishing::dispatch($news, $emailOption === 'emailcert')->afterCommit();
            }
        }
        AuditLogEntry::staff('Added policy '.$policy->name.' ('.$policy->id.')'.($policy->staff_only ? ', staff only' : '').($emailOption !== 'none' ? ', released with news ('.$emailOption.')' : ''));

        return redirect()->route('policies')->with('success', 'Policy '.$policy->name.' added!');
    }

    public function editPolicy(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'required|integer',
            'details' => 'required|string|max:250',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'embed' => 'required|integer|in:0,1',
            'staff_only' => 'required|integer|in:0,1',
            'date' => 'nullable|date',
        ]);

        if ($request->get('section') == -1) {
            return back()->with('error', 'You need to select a section!');
        }

        $policy = Policy::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid().'.'.$file->extension();
            $stored = Storage::disk('public')->putFileAs('files/policies', $file, $fileName);

            if (! $stored) {
                return back()->withInput()->with('error', 'The replacement policy file could not be stored!');
            }

            $oldPath = $this->publicPolicyPath($policy->link);

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $policy->link = Storage::disk('public')->url($stored);
        }

        $policy->section_id = $request->get('section');
        $policy->name = $request->get('name');
        $policy->details = $request->get('details');
        $policy->embed = $request->get('embed');
        $policy->staff_only = $request->get('staff_only');
        $policy->author = Auth::user()->id;
        $policy->releaseDate = $request->input('date') ?: $policy->releaseDate;

        $policy->save();
        AuditLogEntry::staff('Edited policy '.$policy->name.' ('.$policy->id.')'.($request->hasFile('file') ? ', replaced the file' : ''));

        return redirect()->route('policies')->with('success', 'Policy '.$policy->name.' edited!');
    }

    public function deletePolicy($id): RedirectResponse
    {
        $policy = Policy::where('id', $id)->firstOrFail();

        $filePath = $this->publicPolicyPath($policy->link);

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        $policy->delete();
        AuditLogEntry::staff('Deleted policy '.$policy->name.' ('.$policy->id.')');

        return redirect()->route('policies')->with('success', 'Policy deleted!');
    }

    private function publicPolicyPath(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $urlPath = parse_url($url, PHP_URL_PATH);

        if (! is_string($urlPath) || ! Str::startsWith($urlPath, '/storage/')) {
            return null;
        }

        $path = ltrim(Str::after($urlPath, '/storage/'), '/');

        return Str::startsWith($path, 'files/policies/') && ! Str::contains($path, ['\\', '..']) ? $path : null;
    }

    public function addPolicySection(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        $policySection = new PolicySection([
            'section_name' => $request->get('name'),
        ]);

        $policySection->save();
        AuditLogEntry::staff('Added policy section '.$policySection->section_name);

        return redirect()
            ->route('policies')
            ->with('success', 'New policy section: '.$request->get('name').' created!');
    }

    public function deletePolicySection($id): RedirectResponse
    {
        $section = PolicySection::where('id', $id)->firstOrFail();
        $policies = Policy::all();

        foreach ($policies as $p) {
            if ($p->section_id == $id) {
                Policy::where('id', $p->id)
                    ->update(['section_id' => null, 'staff_only' => 1]);
            }
        }

        $section->delete();
        AuditLogEntry::staff('Deleted policy section '.$section->section_name.' (its policies were moved to no section, staff only)');

        return redirect()->route('policies')->with('success', 'Policy section '.$section->section_name.' deleted!');
    }
}
