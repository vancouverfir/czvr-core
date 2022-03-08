<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessArticlePublishing;
use App\Models\News\News;
use App\Models\Publications\MeetingMinutes;
use App\Models\Settings\AuditLogEntry;
use App\Models\Users\StaffMember;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $articles = News::where('certification', false)->get()->sortByDesc('id');

        return view('dashboard.news.index', compact('articles'));
    }

    public function createArticle()
    {
        $staff = StaffMember::where('user_id', '!=', 1)->get();

        return view('dashboard.news.articles.create', compact('staff'));
    }

    public function postArticle(Request $request)
    {
        //Define validator messages
        $messages = [
            'title.required' => 'A title is required.',
            'title.max' => 'A title may not be more than 100 characters long.',
            'image.mimes' => 'Invalid Image. Please use .PNG, .JPG or .GIF.',
            'content.required' => 'Content is required.',
            'emailOption.required' => 'Please select an email option.',
        ];

        //Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'image' => 'mimes:jpeg,jpg,png,gif',
            'content' => 'required',
            'emailOption' => 'required',
        ], $messages);

        //Redirect if fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createArticleErrors');
        }

        $article = new News();

        //Assign title
        $article->title = $request->get('title');

        //Date for publishing
        $article->published = date('Y-m-d H:i:s');

        //Create slug
        $article->slug = Str::slug($request->get('title').'-'.Carbon::now()->toDateString());

        //Upload image if it exists
        if ($request->file('image')) {
            $basePath = 'public/files/'.Carbon::now()->toDateString().'/'.rand(1000, 2000);
            $path = $request->file('image')->store($basePath);
            $article->image = Storage::url($path);
        }

        //Create a summary if required
        if (! $request->get('summary')) {
            $article->summary = strtok($request->get('content'), '\n');
        } else {
            $article->summary = $request->get('summary');
        }

        //Assign author
        $article->user_id = $request->get('author');
        if ($request->get('showAuthor') == 'on') {
            $article->show_author = true;
        } else {
            $article->show_author = false;
        }

        //Content
        $article->content = $request->get('content');

        //Options
        //Publicly visible
        if ($request->get('articleVisible') == 'on') {
            $article->visible = true;
        } else {
            $article->visible = false;
        }
        //Email level
        switch ($request->get('emailOption')) {
            case 'no':
                $article->email_level = 0;
            break;
            case 'controllers':
                $article->email_level = 1;
            break;
            case 'all':
                $article->email_level = 2;
            break;
            case 'allimportant':
                $article->email_level = 3;
            break;
        }

        //Create and publish if needed
        $article->save();
        if ($article->visible) {
            ProcessArticlePublishing::dispatch($article);
            $request->session()->flash('articleCreated', 'Article created and published!');
        } else {
            $request->session()->flash('artileCreated', 'Article created, but not yet published.');
        }

        return redirect()->route('news.articles.view', $article->slug);
    }

    public function viewArticle($slug)
    {
        $staff = StaffMember::where('user_id', '!=', 1)->get();
        $article = News::where('slug', $slug)->firstOrFail();

        return view('dashboard.news.articles.view', compact('article', 'staff'));
    }

    public function viewArticlePublic($slug)
    {
        $article = News::where('slug', $slug)->firstOrFail();
        if (! $article->visible) {
            if (Auth::check() && ! Auth::user()->permissions > 3) {
                abort(403, 'This article is hidden.');
            }
        }

        return view('publicarticle', compact('article'));
    }

    public function editArticle(Request $request, $id)
    {

    //Define validator messages
        $messages = [
            'title.required' => 'A title is required.',
            'title.max' => 'A title may not be more than 100 characters long.',
            'image.mimes' => 'We need an image file in the jpg png or gif formats.',
            'content.required' => 'Content is required.',
        ];

        //Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'image' => 'mimes:jpeg,jpg,png,gif',
            'content' => 'required',
        ], $messages);

        //Redirect if fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator, 'createArticleErrors');
        }

        $article = News::where('id', $id)->first();
        if ($article != null) {
            $article->title = $request->get('title');
        }
        //Upload image if it exists
        if ($request->file('image')) {
            $basePath = 'public/files/'.Carbon::now()->toDateString().'/'.rand(1000, 2000);
            $path = $request->file('image')->store($basePath);
            $article->image = Storage::url($path);
        }

        //Create a summary if required
        if (! $request->get('summary')) {
            $article->summary = strtok($request->get('content'), '\n');
        } else {
            $article->summary = $request->get('summary');
        }

        //Content
        $article->content = $request->get('content');

        //Options
        //Publicly visible
        if ($request->get('articleVisible')) {
            $article->visible = true;
        } else {
            $article->visible = false;
        }

        //Create and publish if needed
        $article->save();

        return redirect()->Back()->withSuccess('Successfully Edited Article!');
    }

    public function deleteArticle($id)
    {
        $article = News::whereId($id)->firstOrFail();
        AuditLogEntry::insert(Auth::user(), 'Deleted News Article '.$article->id, User::find(1), 0);
        $article->delete();

        return redirect('/admin/news')->withSuccess('Article Deleted!');
    }

    public function viewAllPublic()
    {
        $news = News::where('visible', true)->get()->sortByDesc('id');

        return view('publicnews', compact('news'));
    }

    public function minutesIndex()
    {
        $minutes = MeetingMinutes::all();

        return view('dashboard.news.meetingminutes', compact('minutes'));
    }

    public function minutesDelete($id)
    {
        $minutes = MeetingMinutes::whereId($id)->firstOrFail();
        AuditLogEntry::insert(Auth::user(), 'Deleted meeting minutes '.$minutes->title, User::find(1), 0);
        $minutes->delete();

        return redirect()->back()->with('info', 'Deleted item');
    }

    public function minutesUpload(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'file' => 'required',
        ]);

        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();

        Storage::disk('local')->putFileAs(
            'public/files/minutes', $file, $fileName
        );

        $minutes = new MeetingMinutes([
            'user_id' => Auth::id(),
            'title' => $request->get('title'),
            'link' => Storage::url('public/files/minutes/'.$fileName),
        ]);

        $minutes->save();

        AuditLogEntry::insert(Auth::user(), 'Uploaded meeting minutes '.$minutes->title, User::find(1), 0);

        return redirect()->back()->with('success', 'Minutes uploaded!');
    }
}
