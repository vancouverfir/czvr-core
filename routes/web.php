<?php

use App\Http\Controllers\AirportController;
use App\Http\Controllers\AtcTraining\ChecklistController;
use App\Http\Controllers\AtcTraining\InstructingSessionsController;
use App\Http\Controllers\AtcTraining\LabelController;
use App\Http\Controllers\AtcTraining\RosterController;
use App\Http\Controllers\AtcTraining\TrainingController;
use App\Http\Controllers\AtcTraining\TrainingStatsController;
use App\Http\Controllers\AtcTraining\VatcanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveMapController;
use App\Http\Controllers\Network\NetworkController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\Publications\AtcResourcesController;
use App\Http\Controllers\Publications\PoliciesController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Tickets\TicketsController;
use App\Http\Controllers\Users\DataController;
use App\Http\Controllers\Users\StaffListController;
use App\Http\Controllers\Users\UserController;
use App\Models\Users\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Booking Subdomain
Route::domain('booking.czvr.ca')->group(function () {
    Route::get('/', [BookingController::class, 'indexPublic'])->name('booking');

    Route::middleware(['auth', 'booking_certified'])->group(function () {
        Route::post('/', [BookingController::class, 'create'])->name('booking.create');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('booking.update');
        Route::delete('/{id}', [BookingController::class, 'delete'])->name('booking.delete');
    });
});

// Training Subdomain
Route::domain('training.czvr.ca')->group(function () {
    Route::get('/', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/stats', [TrainingStatsController::class, 'index'])->middleware(['auth', 'can:training.stats'])->name('training.stats');
    Route::post('/trainingtimes', [TrainingController::class, 'editTrainingTime'])->middleware(['auth', 'can:training.administer'])->name('waittime.edit');
    Route::get('/atcresources', [AtcResourcesController::class, 'index'])->middleware(['auth', 'can:atc-resources.manage'])->name('atcresources.index');
    Route::get('/resources', [TrainingController::class, 'viewResources'])->middleware(['auth', 'certified'])->name('training.resources');

    // Instructor-only +
    Route::group(['middleware' => ['auth', 'can:training.instruct']], function () {
        Route::get('/instructors', [TrainingController::class, 'instructorsIndex'])->name('training.instructors');
    });

    Route::middleware(['auth', 'can:instructing-sessions.access'])->group(function () {
        Route::get('/instructingsessions', [InstructingSessionsController::class, 'index'])->name('training.instructingsessions.index');
        Route::get('/instructingsessions/new', [InstructingSessionsController::class, 'createForm'])->name('training.instructingsessions.new');
        Route::post('/instructingsessions', [InstructingSessionsController::class, 'create'])->name('training.instructingsessions.create');
        Route::get('/instructingsessions/{session}', [InstructingSessionsController::class, 'show'])->name('training.instructingsessions.viewsession');
        Route::get('/instructingsessions/{session}/edit', [InstructingSessionsController::class, 'edit'])->name('training.instructingsessions.edit');
        Route::put('/instructingsessions/{session}', [InstructingSessionsController::class, 'update'])->name('training.instructingsessions.update');
        Route::delete('/instructingsessions/{session}', [InstructingSessionsController::class, 'cancel'])->name('training.instructingsessions.cancel');
    });

    // Mentor-only +
    Route::group(['middleware' => ['auth', 'can:training.manage']], function () {
        // Labels Edit
        Route::post('/students/{id}/assign/label', [LabelController::class, 'assignLabel'])->name('training.students.assign.label');
        Route::delete('/students/{id}/drop/label/{student_label_id}', [LabelController::class, 'dropLabel'])->name('training.students.drop.label');

        // Students Edit
        Route::post('/students/{student}/complete', [TrainingController::class, 'completeTraining'])->name('training.students.completeTraining');
        Route::get('/allstudents', [TrainingController::class, 'AllStudents'])->name('training.students.students');
        Route::post('/add', [TrainingController::class, 'newStudent'])->name('instructor.student.add.new');
        Route::get('/completed', [TrainingController::class, 'completedStudents'])->name('training.students.completed');
        Route::get('/waitlist', [TrainingController::class, 'newStudents'])->name('training.students.waitlist');
        Route::get('/students/{id}', [TrainingController::class, 'viewStudent'])->name('training.students.view');
        Route::get('/students/{id}/preview', [TrainingController::class, 'previewStudent'])->middleware('can:training.manage')->name('training.students.preview');
        Route::post('/students/{id}/assigninstructor', [TrainingController::class, 'assignInstructorToStudent'])->name('training.students.assigninstructor');
        Route::post('/trainingnotes/add/{id}', [TrainingController::class, 'addNote'])->middleware('can:training.instruct')->name('add.trainingnote');
        Route::get('/trainingnotes/create/{id}', [TrainingController::class, 'newNoteView'])->middleware('can:training.instruct')->name('view.add.note');
        Route::post('/waitlist/sort', [TrainingController::class, 'sort'])->middleware('can:training.instruct')->name('waitlist.sort');
        Route::post('/visitor-waitlist/sort', [TrainingController::class, 'sortVisitor'])->middleware('can:training.instruct')->name('visitor.sort');
        Route::patch('/students/{student}/waitlist-contact', [TrainingController::class, 'updateWaitlistContact'])->name('training.students.waitlist.contact');
        Route::delete('/students/delete/{id}', [TrainingController::class, 'removeStudent'])->middleware('can:training.administer')->name('training.students.destroy');
        Route::patch('/students/checklist/{id}/complete', [ChecklistController::class, 'completeItem'])->name('training.students.checklist.complete');
        Route::post('/students/{student}/checklist/complete-multiple', [ChecklistController::class, 'completeMultiple'])->name('training.students.checklist.completeMultiple');
        Route::post('/students/{student}/promote', [ChecklistController::class, 'promoteStudent'])->name('training.students.promote');
        Route::post('/students/{student}/promote-visitor', [ChecklistController::class, 'promoteVisitor'])->name('training.students.promoteVisitor');
        Route::post('/students/{student}/assign-t2', [ChecklistController::class, 'assignT2Checklist'])->name('training.students.assignT2');
        Route::post('/students/{student}/recertification', [ChecklistController::class, 'startRecertification'])->name('training.students.recertification.start');
    });

    Route::post('/students/{student}/consolidation/waive', [TrainingController::class, 'waiveConsolidation'])
        ->middleware(['auth', 'can:training.consolidation.override'])
        ->name('training.students.consolidation.waive');

    // Student-only +
    Route::group(['middleware' => ['auth', 'student']], function () {
        Route::get('/api/training-notes', [VatcanController::class, 'getVatcanNotes'])->name('vatcan.notes.all');
        Route::get('/renew/{token}', [TrainingController::class, 'showRenewTraining'])->name('training.renew');
        Route::post('/renew/{token}', [TrainingController::class, 'renewTraining'])->name('training.renew.confirm');
        Route::post('/students/{student}/edittimes', [TrainingController::class, 'editTimes'])->name('training.students.editTimes');
    });
});

// Unsubscribe link in subscription emails; signed, so it works without logging in (POST is the one-click unsubscribe)
Route::get('/emails/unsubscribe/{user}', [DataController::class, 'unsubscribeFromEmail'])->middleware('signed')->name('emails.unsubscribe');
Route::post('/emails/unsubscribe/{user}', [DataController::class, 'confirmUnsubscribeFromEmail'])->middleware('signed')->name('emails.unsubscribe.confirm');

// ALL Public Views
Route::get('/', [HomeController::class, 'view'])->name('index');
Route::get('/airports', [AirportController::class, 'index'])->name('airports');
Route::view('/preferredrouting', 'preferredrouting')->name('preferredrouting');
Route::get('/roster', [RosterController::class, 'showPublic'])->name('roster.public');
Route::get('/roster/{id}', [UserController::class, 'viewProfile']);
Route::get('/roster/{id}/connections', [UserController::class, 'viewConnections']);
Route::get('/join', [TrainingController::class, 'joinvancouver'])->name('join.public');
Route::get('/staff', [StaffListController::class, 'index'])->name('staff');
Route::get('/policies', [PoliciesController::class, 'index'])->name('policies');
Route::get('/meetingminutes', [NewsController::class, 'minutesIndex'])->name('meetingminutes');
Route::view('/privacy', 'privacy')->name('privacy');
Route::get('/yourfeedback', [FeedbackController::class, 'yourFeedback'])->name('yourfeedback');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::redirect('/event-coverage', '/events#eventCoverage');
Route::get('/events/{slug}', [EventController::class, 'viewEvent'])->name('events.view');
Route::view('/about', 'about')->name('about');
Route::view('/branding', 'branding')->name('branding');
Route::get('/news/{slug}', [NewsController::class, 'viewArticlePublic'])->name('news.articlepublic');
Route::get('/news', [NewsController::class, 'viewAllPublic'])->name('news');
Route::get('/trainingtimes', [TrainingController::class, 'trainingTime'])->name('trainingtimes');
Route::view('/mochi', 'mochi')->name('mochi');
Route::view('/pdc', 'pdc')->name('pdc');
Route::view('/vfr', 'vfr')->name('vfr');
Route::get('/livemap/data', [LiveMapController::class, 'data'])->name('livemap.data');
Route::view('/editmap', 'editmap')->middleware(['auth', 'can:events.manage'])->name('editmap');
Route::get('sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'), ['Content-Type' => 'application/xml']);
});

Route::prefix('instructors')->group(function () {
    Route::view('/', 'instructors')->name('instructors');
});

// Redirects

Route::get('/github', function () {
    return redirect()->to('https://github.com/vancouverfir/czvr-core');
});

// Authentication

Route::get('/connect/login', [LoginController::class, 'AuthLogin'])->middleware('guest')->name('auth.connect.login');
Route::get('/connect/validate', [LoginController::class, 'validateAuthLogin'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

// Feedback

Route::middleware(['auth'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'createPost'])->name('feedback.create.post');
});

// Base level authentication
Route::group(['middleware' => 'auth'], function () {
    // Privacy accept
    Route::patch('/privacyaccept', [UserController::class, 'privacyAccept']);
    Route::delete('/privacydeny', [UserController::class, 'privacyDeny']);

    // User Tickets
    Route::get('/dashboard/tickets', [TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/dashboard/tickets/{id}', [TicketsController::class, 'viewTicket'])->name('tickets.viewticket');
    Route::post('/dashboard/tickets', [TicketsController::class, 'startNewTicket'])->name('tickets.startticket');
    Route::post('/dashboard/tickets/{id}', [TicketsController::class, 'addReplyToTicket'])->name('tickets.reply');

    Route::group([], function () {
        Route::prefix('admin')->group(function () {
            // View Feedback
            Route::get('/feedback', [FeedbackController::class, 'index'])->middleware('can:feedback.manage')->name('staff.feedback.index');
            Route::get('/feedback/controller/{id}', [FeedbackController::class, 'viewControllerFeedback'])->middleware('can:feedback.manage')->name('staff.feedback.controller');
            Route::post('/feedback/controller/{id}', [FeedbackController::class, 'editControllerFeedback'])->middleware('can:feedback.manage')->name('staff.feedback.controller.edit');
            Route::patch('/feedback/controller/{id}/approve', [FeedbackController::class, 'approveControllerFeedback'])->middleware('can:feedback.manage');
            Route::patch('/feedback/controller/{id}/deny', [FeedbackController::class, 'denyControllerFeedback'])->middleware('can:feedback.manage');
            Route::delete('/feedback/controller/{id}/delete', [FeedbackController::class, 'deleteControllerFeedback'])->middleware('can:feedback.manage');
            Route::get('/feedback/event/{id}', [FeedbackController::class, 'viewEventFeedback'])->middleware('can:feedback.manage')->name('staff.feedback.event');
            Route::delete('/feedback/event/{id}/delete', [FeedbackController::class, 'deleteEventFeedback'])->middleware('can:feedback.manage');
            Route::get('/feedback/website/{id}', [FeedbackController::class, 'viewWebsiteFeedback'])->middleware('can:feedback.manage')->name('staff.feedback.website');
            Route::delete('/feedback/website/{id}/delete', [FeedbackController::class, 'deleteWebsiteFeedback'])->middleware('can:feedback.manage');
        });

        // Closing, re-opening, and placing tickets on hold
        Route::prefix('dashboard/tickets')->middleware('can:tickets.manage')->group(function () {
            Route::post('/{id}/close', [TicketsController::class, 'closeTicket'])->name('tickets.closeticket');
            Route::patch('/{id}/hold', [TicketsController::class, 'onholdTicket'])->name('tickets.onholdticket');
            Route::patch('/{id}/open', [TicketsController::class, 'openTicket'])->name('tickets.openticket');
        });

        // View Tickets (staff)
        Route::get('/dashboard/staff/tickets', [TicketsController::class, 'staffIndex'])->middleware('can:tickets.manage')->name('tickets.staff');

        // Staff News
        Route::prefix('admin/news')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->middleware('can:news.manage')->name('news.index');
            Route::get('/article/create', [NewsController::class, 'createArticle'])->middleware('can:news.manage')->name('news.articles.create');
            Route::post('/article/create', [NewsController::class, 'postArticle'])->middleware('can:news.manage')->name('news.articles.create.post');
            Route::get('/article/{slug}', [NewsController::class, 'viewArticle'])->middleware('can:news.manage')->name('news.articles.view');
            Route::delete('/article/delete/{id}', [NewsController::class, 'deleteArticle'])->middleware('can:news.manage')->name('news.articles.delete');
            Route::post('/article/edit/{id}', [NewsController::class, 'editArticle'])->middleware('can:news.manage')->name('news.articles.edit');
        });
    });

    // User Event Applications
    Route::post('/dashboard/events/controllerapplications/ajax', [EventController::class, 'controllerApplicationAjaxSubmit'])->name('events.controllerapplication.ajax');
    Route::get('/dashboard/events/view', [EventController::class, 'viewControllers']);

    // Staff Events
    Route::group(['prefix' => 'admin/events'], function () {
        Route::get('/', [EventController::class, 'adminIndex'])->middleware('can:events.manage')->name('events.admin.index');
        Route::get('/create', [EventController::class, 'adminCreateEvent'])->middleware('can:events.manage')->name('events.admin.create');
        Route::post('/create', [EventController::class, 'adminCreateEventPost'])->middleware('can:events.manage')->name('events.admin.create.post');
        Route::post('/{slug}/edit', [EventController::class, 'adminEditEventPost'])->middleware('can:events.manage')->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', [EventController::class, 'adminCreateUpdatePost'])->middleware('can:events.manage')->name('events.admin.update.post');
        Route::get('/{slug}', [EventController::class, 'adminViewEvent'])->middleware('can:events.manage')->name('events.admin.view');
        Route::delete('/{slug}/delete', [EventController::class, 'adminDeleteEvent'])->middleware('can:events.manage')->name('events.admin.delete');
        Route::delete('/{slug}/controllerapps/{cid}/delete', [EventController::class, 'adminDeleteControllerApp'])->middleware('can:events.manage')->name('events.admin.controllerapps.delete');
        Route::delete('/{slug}/updates/{id}/delete', [EventController::class, 'adminDeleteUpdate'])->middleware('can:events.manage')->name('events.admin.update.delete');
        Route::get('/applications/{id}', [EventController::class, 'viewApplications'])->middleware('can:events.manage')->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', [EventController::class, 'confirmController'])->middleware('can:events.manage')->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', [EventController::class, 'addController'])->middleware('can:events.manage')->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', [EventController::class, 'deleteController'])->middleware('can:events.manage')->name('event.deletecontroller');
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::post('/users/changeavatar', [UserController::class, 'changeAvatar'])->name('users.changeavatar');
        Route::patch('/users/changeavatar/discord', [UserController::class, 'changeAvatarDiscord'])->name('users.changeavatar.discord');
        Route::patch('/users/resetavatar', [UserController::class, 'resetAvatar'])->name('users.resetavatar');
        Route::post('/users/changedisplayname', [UserController::class, 'changeDisplayName'])->name('users.changedisplayname');
        Route::get('/users/defaultavatar/{id}', function ($id) {
            $user = User::whereId($id)->firstOrFail();
            if ($user->isAvatarDefault()) {
                return true;
            }

            return false;
        });

        // Roster
        Route::group([], function () {
            Route::get('/roster', [RosterController::class, 'index'])->middleware('can:roster.manage')->name('roster.index');
            Route::post('/roster/controller/add/', [RosterController::class, 'addController'])->middleware('can:roster.manage')->name('roster.addcontroller');
            Route::post('/roster/controller/addv/', [RosterController::class, 'addVisitController'])->middleware('can:roster.manage')->name('roster.addvisitcontroller');
            Route::post('/roster/edit/{id}', [RosterController::class, 'editController'])->middleware('can:roster.manage')->name('roster.editcontroller');
            Route::get('/roster/edit/{id}', [RosterController::class, 'editControllerForm'])->middleware('can:roster.manage')->name('roster.editcontrollerform');
            Route::get('/roster/{id}', [RosterController::class, 'viewController'])->middleware('can:roster.manage')->name('roster.viewcontroller');
            Route::delete('/roster/{id}/delete/', [RosterController::class, 'deleteController'])->middleware('can:roster.manage')->name('roster.deletecontroller');
        });

        // Email prefs
        Route::get('/emailpref', [DataController::class, 'emailPref'])->name('dashboard.emailpref');
        Route::patch('/emailpref/subscribe', [DataController::class, 'subscribeEmails']);
        Route::patch('/emailpref/unsubscribe', [DataController::class, 'unsubscribeEmails']);
    });
    // '/me'
    Route::prefix('dashboard/me')->group(function () {
        Route::post('/editbiography', [UserController::class, 'editBio'])->name('me.editbio');
        Route::get('/discord/link', [UserController::class, 'linkDiscord'])->name('me.discord.link');
        Route::delete('/discord/unlink', [UserController::class, 'unlinkDiscord'])->name('me.discord.unlink');
        Route::get('/discord/link/redirect', [UserController::class, 'linkDiscordRedirect'])->name('me.discord.link.redirect');
        Route::get('/discord/server/join', [UserController::class, 'joinDiscordServerRedirect'])->name('me.discord.join');
        Route::get('/discord/server/join/redirect', [UserController::class, 'joinDiscordServer']);
        Route::get('/preferences', [UserController::class, 'preferences'])->name('me.preferences');
        // GDPR
        Route::get('/data', [DataController::class, 'index'])->name('me.data');
        Route::post('/data/export/all', [DataController::class, 'exportAllData'])->name('me.data.export.all');
    });

    // Users View/Edit
    Route::group([], function () {
        Route::prefix('admin/users')->Group(function () {
            Route::get('/', [UserController::class, 'viewAllUsers'])->middleware('can:users.manage')->name('users.viewall');
            Route::post('/search/ajax', [UserController::class, 'searchUsers'])->middleware('can:users.manage')->name('users.search.ajax');
            Route::get('{id}', [UserController::class, 'adminViewUserProfile'])->middleware('can:users.manage')->name('users.viewprofile');
            Route::put('/{id}/access', [UserController::class, 'updateAccess'])->middleware('can:roles.manage')->name('users.access.update');
            Route::post('/{id}', [UserController::class, 'createUserNote'])->middleware('can:users.manage')->name('users.createnote');
            Route::delete('/{user_id}/note/{note_id}/delete', [UserController::class, 'deleteUserNote'])->middleware('can:users.manage')->name('users.deletenote');
            Route::post('/func/avatarchange', [UserController::class, 'changeUsersAvatar'])->middleware('can:users.manage')->name('users.changeusersavatar');
            Route::post('/func/avatarreset', [UserController::class, 'resetUsersAvatar'])->middleware('can:users.manage')->name('users.resetusersavatar');
            Route::post('/func/bioreset', [UserController::class, 'resetUsersBio'])->middleware('can:users.manage')->name('users.resetusersbio');
            Route::delete('/{id}/delete', [UserController::class, 'deleteUser'])->middleware('can:users.manage');
            Route::get('/{id}/edit', [UserController::class, 'editUser'])->middleware('can:users.manage')->name('users.edit.create');
            Route::get('/{id}/email', [UserController::class, 'emailCreate'])->middleware('can:users.manage')->name('users.email.create');
            Route::post('/{id}/email', [UserController::class, 'emailStore'])->middleware('can:users.manage')->name('users.email.store');
        });
    });

    Route::group([], function () {
        // Upload and Delete ATC Resources
        Route::post('/atcresources', [AtcResourcesController::class, 'uploadResource'])->middleware('can:atc-resources.manage')->name('atcresources.upload');
        Route::put('/atcresources/{resource}', [AtcResourcesController::class, 'updateResource'])->middleware('can:atc-resources.manage')->name('atcresources.update');
        Route::delete('/atcresources/delete/{id}', [AtcResourcesController::class, 'deleteResource'])->middleware('can:atc-resources.manage')->name('atcresources.delete');
        // Policy creation and settings
        Route::post('/policies', [PoliciesController::class, 'addPolicy'])->middleware('can:policies.manage')->name('policies.create');
        Route::post('/policies/{id}/edit', [PoliciesController::class, 'editPolicy'])->middleware('can:policies.manage');
        Route::delete('/policies/{id}/delete', [PoliciesController::class, 'deletePolicy'])->middleware('can:policies.manage');
        Route::post('/policies/section/create', [PoliciesController::class, 'addPolicySection'])->middleware('can:policies.manage')->name('policysection.create');
        Route::delete('/policies/section/{id}/delete', [PoliciesController::class, 'deletePolicySection'])->middleware('can:policies.manage');
    });

    // ADMIN ONLY
    // Minutes
    Route::group([], function () {
        Route::delete('/meetingminutes/{id}', [NewsController::class, 'minutesDelete'])->middleware('can:meeting-minutes.manage')->name('meetingminutes.delete');
        Route::post('/meetingminutes', [NewsController::class, 'minutesUpload'])->middleware('can:meeting-minutes.manage')->name('meetingminutes.upload');
        // Network
        Route::get('/admin/network', [NetworkController::class, 'index'])->middleware('can:network.manage')->name('network.index');
        Route::get('/admin/network/monitoredpositions.json', [NetworkController::class, 'downloadMonitoredPositions'])->middleware('can:network.manage')->name('network.monitoredpositions.download');
        Route::post('/admin/network/monitoredpositions', [NetworkController::class, 'createMonitoredPosition'])->middleware('can:network.manage')->name('network.monitoredpositions.create');
        Route::get('/admin/network/monitoredpositions/{position}', [NetworkController::class, 'viewMonitoredPosition'])->middleware('can:network.manage')->name('network.monitoredpositions.view');
        Route::put('/admin/network/monitoredpositions/{position}', [NetworkController::class, 'updateMonitoredPosition'])->middleware('can:network.manage')->name('network.monitoredpositions.update');
        Route::delete('/admin/network/monitoredpositions/{position}', [NetworkController::class, 'deleteMonitoredPosition'])->middleware('can:network.manage')->name('network.monitoredpositions.delete');

        // Settings
        Route::prefix('admin/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->middleware('can:settings.access')->name('settings.index');
            Route::get('/roles', [SettingsController::class, 'viewRoles'])->middleware('can:roles.manage')->name('roles.view');
            Route::post('/addrole', [SettingsController::class, 'addRole'])->middleware('can:roles.manage')->name('roles.add');
            Route::put('/roles/{role}', [SettingsController::class, 'updateRole'])->middleware('can:roles.manage')->name('roles.update');
            Route::delete('/deleterole/{id}', [SettingsController::class, 'deleteRole'])->middleware('can:roles.manage')->name('roles.delete');
            Route::get('/site-information', [SettingsController::class, 'siteInformation'])->middleware('can:site-settings.manage')->name('settings.siteinformation');
            Route::post('/site-information', [SettingsController::class, 'saveSiteInformation'])->middleware('can:site-settings.manage')->name('settings.siteinformation.post');
            Route::get('/emails', [SettingsController::class, 'emails'])->middleware('can:site-settings.manage')->name('settings.emails');
            Route::post('/emails', [SettingsController::class, 'saveEmails'])->middleware('can:site-settings.manage')->name('settings.emails.post');
            Route::get('/audit-log', [SettingsController::class, 'auditLog'])->middleware('can:audit-log.access')->name('settings.auditlog');
            Route::get('/staff', [StaffListController::class, 'editIndex'])->middleware('can:staff.manage')->name('settings.staff');
            Route::post('/staff/{id}', [StaffListController::class, 'editStaffMember'])->middleware('can:staff.manage')->name('settings.staff.editmember');
            Route::post('/staff/a/add', [StaffListController::class, 'addStaffMember'])->middleware('can:staff.manage')->name('settings.staff.addmember');
            Route::post('/staff/{id}/delete', [StaffListController::class, 'deleteStaffMember'])->middleware('can:staff.manage')->name('settings.staff.deletemember');
            Route::get('/banner', [SettingsController::class, 'banner'])->middleware('can:site-settings.manage')->name('settings.banner');
            Route::post('/banner', [SettingsController::class, 'bannerEdit'])->middleware('can:site-settings.manage')->name('settings.banner.edit');
            Route::get('/images', [SettingsController::class, 'imagesIndex'])->middleware('can:site-settings.manage')->name('settings.images');
            Route::post('images', [SettingsController::class, 'uploadImage'])->middleware('can:site-settings.manage')->name('settings.images.upload');
            Route::post('/images/edit/{id}', [SettingsController::class, 'editImage'])->middleware('can:site-settings.manage')->name('settings.images.edit');
            Route::get('/images/test/{id}', [SettingsController::class, 'testImage'])->middleware('can:site-settings.manage')->name('settings.images.test');
            Route::delete('/images/delete/{id}', [SettingsController::class, 'deleteImage'])->middleware('can:site-settings.manage')->name('settings.images.delete');
        });
    });
});
