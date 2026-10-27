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

// Booking subdomain
Route::domain('booking.czvr.ca')->group(function () {
    Route::get('/', [BookingController::class, 'indexPublic'])->name('booking');

    Route::middleware(['auth', 'booking_certified'])->group(function () {
        Route::post('/', [BookingController::class, 'create'])->name('booking.create');
    });

    // Own bookings (and anyone's with bookings.manage) are checked in the controller
    Route::middleware('auth')->group(function () {
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('booking.update');
        Route::delete('/{id}', [BookingController::class, 'delete'])->name('booking.delete');
    });
});

// Training subdomain
Route::domain('training.czvr.ca')->group(function () {
    Route::get('/', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/stats', [TrainingStatsController::class, 'index'])->middleware(['auth', 'can:training.stats'])->name('training.stats');
    Route::post('/trainingtimes', [TrainingController::class, 'editTrainingTime'])->middleware(['auth', 'can:training.administer'])->name('waittime.edit');
    Route::get('/atcresources', [AtcResourcesController::class, 'index'])->middleware(['auth', 'can:atc-resources.manage'])->name('atcresources.index');
    Route::get('/resources', [TrainingController::class, 'viewResources'])->middleware(['auth', 'certified'])->name('training.resources');

    // Instructors page
    Route::middleware(['auth', 'can:training.instruct'])->group(function () {
        Route::get('/instructors', [TrainingController::class, 'instructorsIndex'])->name('training.instructors');
    });

    // Instructing sessions
    Route::middleware(['auth', 'can:instructing-sessions.manage'])->group(function () {
        Route::get('/instructingsessions', [InstructingSessionsController::class, 'index'])->name('training.instructingsessions.index');
        Route::get('/instructingsessions/new', [InstructingSessionsController::class, 'createForm'])->name('training.instructingsessions.new');
        Route::post('/instructingsessions', [InstructingSessionsController::class, 'create'])->name('training.instructingsessions.create');
        Route::get('/instructingsessions/{session}', [InstructingSessionsController::class, 'show'])->name('training.instructingsessions.viewsession');
        Route::get('/instructingsessions/{session}/edit', [InstructingSessionsController::class, 'edit'])->name('training.instructingsessions.edit');
        Route::put('/instructingsessions/{session}', [InstructingSessionsController::class, 'update'])->name('training.instructingsessions.update');
        Route::delete('/instructingsessions/{session}', [InstructingSessionsController::class, 'cancel'])->name('training.instructingsessions.cancel');
    });

    // Students (Access Training Centre)
    Route::middleware(['auth', 'can:training.manage'])->group(function () {
        // Labels
        Route::post('/students/{id}/assign/label', [LabelController::class, 'assignLabel'])->name('training.students.assign.label');
        Route::delete('/students/{id}/drop/label/{student_label_id}', [LabelController::class, 'dropLabel'])->name('training.students.drop.label');

        // Student lists and pages
        Route::post('/students/{student}/complete', [TrainingController::class, 'completeTraining'])->name('training.students.completeTraining');
        Route::get('/allstudents', [TrainingController::class, 'AllStudents'])->name('training.students.students');
        Route::post('/add', [TrainingController::class, 'newStudent'])->name('instructor.student.add.new');
        Route::get('/completed', [TrainingController::class, 'completedStudents'])->name('training.students.completed');
        Route::get('/waitlist', [TrainingController::class, 'newStudents'])->name('training.students.waitlist');
        Route::get('/students/{id}', [TrainingController::class, 'viewStudent'])->name('training.students.view');
        Route::get('/students/{id}/preview', [TrainingController::class, 'previewStudent'])->middleware('can:training.manage')->name('training.students.preview');
        Route::post('/students/{id}/assigninstructor', [TrainingController::class, 'assignInstructorToStudent'])->name('training.students.assigninstructor');

        // VATCAN training notes
        Route::post('/students/{student}/vatcan-notes', [VatcanController::class, 'storeNote'])->name('training.students.vatcan.notes.store');
        Route::patch('/students/{student}/vatcan-notes/{note}', [VatcanController::class, 'updateNote'])->whereNumber('note')->name('training.students.vatcan.notes.update');

        // Staff comments
        Route::post('/trainingnotes/add/{id}', [TrainingController::class, 'addNote'])->middleware('can:training.instruct')->name('add.trainingnote');
        Route::get('/trainingnotes/create/{id}', [TrainingController::class, 'newNoteView'])->middleware('can:training.instruct')->name('view.add.note');

        // Waitlist order and contact
        Route::post('/waitlist/sort', [TrainingController::class, 'sort'])->middleware('can:training.waitlist.manage')->name('waitlist.sort');
        Route::post('/visitor-waitlist/sort', [TrainingController::class, 'sortVisitor'])->middleware('can:training.waitlist.manage')->name('visitor.sort');
        Route::patch('/students/{student}/waitlist-contact', [TrainingController::class, 'updateWaitlistContact'])->middleware('can:training.waitlist.manage')->name('training.students.waitlist.contact');

        // Checklists, promotions and recertification
        Route::patch('/students/checklist/{id}/complete', [ChecklistController::class, 'completeItem'])->name('training.students.checklist.complete');
        Route::post('/students/{student}/checklist/complete-multiple', [ChecklistController::class, 'completeMultiple'])->name('training.students.checklist.completeMultiple');
        Route::post('/students/{student}/promote', [ChecklistController::class, 'promoteStudent'])->name('training.students.promote');
        Route::post('/students/{student}/promote-visitor', [ChecklistController::class, 'promoteVisitor'])->name('training.students.promoteVisitor');
        Route::post('/students/{student}/assign-t2', [ChecklistController::class, 'assignT2Checklist'])->name('training.students.assignT2');
        Route::post('/students/{student}/recertification', [ChecklistController::class, 'startRecertification'])->name('training.students.recertification.start');
    });

    Route::delete('/students/delete/{id}', [TrainingController::class, 'removeStudent'])->middleware(['auth', 'can:training.students.delete'])->name('training.students.destroy');
    Route::post('/students/{student}/consolidation/waive', [TrainingController::class, 'waiveConsolidation'])->middleware(['auth', 'can:training.consolidation.override'])->name('training.students.consolidation.waive');

    // Students' own pages
    Route::middleware(['auth', 'student'])->group(function () {
        Route::get('/api/training-notes', [VatcanController::class, 'getVatcanNotes'])->name('vatcan.notes.all');
        Route::get('/renew/{token}', [TrainingController::class, 'showRenewTraining'])->name('training.renew');
        Route::post('/renew/{token}', [TrainingController::class, 'renewTraining'])->name('training.renew.confirm');
        Route::post('/students/{student}/edittimes', [TrainingController::class, 'editTimes'])->name('training.students.editTimes');
    });
});

// Unsubscribe link in subscription emails: signed, so it works without logging in (POST is the one-click unsubscribe)
Route::get('/emails/unsubscribe/{user}', [DataController::class, 'unsubscribeFromEmail'])->middleware('signed')->name('emails.unsubscribe');
Route::post('/emails/unsubscribe/{user}', [DataController::class, 'confirmUnsubscribeFromEmail'])->middleware('signed')->name('emails.unsubscribe.confirm');

// Public pages
Route::get('/', [HomeController::class, 'view'])->name('index');
Route::get('/airports', [AirportController::class, 'index'])->name('airports');
Route::view('/preferredrouting', 'pilots.preferredrouting')->name('preferredrouting');
Route::get('/roster', [RosterController::class, 'showPublic'])->name('roster.public');
Route::get('/roster/{id}', [UserController::class, 'viewProfile']);
Route::get('/roster/{id}/connections', [UserController::class, 'viewConnections']);
Route::get('/join', [TrainingController::class, 'joinvancouver'])->name('join.public');
Route::get('/staff', [StaffListController::class, 'index'])->name('staff');
Route::get('/policies', [PoliciesController::class, 'index'])->name('policies');
Route::get('/meetingminutes', [NewsController::class, 'minutesIndex'])->name('meetingminutes');
Route::view('/privacy', 'publications.privacy')->name('privacy');
Route::get('/yourfeedback', [FeedbackController::class, 'yourFeedback'])->name('yourfeedback');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::redirect('/event-coverage', '/events#eventCoverage');
Route::get('/events/{slug}', [EventController::class, 'viewEvent'])->name('events.view');
Route::view('/about', 'about.index')->name('about');
Route::view('/branding', 'about.branding')->name('branding');
Route::get('/news/{slug}', [NewsController::class, 'viewArticlePublic'])->name('news.articlepublic');
Route::get('/news', [NewsController::class, 'viewAllPublic'])->name('news');
Route::get('/trainingtimes', [TrainingController::class, 'trainingTime'])->name('trainingtimes');
Route::view('/mochi', 'mochi.index')->name('mochi');
Route::view('/pdc', 'pilots.pdc')->name('pdc');
Route::view('/vfr', 'pilots.vfr')->name('vfr');
Route::get('/livemap/data', [LiveMapController::class, 'data'])->name('livemap.data');
Route::view('/editmap', 'events.editmap')->middleware(['auth', 'can:events.manage'])->name('editmap');
Route::get('sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'), ['Content-Type' => 'application/xml']);
});
Route::view('/instructors', 'training.public-instructors')->name('instructors');

// Redirects
Route::get('/github', function () {
    return redirect()->to('https://github.com/vancouverfir/czvr-core');
});

// Authentication
Route::get('/connect/login', [LoginController::class, 'AuthLogin'])->middleware('guest')->name('auth.connect.login');
Route::get('/connect/validate', [LoginController::class, 'validateAuthLogin'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

// Submit feedback
Route::middleware('auth')->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'createPost'])->name('feedback.create.post');
});

// Everything below needs a login; each page adds its own permission
Route::middleware('auth')->group(function () {
    // Privacy policy accept / deny
    Route::patch('/privacyaccept', [UserController::class, 'privacyAccept']);
    Route::delete('/privacydeny', [UserController::class, 'privacyDeny']);

    // Your tickets
    Route::get('/dashboard/tickets', [TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/dashboard/tickets/{id}', [TicketsController::class, 'viewTicket'])->name('tickets.viewticket');
    Route::post('/dashboard/tickets', [TicketsController::class, 'startNewTicket'])->name('tickets.startticket');
    Route::post('/dashboard/tickets/{id}', [TicketsController::class, 'addReplyToTicket'])->name('tickets.reply');

    // Submitted feedback (staff)
    Route::prefix('admin')->middleware('can:feedback.manage')->group(function () {
        Route::get('/feedback', [FeedbackController::class, 'index'])->name('staff.feedback.index');
        Route::get('/feedback/controller/{id}', [FeedbackController::class, 'viewControllerFeedback'])->name('staff.feedback.controller');
        Route::post('/feedback/controller/{id}', [FeedbackController::class, 'editControllerFeedback'])->name('staff.feedback.controller.edit');
        Route::patch('/feedback/controller/{id}/approve', [FeedbackController::class, 'approveControllerFeedback']);
        Route::patch('/feedback/controller/{id}/deny', [FeedbackController::class, 'denyControllerFeedback']);
        Route::delete('/feedback/controller/{id}/delete', [FeedbackController::class, 'deleteControllerFeedback']);
        Route::get('/feedback/event/{id}', [FeedbackController::class, 'viewEventFeedback'])->name('staff.feedback.event');
        Route::delete('/feedback/event/{id}/delete', [FeedbackController::class, 'deleteEventFeedback']);
        Route::get('/feedback/website/{id}', [FeedbackController::class, 'viewWebsiteFeedback'])->name('staff.feedback.website');
        Route::delete('/feedback/website/{id}/delete', [FeedbackController::class, 'deleteWebsiteFeedback']);
    });

    // Closing, reopening and placing tickets on hold (staff)
    Route::prefix('dashboard/tickets')->middleware('can:tickets.manage')->group(function () {
        Route::post('/{id}/close', [TicketsController::class, 'closeTicket'])->name('tickets.closeticket');
        Route::patch('/{id}/hold', [TicketsController::class, 'onholdTicket'])->name('tickets.onholdticket');
        Route::patch('/{id}/open', [TicketsController::class, 'openTicket'])->name('tickets.openticket');
    });

    // All tickets (staff)
    Route::get('/dashboard/staff/tickets', [TicketsController::class, 'staffIndex'])->middleware('can:tickets.manage')->name('tickets.staff');

    // News (staff)
    Route::prefix('admin/news')->middleware('can:news.manage')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('news.index');
        Route::get('/article/create', [NewsController::class, 'createArticle'])->name('news.articles.create');
        Route::post('/article/create', [NewsController::class, 'postArticle'])->name('news.articles.create.post');
        Route::get('/article/{slug}', [NewsController::class, 'viewArticle'])->name('news.articles.view');
        Route::delete('/article/delete/{id}', [NewsController::class, 'deleteArticle'])->name('news.articles.delete');
        Route::post('/article/edit/{id}', [NewsController::class, 'editArticle'])->name('news.articles.edit');
    });

    // Event applications
    Route::post('/dashboard/events/controllerapplications/ajax', [EventController::class, 'controllerApplicationAjaxSubmit'])->name('events.controllerapplication.ajax');
    Route::get('/dashboard/events/view', [EventController::class, 'viewControllers']);

    // Events (staff)
    Route::prefix('admin/events')->middleware('can:events.manage')->group(function () {
        Route::get('/', [EventController::class, 'adminIndex'])->name('events.admin.index');
        Route::get('/create', [EventController::class, 'adminCreateEvent'])->name('events.admin.create');
        Route::post('/create', [EventController::class, 'adminCreateEventPost'])->name('events.admin.create.post');
        Route::post('/{slug}/edit', [EventController::class, 'adminEditEventPost'])->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', [EventController::class, 'adminCreateUpdatePost'])->name('events.admin.update.post');
        Route::get('/{slug}', [EventController::class, 'adminViewEvent'])->name('events.admin.view');
        Route::delete('/{slug}/delete', [EventController::class, 'adminDeleteEvent'])->name('events.admin.delete');
        Route::delete('/{slug}/controllerapps/{cid}/delete', [EventController::class, 'adminDeleteControllerApp'])->name('events.admin.controllerapps.delete');
        Route::delete('/{slug}/updates/{id}/delete', [EventController::class, 'adminDeleteUpdate'])->name('events.admin.update.delete');
        Route::get('/applications/{id}', [EventController::class, 'viewApplications'])->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', [EventController::class, 'confirmController'])->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', [EventController::class, 'addController'])->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', [EventController::class, 'deleteController'])->name('event.deletecontroller');
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

        // Avatar and display name
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

        // Roster (staff)
        Route::middleware('can:roster.manage')->group(function () {
            Route::get('/roster', [RosterController::class, 'index'])->name('roster.index');
            Route::post('/roster/controller/add/', [RosterController::class, 'addController'])->name('roster.addcontroller');
            Route::post('/roster/controller/addv/', [RosterController::class, 'addVisitController'])->name('roster.addvisitcontroller');
            Route::post('/roster/edit/{id}', [RosterController::class, 'editController'])->name('roster.editcontroller');
            Route::get('/roster/edit/{id}', [RosterController::class, 'editControllerForm'])->name('roster.editcontrollerform');
            Route::get('/roster/{id}', [RosterController::class, 'viewController'])->name('roster.viewcontroller');
            Route::delete('/roster/{id}/delete/', [RosterController::class, 'deleteController'])->name('roster.deletecontroller');
        });

        // Email preferences
        Route::get('/emailpref', [DataController::class, 'emailPref'])->name('dashboard.emailpref');
        Route::patch('/emailpref/subscribe', [DataController::class, 'subscribeEmails']);
        Route::patch('/emailpref/unsubscribe', [DataController::class, 'unsubscribeEmails']);
    });

    // Your account
    Route::prefix('dashboard/me')->group(function () {
        Route::post('/editbiography', [UserController::class, 'editBio'])->name('me.editbio');

        // Discord
        Route::get('/discord/link', [UserController::class, 'linkDiscord'])->name('me.discord.link');
        Route::delete('/discord/unlink', [UserController::class, 'unlinkDiscord'])->name('me.discord.unlink');
        Route::get('/discord/link/redirect', [UserController::class, 'linkDiscordRedirect'])->name('me.discord.link.redirect');
        Route::get('/discord/server/join', [UserController::class, 'joinDiscordServerRedirect'])->name('me.discord.join');
        Route::get('/discord/server/join/redirect', [UserController::class, 'joinDiscordServer']);

        Route::get('/preferences', [UserController::class, 'preferences'])->name('me.preferences');

        // Your data (GDPR)
        Route::get('/data', [DataController::class, 'index'])->name('me.data');
        Route::post('/data/export/all', [DataController::class, 'exportAllData'])->name('me.data.export.all');
    });

    // Users (staff)
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'viewAllUsers'])->middleware('can:users.manage')->name('users.viewall');
        Route::post('/search/ajax', [UserController::class, 'searchUsers'])->middleware('can:users.manage')->name('users.search.ajax');
        Route::get('/{id}', [UserController::class, 'adminViewUserProfile'])->middleware('can:users.manage')->name('users.viewprofile');
        Route::put('/{id}/access', [UserController::class, 'updateAccess'])->middleware('can:roles.manage')->name('users.access.update');
        Route::post('/func/avatarchange', [UserController::class, 'changeUsersAvatar'])->middleware('can:users.manage')->name('users.changeusersavatar');
        Route::post('/func/avatarreset', [UserController::class, 'resetUsersAvatar'])->middleware('can:users.manage')->name('users.resetusersavatar');
        Route::post('/func/bioreset', [UserController::class, 'resetUsersBio'])->middleware('can:users.manage')->name('users.resetusersbio');
        Route::delete('/{id}/delete', [UserController::class, 'deleteUser'])->middleware('can:users.manage');
        Route::get('/{id}/edit', [UserController::class, 'editUser'])->middleware('can:users.manage')->name('users.edit.create');
        Route::get('/{id}/email', [UserController::class, 'emailCreate'])->middleware('can:users.manage')->name('users.email.create');
        Route::post('/{id}/email', [UserController::class, 'emailStore'])->middleware('can:users.manage')->name('users.email.store');
    });

    // ATC resources (staff)
    Route::middleware('can:atc-resources.manage')->group(function () {
        Route::post('/atcresources', [AtcResourcesController::class, 'uploadResource'])->name('atcresources.upload');
        Route::put('/atcresources/{resource}', [AtcResourcesController::class, 'updateResource'])->name('atcresources.update');
        Route::delete('/atcresources/delete/{id}', [AtcResourcesController::class, 'deleteResource'])->name('atcresources.delete');
    });

    // Policies (staff)
    Route::middleware('can:policies.manage')->group(function () {
        Route::post('/policies', [PoliciesController::class, 'addPolicy'])->name('policies.create');
        Route::post('/policies/{id}/edit', [PoliciesController::class, 'editPolicy']);
        Route::delete('/policies/{id}/delete', [PoliciesController::class, 'deletePolicy']);
        Route::post('/policies/section/create', [PoliciesController::class, 'addPolicySection'])->name('policysection.create');
        Route::delete('/policies/section/{id}/delete', [PoliciesController::class, 'deletePolicySection']);
    });

    // Meeting minutes (staff)
    Route::middleware('can:meeting-minutes.manage')->group(function () {
        Route::delete('/meetingminutes/{id}', [NewsController::class, 'minutesDelete'])->name('meetingminutes.delete');
        Route::post('/meetingminutes', [NewsController::class, 'minutesUpload'])->name('meetingminutes.upload');
    });

    // Network data (admin)
    Route::prefix('admin/network')->middleware('can:network.manage')->group(function () {
        Route::get('/', [NetworkController::class, 'index'])->name('network.index');
        Route::get('/monitoredpositions.json', [NetworkController::class, 'downloadMonitoredPositions'])->name('network.monitoredpositions.download');
        Route::post('/monitoredpositions', [NetworkController::class, 'createMonitoredPosition'])->name('network.monitoredpositions.create');
        Route::get('/monitoredpositions/{position}', [NetworkController::class, 'viewMonitoredPosition'])->name('network.monitoredpositions.view');
        Route::put('/monitoredpositions/{position}', [NetworkController::class, 'updateMonitoredPosition'])->name('network.monitoredpositions.update');
        Route::delete('/monitoredpositions/{position}', [NetworkController::class, 'deleteMonitoredPosition'])->name('network.monitoredpositions.delete');
    });

    // Settings (admin)
    Route::prefix('admin/settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->middleware('can:settings.access')->name('settings.index');

        // Roles and permissions
        Route::get('/roles', [SettingsController::class, 'viewRoles'])->middleware('can:roles.manage')->name('roles.view');
        Route::post('/addrole', [SettingsController::class, 'addRole'])->middleware('can:roles.manage')->name('roles.add');
        Route::put('/roles/{role}', [SettingsController::class, 'updateRole'])->middleware('can:roles.manage')->name('roles.update');
        Route::delete('/deleterole/{id}', [SettingsController::class, 'deleteRole'])->middleware('can:roles.manage')->name('roles.delete');

        // Site information and staff emails
        Route::get('/site-information', [SettingsController::class, 'siteInformation'])->middleware('can:site-settings.manage')->name('settings.siteinformation');
        Route::post('/site-information', [SettingsController::class, 'saveSiteInformation'])->middleware('can:site-settings.manage')->name('settings.siteinformation.post');
        Route::get('/emails', [SettingsController::class, 'emails'])->middleware('can:site-settings.manage')->name('settings.emails');
        Route::post('/emails', [SettingsController::class, 'saveEmails'])->middleware('can:site-settings.manage')->name('settings.emails.post');

        Route::get('/audit-log', [SettingsController::class, 'auditLog'])->middleware('can:audit-log.access')->name('settings.auditlog');

        // Staff directory
        Route::get('/staff', [StaffListController::class, 'editIndex'])->middleware('can:staff.manage')->name('settings.staff');
        Route::post('/staff/{id}', [StaffListController::class, 'editStaffMember'])->middleware('can:staff.manage')->name('settings.staff.editmember');
        Route::post('/staff/a/add', [StaffListController::class, 'addStaffMember'])->middleware('can:staff.manage')->name('settings.staff.addmember');
        Route::post('/staff/{id}/delete', [StaffListController::class, 'deleteStaffMember'])->middleware('can:staff.manage')->name('settings.staff.deletemember');

        // Banner and homepage images
        Route::get('/banner', [SettingsController::class, 'banner'])->middleware('can:site-settings.manage')->name('settings.banner');
        Route::post('/banner', [SettingsController::class, 'bannerEdit'])->middleware('can:site-settings.manage')->name('settings.banner.edit');
        Route::get('/images', [SettingsController::class, 'imagesIndex'])->middleware('can:site-settings.manage')->name('settings.images');
        Route::post('/images', [SettingsController::class, 'uploadImage'])->middleware('can:site-settings.manage')->name('settings.images.upload');
        Route::post('/images/edit/{id}', [SettingsController::class, 'editImage'])->middleware('can:site-settings.manage')->name('settings.images.edit');
        Route::get('/images/test/{id}', [SettingsController::class, 'testImage'])->middleware('can:site-settings.manage')->name('settings.images.test');
        Route::delete('/images/delete/{id}', [SettingsController::class, 'deleteImage'])->middleware('can:site-settings.manage')->name('settings.images.delete');
    });
});
