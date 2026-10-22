<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Feedback\ControllerFeedback;
use App\Models\Feedback\EventFeedback;
use App\Models\Feedback\WebsiteFeedback;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\Users\User;
use App\Notifications\Feedback\NewControllerFeedback;
use App\Notifications\Feedback\NewEventFeedback;
use App\Notifications\Feedback\NewWebsiteFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $controller_feedback = ControllerFeedback::with(['user', 'controller'])->get()->sortByDesc('created_at');
        $event_feedback = EventFeedback::with('user')->get()->sortByDesc('created_at');
        $website_feedback = WebsiteFeedback::with('user')->get()->sortByDesc('created_at');
        $controller_feedback_attention = ControllerFeedback::where('approval', 0)->get();

        return view('feedback.index', compact('controller_feedback', 'event_feedback', 'website_feedback', 'controller_feedback_attention'));
    }

    public function yourFeedback(): View
    {
        $feedback = ControllerFeedback::with('controller')->where('approval', 2)->orderByDesc('updated_at')->get();

        return view('feedback.yourfeedback', compact('feedback'));
    }

    public function viewControllerFeedback($id): View
    {
        $submitter = User::where('id', ControllerFeedback::where('id', $id)->firstOrFail()->user_id)->firstOrFail();
        $controller = User::where('id', ControllerFeedback::where('id', $id)->firstOrFail()->controller_cid)->firstOrFail();
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        return view('feedback.controllerview', compact('id', 'submitter', 'controller', 'feedback'));
    }

    public function approveControllerFeedback($id): RedirectResponse
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        if ($feedback->approval == 2) {
            return redirect()->back()->withErrors('Feedback ID#'.$id.' has already been approved!');
        }

        $feedback->approval = 2;
        $feedback->save();

        return redirect()->back()->withSuccess('Feedback ID#'.$id.' has been approved!');
    }

    public function denyControllerFeedback($id): RedirectResponse
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();

        if ($feedback->approval == 1) {
            return redirect()->back()->withErrors('Controller Feedback ID#'.$id.' has already been denied!');
        }

        $feedback->approval = 1;
        $feedback->save();

        return redirect()->back()->withSuccess('Controller Feedback ID#'.$id.' has been denied!');
    }

    public function editControllerFeedback(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate(['content' => ['required', 'string', 'max:20000']]);
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();
        $feedback->content = $validated['content'];
        $feedback->save();

        $log = new AuditLogEntry;
        $log->user_id = Auth::user()->id;
        $log->affected_id = $feedback->controller_cid;
        $log->action = 'Edited Controller Feedback ID#'.$id;
        $log->time = date('Y-m-d H:i:s');
        $log->private = 0;
        $log->save();

        return redirect()->back()->withSuccess('Controller Feedback ID#'.$feedback->id.' has been edited!');
    }

    public function deleteControllerFeedback($id): RedirectResponse
    {
        $feedback = ControllerFeedback::where('id', $id)->firstOrFail();
        $feedback->delete();

        return redirect()->to(route('staff.feedback.index'))->withSuccess('Controller Feedback ID#'.$id.' has been deleted!');
    }

    public function viewEventFeedback($id): View
    {
        $submitter = User::where('id', EventFeedback::where('id', $id)->firstOrFail()->user_id)->firstOrFail();
        $feedback = EventFeedback::where('id', $id)->firstOrFail();

        return view('feedback.eventview', compact('id', 'submitter', 'feedback'));
    }

    public function deleteEventFeedback($id): RedirectResponse
    {
        $feedback = EventFeedback::where('id', $id)->firstOrFail();
        $feedback->delete();

        return redirect()->to(route('staff.feedback.index'))->withSuccess('Event Feedback ID#'.$id.' has been deleted!');
    }

    public function viewWebsiteFeedback($id): View
    {
        $submitter = User::where('id', WebsiteFeedback::where('id', $id)->firstOrFail()->user_id)->firstOrFail();
        $feedback = WebsiteFeedback::where('id', $id)->firstOrFail();

        return view('feedback.websiteview', compact('id', 'submitter', 'feedback'));
    }

    public function deleteWebsiteFeedback($id): RedirectResponse
    {
        $feedback = WebsiteFeedback::where('id', $id)->firstOrFail();
        $feedback->delete();

        return redirect()->to(route('staff.feedback.index'))->withSuccess('Website Feedback ID#'.$id.' has been deleted!');
    }

    public function create(): View
    {
        $controllers = RosterMember::all()->sortBy('cid');

        return view('feedback.create', compact('controllers'));
    }

    public function createPost(Request $request): View|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'feedbackType' => ['required', 'in:controller,event,website'],
            'content' => ['required', 'string', 'max:20000'],
            'subject' => ['nullable', 'required_unless:feedbackType,controller', 'string', 'max:255'],
            'controllerCid' => ['nullable', 'required_if:feedbackType,controller', 'integer', 'exists:roster,cid'],
            'position' => ['nullable', 'required_if:feedbackType,controller', 'string', 'max:100'],
        ], [
            'feedbackType.required' => 'You need to select a type of feedback!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator, 'createFeedbackErrors');
        }

        switch ($request->get('feedbackType')) {
            case 'website':
                $feedback = new WebsiteFeedback([
                    'user_id' => Auth::id(),
                    'subject' => $request->get('subject'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emailwebmaster)->notify(new NewWebsiteFeedback($feedback));
                break;
            case 'event':
                $feedback = new EventFeedback([
                    'user_id' => Auth::id(),
                    'subject' => $request->get('subject'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emaileventc)->notify(new NewEventFeedback($feedback));
                break;
            case 'controller':
                $feedback = new ControllerFeedback([
                    'user_id' => Auth::id(),
                    'controller_cid' => $request->get('controllerCid'),
                    'position' => $request->get('position'),
                    'content' => $request->get('content'),
                ]);
                $feedback->save();
                Notification::route('mail', CoreSettings::find(1)->emailfirchief)->notify(new NewControllerFeedback($feedback));
                Notification::route('mail', CoreSettings::find(1)->emaildepfirchief)->notify(new NewControllerFeedback($feedback));
                break;
        }

        return view('feedback.sent');
    }
}
