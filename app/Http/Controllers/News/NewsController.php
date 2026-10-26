<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessArticlePublishing;
use App\Models\News\News;
use App\Models\Publications\MeetingMinutes;
use App\Models\Settings\AuditLogEntry;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use App\Support\Authorization\PermissionCatalog;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $articles = News::where('certification', false)->get()->sortByDesc('id');

        return view('dashboard.news.index', compact('articles'));
    }

    public function createArticle(): View
    {
        $staff = StaffMember::where('user_id', '!=', 1)->get();

        return view('dashboard.news.articles.create', compact('staff'));
    }

    public function postArticle(Request $request): RedirectResponse
    {
        // Define validator messages
        $messages = [
            'title.required' => 'A title is required.',
            'title.max' => 'A title may not be more than 100 characters long.',
            'image.mimes' => 'Invalid Image. Please use .PNG, .JPG or .GIF.',
            'content.required' => 'Content is required.',
            'emailOption.required' => 'Please select an email option.',
        ];

        // Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'content' => 'required',
            'emailOption' => 'required|in:no,subscribers',
        ], $messages);

        // Redirect if fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createArticleErrors');
        }

        $article = new News;

        // Assign title
        $article->title = $request->get('title');

        // Date for publishing
        $article->published = date('Y-m-d H:i:s');

        // Create slug
        $article->slug = Str::slug($request->get('title').'-'.Carbon::now()->toDateString());

        // Upload image if it exists
        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->putFile('files/news', $request->file('image'));

            if (! $path) {
                return back()->withInput()->with('error', 'The article image could not be stored!');
            }

            $article->image = Storage::disk('public')->url($path);
        }

        // Create a summary if required
        if (! $request->get('summary')) {
            $article->summary = strtok($request->get('content'), '\n');
        } else {
            $article->summary = $request->get('summary');
        }

        // Assign author
        $article->user_id = $request->get('author');
        if ($request->get('showAuthor') == 'on') {
            $article->show_author = true;
        } else {
            $article->show_author = false;
        }

        // Content
        $article->content = $request->get('content');

        // Options
        // Publicly visible
        if ($request->get('articleVisible') == 'on') {
            $article->visible = true;
        } else {
            $article->visible = false;
        }
        // Email level
        $article->email_level = $request->get('emailOption') === 'subscribers' ? News::EMAIL_SUBSCRIBERS : News::EMAIL_NONE;

        // Create and publish if needed
        $article->save();
        if ($article->visible) {
            ProcessArticlePublishing::dispatch($article);
            $request->session()->flash('articleCreated', 'Article created and published!');
        } else {
            $request->session()->flash('artileCreated', 'Article created, but not yet published.');
        }

        return redirect()->route('news.articles.view', $article->slug);
    }

    public function viewArticle($slug): View
    {
        $staff = StaffMember::where('user_id', '!=', 1)->get();
        $article = News::where('slug', $slug)->firstOrFail();

        return view('dashboard.news.articles.view', compact('article', 'staff'));
    }

    public function viewArticlePublic($slug): View
    {
        $article = News::where('slug', $slug)->firstOrFail();
        if (! $article->visible && (! Auth::check() || ! Auth::user()->hasPermissionTo(PermissionCatalog::NEWS_MANAGE))) {
            abort(404);
        }

        return view('news.article', compact('article'));
    }

    public function editArticle(Request $request, $id): RedirectResponse
    {
        // Define validator messages
        $messages = [
            'title.required' => 'A title is required.',
            'title.max' => 'A title may not be more than 100 characters long.',
            'image.mimes' => 'We need an image file in the jpg png or gif formats.',
            'content.required' => 'Content is required.',
        ];

        // Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'content' => 'required',
        ], $messages);

        // Redirect if fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createArticleErrors');
        }

        $article = News::findOrFail($id);
        $article->title = $request->get('title');
        // Upload image if it exists
        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->putFile('files/news', $request->file('image'));

            if (! $path) {
                return back()->withInput()->with('error', 'The article image could not be stored!');
            }

            $article->image = Storage::disk('public')->url($path);
        }

        // Create a summary if required
        if (! $request->get('summary')) {
            $article->summary = strtok($request->get('content'), '\n');
        } else {
            $article->summary = $request->get('summary');
        }

        // Content
        $article->content = $request->get('content');

        // Options
        // Publicly visible
        if ($request->get('articleVisible')) {
            $article->visible = true;
        } else {
            $article->visible = false;
        }

        // Create and publish if needed
        $article->save();

        return redirect()->Back()->withSuccess('Successfully Edited Article!');
    }

    public function deleteArticle($id): RedirectResponse
    {
        $article = News::whereId($id)->firstOrFail();
        AuditLogEntry::insert(Auth::user(), 'Deleted News Article '.$article->id, User::find(1), 0);
        $article->delete();

        return redirect('/admin/news')->withSuccess('Article Deleted!');
    }

    public function viewAllPublic(): View
    {
        $news = News::where('visible', true)->get()->sortByDesc('id');

        return view('news.index', compact('news'));
    }

    public function minutesIndex(): View
    {
        $minutes = MeetingMinutes::with('user')->latest()->get();

        return view('publications.meetingminutes', compact('minutes'));
    }

    public function minutesDelete($id): RedirectResponse
    {
        $minutes = MeetingMinutes::whereId($id)->firstOrFail();
        $urlPath = parse_url($minutes->link, PHP_URL_PATH);

        if (is_string($urlPath) && Str::startsWith($urlPath, '/storage/')) {
            $filePath = ltrim(Str::after($urlPath, '/storage/'), '/');

            if (Str::startsWith($filePath, 'files/minutes/') && ! Str::contains($filePath, ['\\', '..'])) {
                Storage::disk('public')->delete($filePath);
            }
        }
        AuditLogEntry::insert(Auth::user(), 'Deleted meeting minutes '.$minutes->title, User::find(1), 0);
        $minutes->delete();

        return redirect()->back()->with('info', 'Deleted item');
    }

    public function minutesUpload(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');

        $fileName = Str::uuid().'.'.$file->extension();
        $stored = Storage::disk('public')->putFileAs('files/minutes', $file, $fileName);

        if (! $stored) {
            return back()->withInput()->with('error', 'The meeting minutes file could not be stored!');
        }

        $minutes = new MeetingMinutes([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'link' => Storage::disk('public')->url($stored),
        ]);

        $minutes->save();

        AuditLogEntry::insert(Auth::user(), 'Uploaded meeting minutes '.$minutes->title, User::find(1), 0);

        return redirect()->back()->with('success', 'Minutes uploaded!');
    }
}
