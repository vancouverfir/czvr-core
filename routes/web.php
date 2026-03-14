<?php

use App\Http\Controllers\AtcTraining\ChecklistController;
use App\Http\Controllers\AtcTraining\InstructingSessionsController;
use App\Http\Controllers\AtcTraining\LabelController;
use App\Http\Controllers\AtcTraining\RosterController;
use App\Http\Controllers\AtcTraining\TeachersController;
use App\Http\Controllers\AtcTraining\TrainingController;
use App\Http\Controllers\AtcTraining\VatcanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Events\EventController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Network\NetworkController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\Publications\AtcResourcesController;
use App\Http\Controllers\Publications\PoliciesController;
use App\Http\Controllers\Publications\UploadController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Tickets\TicketsController;
use App\Http\Controllers\Users\DataController;
use App\Http\Controllers\Users\StaffListController;
use App\Http\Controllers\Users\UserController;
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
Route::group(['domain' => 'booking.czvr.ca'], function () {
    // Public Booking
    Route::get('/', [BookingController::class, 'indexPublic'])->name('booking');

    // Protected Create Booking
    Route::group(['middleware' => ['auth', 'booking_certified']], function () {
        Route::post('/', [BookingController::class, 'create'])->name('booking.create');
    });

    // Protected Booking
    Route::group(['middleware' => ['auth', 'certified']], function () {
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('booking.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('booking.update');
        Route::delete('/{id}', [BookingController::class, 'delete'])->name('booking.delete');
    });
});

// Training Subdomain
Route::group(['domain' => 'training.czvr.ca'], function () {
    // Index
    Route::get('/', [TrainingController::class, 'index'])->name('training.index');
    Route::post('/trainingtimes', [TrainingController::class, 'editTrainingTime'])->middleware('staff')->name('waittime.edit');

    // Instructor-only +
    Route::group(['middleware' => ['auth', 'instructor']], function () {
        // Atc Resources Edit
        Route::get('/atcresources', [AtcResourcesController::class, 'index'])->middleware('atc')->name('atcresources.index');

        Route::get('/instructors', [TrainingController::class, 'instructorsIndex'])->name('training.instructors');
        Route::post('/instructors', [TrainingController::class, 'addInstructor'])->name('training.instructors.add');
    });

    // Mentor-only +
    Route::group(['middleware' => ['auth', 'mentor']], function () {
        // Atc Resources Edit
        Route::get('/atcresources', [AtcResourcesController::class, 'index'])->middleware('atc')->name('atcresources.index');

        // Instructing Session
        Route::get('/instructingsessions', [InstructingSessionsController::class, 'index'])->name('training.instructingsessions.index');
        Route::get('/instructingsessions/new', [InstructingSessionsController::class, 'createForm'])->name('training.instructingsessions.new');
        Route::post('/instructingsessions', [InstructingSessionsController::class, 'create'])->name('training.instructingsessions.create');
        Route::get('/instructingsessions/{session}', [InstructingSessionsController::class, 'show'])->name('training.instructingsessions.viewsession');
        Route::get('/instructingsessions/{session}/edit', [InstructingSessionsController::class, 'edit'])->name('training.instructingsessions.edit');
        Route::put('/instructingsessions/{session}', [InstructingSessionsController::class, 'update'])->name('training.instructingsessions.update');
        Route::delete('/instructingsessions/{session}', [InstructingSessionsController::class, 'cancel'])->name('training.instructingsessions.cancel');

        // Labels Edit
        Route::post('/students/{id}/assign/label', [LabelController::class, 'assignLabel'])->name('training.students.assign.label');
        Route::get('/students/{id}/drop/label/{student_label_id}', [LabelController::class, 'dropLabel'])->name('training.students.drop.label');

        // Students Edit
        Route::post('/students/{student}/complete', [TrainingController::class, 'completeTraining'])->name('training.students.completeTraining');
        Route::get('/allstudents', [TrainingController::class, 'AllStudents'])->name('training.students.students');
        Route::post('/add', [TrainingController::class, 'newStudent'])->name('instructor.student.add.new');
        Route::get('/completed', [TrainingController::class, 'completedStudents'])->name('training.students.completed');
        Route::get('/waitlist', [TrainingController::class, 'newStudents'])->name('training.students.waitlist');
        Route::get('/students/{id}', [TrainingController::class, 'viewStudent'])->name('training.students.view');
        Route::post('/students/{id}/assigninstructor', [TrainingController::class, 'assignInstructorToStudent'])->name('training.students.assigninstructor');
        Route::post('/trainingnotes/add/{id}', [TrainingController::class, 'addNote'])->name('add.trainingnote');
        Route::get('/trainingnotes/create/{id}', [TrainingController::class, 'newNoteView'])->name('view.add.note');
        Route::post('/waitlist/sort', [TrainingController::class, 'sort'])->name('waitlist.sort');
        Route::post('/visitor-waitlist/sort', [TrainingController::class, 'sortVisitor'])->name('visitor.sort');
        Route::get('/students/delete/{id}', [TrainingController::class, 'showDeleteForm'])->name('training.students.delete');
        Route::delete('/students/delete/{id}', [TrainingController::class, 'removeStudent'])->name('training.students.destroy');
        Route::patch('/students/checklist/{id}/complete', [ChecklistController::class, 'completeItem'])->name('training.students.checklist.complete');
        Route::post('/students/{student}/checklist/complete-multiple', [ChecklistController::class, 'completeMultiple'])->name('training.students.checklist.completeMultiple');
        Route::post('/students/{student}/promote', [ChecklistController::class, 'promoteStudent'])->name('training.students.promote');
        Route::post('/students/{student}/promote-visitor', [ChecklistController::class, 'promoteVisitor'])->name('training.students.promoteVisitor');
        Route::post('/students/{student}/assign-t2', [ChecklistController::class, 'assignT2Checklist'])->name('training.students.assignT2');
    });

    // Student-only +
    Route::group(['middleware' => ['auth', 'student']], function () {
        Route::get('/api/training-notes', [VatcanController::class, 'getVatcanNotes'])->name('vatcan.notes.all');
        Route::get('/resources', [TrainingController::class, 'viewResources'])->name('training.resources');
        Route::get('/students/{id}/allnotes', [TrainingController::class, 'allNotes'])->name('training.students.allnotes');
        Route::get('/renew/{token}', [TrainingController::class, 'renewTraining'])->name('training.renew');
        Route::post('/students/{student}/edittimes', [TrainingController::class, 'editTimes'])->name('training.students.editTimes');
    });
});

//ALL Public Views
Route::get('/', [HomeController::class, 'view'])->name('index');
Route::view('/airports', 'airports')->name('airports');
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
Route::get('/event-coverage', [EventController::class, 'coverage'])->name('events.coverage');
Route::get('/events/{slug}', [EventController::class, 'viewEvent'])->name('events.view');
Route::view('/about', 'about')->name('about');
Route::view('/branding', 'branding')->name('branding');
Route::get('/news/{slug}', [NewsController::class, 'viewArticlePublic'])->name('news.articlepublic');
Route::get('/news', [NewsController::class, 'viewAllPublic'])->name('news');
Route::get('/trainingtimes', [TrainingController::class, 'trainingTime'])->name('trainingtimes');
Route::view('/mochi', 'mochi')->name('mochi');
Route::view('/pdc', 'pdc')->name('pdc');
Route::view('/vfr', 'vfr')->name('vfr');
Route::view('/livemap', 'livemap')->name('livemap');
Route::view('/editmap', 'editmap')->name('editmap');
Route::get('sitemap.xml', function () {
    return \Illuminate\Support\Facades\Redirect::to('sitemap.xml');
});

Route::prefix('instructors')->group(function () {
    Route::view('/', 'instructors')->name('instructors');
    Route::post('/', [TeachersController::class, 'store'])->name('instructors.store')->middleware('staff');
    Route::get('{id}', [TeachersController::class, 'delete'])->name('instructors.delete')->middleware('staff');
});

//Redirects

Route::get('/github', function () {
    return redirect()->to('https://github.com/vancouverfir/czvr-core');
});

//Authentication

Route::get('/connect/login', [LoginController::class, 'AuthLogin'])->middleware('guest')->name('auth.connect.login');
Route::get('/connect/validate', [LoginController::class, 'validateAuthLogin'])->middleware('guest');
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

//Feedback

Route::middleware(['auth_check'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'createPost'])->name('feedback.create.post');
});

//Base level authentication
Route::group(['middleware' => 'auth'], function () {
    //Privacy accept
    Route::get('/privacyaccept', [UserController::class, 'privacyAccept']);
    Route::get('/privacydeny', [UserController::class, 'privacyDeny']);

    //User Tickets
    Route::get('/dashboard/tickets', [TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/dashboard/tickets/{id}', [TicketsController::class, 'viewTicket'])->name('tickets.viewticket');
    Route::post('/dashboard/tickets', [TicketsController::class, 'startNewTicket'])->name('tickets.startticket');
    Route::post('/dashboard/tickets/{id}', [TicketsController::class, 'addReplyToTicket'])->name('tickets.reply');

    Route::group(['middleware' => 'staff'], function () {
        Route::prefix('admin')->group(function () {
            //Uploads
            Route::get('/upload', [UploadController::class, 'upload'])->middleware('staff')->name('dashboard.upload');
            Route::post('/upload', [UploadController::class, 'uploadPost'])->middleware('staff')->name('dashboard.upload.post');
            Route::get('/upload/manage', [UploadController::class, 'manageuploads'])->middleware('staff')->name('dashboard.uploadmanage');
            Route::post('/upload/delete/{filename}', [UploadController::class, 'deletepost'])->middleware('staff')->name('dashboard.uploaddelete');
            //View Feedback
            Route::get('/feedback', [FeedbackController::class, 'index'])->name('staff.feedback.index');
            Route::get('/feedback/controller/{id}', [FeedbackController::class, 'viewControllerFeedback'])->name('staff.feedback.controller');
            Route::post('/feedback/controller/{id}', [FeedbackController::class, 'editControllerFeedback'])->name('staff.feedback.controller.edit');
            Route::get('/feedback/controller/{id}/approve', [FeedbackController::class, 'approveControllerFeedback']);
            Route::get('/feedback/controller/{id}/deny', [FeedbackController::class, 'denyControllerFeedback']);
            Route::get('/feedback/controller/{id}/delete', [FeedbackController::class, 'deleteControllerFeedback']);
            Route::get('/feedback/event/{id}', [FeedbackController::class, 'viewEventFeedback'])->name('staff.feedback.event');
            Route::get('/feedback/event/{id}/delete', [FeedbackController::class, 'deleteEventFeedback']);
            Route::get('/feedback/website/{id}', [FeedbackController::class, 'viewWebsiteFeedback'])->name('staff.feedback.website');
            Route::get('/feedback/website/{id}/delete', [FeedbackController::class, 'deleteWebsiteFeedback']);
        });

        //Closing, re-opening, and placing tickets on hold
        Route::prefix('dashboard/tickets')->group(function () {
            Route::post('/{id}/close', [TicketsController::class, 'closeTicket'])->name('tickets.closeticket');
            Route::get('/{id}/hold', [TicketsController::class, 'onholdTicket'])->name('tickets.onholdticket');
            Route::get('/{id}/open', [TicketsController::class, 'openTicket'])->name('tickets.openticket');
        });

        //View Tickets (staff)
        Route::get('/dashboard/staff/tickets', [TicketsController::class, 'staffIndex'])->name('tickets.staff');

        //Staff News
        Route::prefix('admin/news')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('news.index');
            Route::get('/article/create', [NewsController::class, 'createArticle'])->name('news.articles.create');
            Route::post('/article/create', [NewsController::class, 'postArticle'])->name('news.articles.create.post');
            Route::get('/article/{slug}', [NewsController::class, 'viewArticle'])->name('news.articles.view');
            Route::get('/article/delete/{id}', [NewsController::class, 'deleteArticle'])->name('news.articles.delete');
            Route::post('/article/edit/{id}', [NewsController::class, 'editArticle'])->name('news.articles.edit');
        });

        //Assigning Instructor
        Route::prefix('instructor')->group(function () {
            Route::post('/add', [TrainingController::class, 'assignStudent'])->name('instructor.student.add');
            Route::get('/delete/{id}', [TrainingController::class, 'deleteStudent'])->name('instructor.student.delete');
        });
    });

    //User Event Applications
    Route::post('/dashboard/events/controllerapplications/ajax', [EventController::class, 'controllerApplicationAjaxSubmit'])->name('events.controllerapplication.ajax');
    Route::get('/dashboard/events/view', [EventController::class, 'viewControllers']);

    //Staff Events
    Route::group(['prefix' => 'admin/events', 'middleware' => 'staff'], function () {
        Route::get('/', [EventController::class, 'adminIndex'])->name('events.admin.index');
        Route::get('/create', [EventController::class, 'adminCreateEvent'])->middleware('staff')->name('events.admin.create');
        Route::post('/create', [EventController::class, 'adminCreateEventPost'])->middleware('staff')->name('events.admin.create.post');
        Route::post('/{slug}/edit', [EventController::class, 'adminEditEventPost'])->middleware('staff')->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', [EventController::class, 'adminCreateUpdatePost'])->middleware('staff')->name('events.admin.update.post');
        Route::get('/{slug}', [EventController::class, 'adminViewEvent'])->name('events.admin.view');
        Route::get('/{slug}/delete', [EventController::class, 'adminDeleteEvent'])->middleware('staff')->name('events.admin.delete');
        Route::get('/{slug}/controllerapps/{cid}/delete', [EventController::class, 'adminDeleteControllerApp'])->middleware('staff')->name('events.admin.controllerapps.delete');
        Route::get('/{slug}/updates/{id}/delete', [EventController::class, 'adminDeleteUpdate'])->middleware('staff')->name('events.admin.update.delete');
        Route::get('/applications/{id}', [EventController::class, 'viewApplications'])->middleware('staff')->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', [EventController::class, 'confirmController'])->middleware('staff')->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', [EventController::class, 'addController'])->middleware('staff')->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', [EventController::class, 'deleteController'])->middleware('staff')->name('event.deletecontroller');
    });

    //Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::post('/users/changeavatar', [UserController::class, 'changeAvatar'])->name('users.changeavatar');
        Route::get('/users/changeavatar/discord', [UserController::class, 'changeAvatarDiscord'])->name('users.changeavatar.discord');
        Route::get('/users/resetavatar', [UserController::class, 'resetAvatar'])->name('users.resetavatar');
        Route::post('/users/changedisplayname', [UserController::class, 'changeDisplayName'])->name('users.changedisplayname');
        Route::get('/users/defaultavatar/{id}', function ($id) {
            $user = \App\Models\Users\User::whereId($id)->firstOrFail();
            if ($user->isAvatarDefault()) {
                return true;
            }

            return false;
        });

        //Roster
        Route::group(['middleware' => 'instructor'], function () {
            Route::get('/roster', [RosterController::class, 'index'])->name('roster.index');
            Route::post('/roster/controller/add/', [RosterController::class, 'addController'])->name('roster.addcontroller');
            Route::post('/roster/controller/addv/', [RosterController::class, 'addVisitController'])->name('roster.addvisitcontroller');
            Route::post('/roster/edit/{id}', [RosterController::class, 'editController'])->name('roster.editcontroller');
            Route::get('/roster/edit/{id}', [RosterController::class, 'editControllerForm'])->name('roster.editcontrollerform');
            Route::get('/roster/{id}', [RosterController::class, 'viewController'])->name('roster.viewcontroller');
            Route::get('/roster/{id}/delete/', [RosterController::class, 'deleteController'])->name('roster.deletecontroller');
        });

        //Email prefs
        Route::get('/emailpref', [DataController::class, 'emailPref'])->name('dashboard.emailpref');
        Route::get('/emailpref/subscribe', [DataController::class, 'subscribeEmails']);
        Route::get('/emailpref/unsubscribe', [DataController::class, 'unsubscribeEmails']);
    });
    // '/me'
    Route::prefix('dashboard/me')->group(function () {
        Route::post('/editbiography', [UserController::class, 'editBio'])->name('me.editbio');
        Route::get('/discord/link', [UserController::class, 'linkDiscord'])->name('me.discord.link');
        Route::get('/discord/unlink', [UserController::class, 'unlinkDiscord'])->name('me.discord.unlink');
        Route::get('/discord/link/redirect', [UserController::class, 'linkDiscordRedirect'])->name('me.discord.link.redirect');
        Route::get('/discord/server/join', [UserController::class, 'joinDiscordServerRedirect'])->name('me.discord.join');
        Route::get('/discord/server/join/redirect', [UserController::class, 'joinDiscordServer']);
        Route::get('/preferences', [UserController::class, 'preferences'])->name('me.preferences');
        Route::post('/preferences', [UserController::class, 'preferencesPost'])->name('me.preferences.post');
        //GDPR
        Route::get('/data', [DataController::class, 'index'])->name('me.data');
        Route::post('/data/export/all', [DataController::class, 'exportAllData'])->name('me.data.export.all');
    });

    //Users View/Edit
    Route::group(['middleware' => 'staff'], function () {
        Route::prefix('admin/users')->Group(function () {
            Route::get('/', [UserController::class, 'viewAllUsers'])->name('users.viewall');
            Route::post('/search/ajax', [UserController::class, 'searchUsers'])->name('users.search.ajax');
            Route::get('{id}', [UserController::class, 'adminViewUserProfile'])->name('users.viewprofile');
            Route::post('/{id}', [UserController::class, 'createUserNote'])->name('users.createnote');
            Route::post('/edit/{id}', [UserController::class, 'editPermissions'])->name('edit.userpermissions');
            Route::get('/{user_id}/note/{note_id}/delete', [UserController::class, 'deleteUserNote'])->name('users.deletenote');
            Route::post('/func/avatarchange', [UserController::class, 'changeUsersAvatar'])->name('users.changeusersavatar');
            Route::post('/func/avatarreset', [UserController::class, 'resetUsersAvatar'])->name('users.resetusersavatar');
            Route::post('/func/bioreset', [UserController::class, 'resetUsersBio'])->name('users.resetusersbio');
            Route::get('/{id}/delete', [UserController::class, 'deleteUser']);
            Route::get('/{id}/edit', [UserController::class, 'editUser'])->name('users.edit.create');
            Route::post('/{id}/edit', [UserController::class, 'storeEditUser'])->name('users.edit.store');
            Route::get('/{id}/email', [UserController::class, 'emailCreate'])->name('users.email.create');
            Route::get('/{id}/email', [UserController::class, 'emailStore'])->name('users.email.store');
        });
    });

    Route::group(['middleware' => 'staff'], function () {
        //Upload and Delete ATC Resources
        Route::post('/atcresources', [AtcResourcesController::class, 'uploadResource'])->name('atcresources.upload');
        Route::get('/atcresources/delete/{id}', [AtcResourcesController::class, 'deleteResource'])->name('atcresources.delete');
        //Policy creation and settings
        Route::post('/policies', [PoliciesController::class, 'addPolicy'])->name('policies.create');
        Route::post('/policies/{id}/edit', [PoliciesController::class, 'editPolicy']);
        Route::get('/policies/{id}/delete', [PoliciesController::class, 'deletePolicy']);
        Route::post('/policies/section/create', [PoliciesController::class, 'addPolicySection'])->name('policysection.create');
        Route::get('/policies/section/{id}/delete', [PoliciesController::class, 'deletePolicySection']);
    });

    //ADMIN ONLY
    //Minutes
    Route::group(['middleware' => 'executive'], function () {
        Route::get('/meetingminutes/{id}', [NewsController::class, 'minutesDelete'])->name('meetingminutes.delete');
        Route::post('/meetingminutes', [NewsController::class, 'minutesUpload'])->name('meetingminutes.upload');
        //Network
        Route::get('/admin/network', [NetworkController::class, 'index'])->name('network.index');
        Route::get('/admin/network/monitoredpositions', [NetworkController::class, 'monitoredPositionsIndex'])->name('network.monitoredpositions.index');
        Route::get('/admin/network/monitoredpositions/{position}', [NetworkController::class, 'viewMonitoredPosition'])->name('network.monitoredpositions.view');
        Route::post('/admin/network/monitoredpositions/create', [NetworkController::class, 'createMonitoredPosition'])->name('network.monitoredpositions.create');

        //Settings
        Route::prefix('admin/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
            Route::get('/roles', [SettingsController::class, 'viewRoles'])->name('roles.view');
            Route::post('/addrole', [SettingsController::class, 'addRole'])->name('roles.add');
            Route::get('/deleterole/{id}', [SettingsController::class, 'deleteRole'])->name('roles.delete');
            Route::post('/userrole/add', [UserController::class, 'addRole'])->name('user.role.add');
            Route::get('/deleteuserrole/{id}/{user}', [UserController::class, 'deleterole'])->name('user.role.delete');
            Route::get('/site-information', [SettingsController::class, 'siteInformation'])->name('settings.siteinformation');
            Route::post('/site-information', [SettingsController::class, 'saveSiteInformation'])->name('settings.siteinformation.post');
            Route::get('/emails', [SettingsController::class, 'emails'])->name('settings.emails');
            Route::post('/emails', [SettingsController::class, 'saveEmails'])->name('settings.emails.post');
            Route::get('/audit-log', [SettingsController::class, 'auditLog'])->name('settings.auditlog');
            Route::get('/staff', [StaffListController::class, 'editIndex'])->name('settings.staff');
            Route::post('/staff/{id}', [StaffListController::class, 'editStaffMember'])->name('settings.staff.editmember');
            Route::post('/staff/a/add', [StaffListController::class, 'addStaffMember'])->name('settings.staff.addmember');
            Route::post('/staff/{id}/delete', [StaffListController::class, 'deleteStaffMember'])->name('settings.staff.deletemember');
            Route::get('/banner', [SettingsController::class, 'banner'])->name('settings.banner');
            Route::post('/banner', [SettingsController::class, 'bannerEdit'])->name('settings.banner.edit');
            Route::get('/images', [SettingsController::class, 'imagesIndex'])->name('settings.images');
            Route::post('images', [SettingsController::class, 'uploadImage'])->name('settings.images.upload');
            Route::post('/images/edit/{id}', [SettingsController::class, 'editImage'])->name('settings.images.edit');
            Route::get('/images/test/{id}', [SettingsController::class, 'testImage'])->name('settings.images.test');
            Route::get('/images/delete/{id}', [SettingsController::class, 'deleteImage'])->name('settings.images.delete');
        });
    });
});
