<?php

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
    Route::get('/', 'Booking\BookingController@indexPublic')->name('booking');

    // Protected Create Booking
    Route::group(['middleware' => 'booking_certified'], function () {
        Route::post('/', 'Booking\BookingController@create')->name('booking.create');
    });

    // Protected Booking
    Route::group(['middleware' => 'certified'], function () {
        Route::get('/{id}/edit', 'Booking\BookingController@edit')->name('booking.edit');
        Route::put('/{id}', 'Booking\BookingController@update')->name('booking.update');
        Route::delete('/{id}', 'Booking\BookingController@delete')->name('booking.delete');
    });
});

// Training Subdomain
Route::group(['domain' => 'training.czvr.ca'], function () {
    // Index
    Route::get('/', 'AtcTraining\TrainingController@index')->name('training.index');
    Route::post('/trainingtimes', 'AtcTraining\TrainingController@editTrainingTime')->middleware('staff')->name('waittime.edit');

    // Instructor-only +
    Route::group(['middleware' => 'instructor'], function () {
        // Atc Resources Edit
        Route::get('/atcresources', 'Publications\AtcResourcesController@index')->middleware('atc')->name('atcresources.index');

        Route::get('/instructors', 'AtcTraining\TrainingController@instructorsIndex')->name('training.instructors');
        Route::post('/instructors', 'AtcTraining\TrainingController@addInstructor')->name('training.instructors.add');
    });

    // Mentor-only +
    Route::group(['middleware' => 'mentor'], function () {
        // Atc Resources Edit
        Route::get('/atcresources', 'Publications\AtcResourcesController@index')->middleware('atc')->name('atcresources.index');

        // Instructing Session
        Route::get('/instructingsessions', 'AtcTraining\InstructingSessionsController@index')->name('training.instructingsessions.index');
        Route::get('/instructingsessions/new', 'AtcTraining\InstructingSessionsController@createForm')->name('training.instructingsessions.new');
        Route::post('/instructingsessions', 'AtcTraining\InstructingSessionsController@create')->name('training.instructingsessions.create');
        Route::get('/instructingsessions/{session}', 'AtcTraining\InstructingSessionsController@show')->name('training.instructingsessions.viewsession');
        Route::get('/instructingsessions/{session}/edit', 'AtcTraining\InstructingSessionsController@edit')->name('training.instructingsessions.edit');
        Route::put('/instructingsessions/{session}', 'AtcTraining\InstructingSessionsController@update')->name('training.instructingsessions.update');
        Route::delete('/instructingsessions/{session}', 'AtcTraining\InstructingSessionsController@cancel')->name('training.instructingsessions.cancel');

        // Labels Edit
        Route::post('/students/{id}/assign/label', 'AtcTraining\LabelController@assignLabel')->name('training.students.assign.label');
        Route::get('/students/{id}/drop/label/{student_label_id}', 'AtcTraining\LabelController@dropLabel')->name('training.students.drop.label');

        // Students Edit
        Route::post('/students/{student}/complete', 'AtcTraining\TrainingController@completeTraining')->name('training.students.completeTraining');
        Route::get('/allstudents', 'AtcTraining\TrainingController@AllStudents')->name('training.students.students');
        Route::post('/add', 'AtcTraining\TrainingController@newStudent')->name('instructor.student.add.new');
        Route::get('/completed', 'AtcTraining\TrainingController@completedStudents')->name('training.students.completed');
        Route::get('/waitlist', 'AtcTraining\TrainingController@newStudents')->name('training.students.waitlist');
        Route::get('/students/{id}', 'AtcTraining\TrainingController@viewStudent')->name('training.students.view');
        Route::post('/students/{id}/assigninstructor', 'AtcTraining\TrainingController@assignInstructorToStudent')->name('training.students.assigninstructor');
        Route::post('/trainingnotes/add/{id}', 'AtcTraining\TrainingController@addNote')->name('add.trainingnote');
        Route::get('/trainingnotes/create/{id}', 'AtcTraining\TrainingController@newNoteView')->name('view.add.note');
        Route::post('/waitlist/sort', 'AtcTraining\TrainingController@sort')->name('waitlist.sort');
        Route::post('/visitor-waitlist/sort', 'AtcTraining\TrainingController@sortVisitor')->name('visitor.sort');
        Route::get('/students/delete/{id}', 'AtcTraining\TrainingController@showDeleteForm')->name('training.students.delete');
        Route::delete('/students/delete/{id}', 'AtcTraining\TrainingController@removeStudent')->name('training.students.destroy');
        Route::patch('/students/checklist/{id}/complete', 'AtcTraining\ChecklistController@completeItem')->name('training.students.checklist.complete');
        Route::post('/students/{student}/checklist/complete-multiple', 'AtcTraining\ChecklistController@completeMultiple')->name('training.students.checklist.completeMultiple');
        Route::post('/students/{student}/promote', 'AtcTraining\ChecklistController@promoteStudent')->name('training.students.promote');
        Route::post('/students/{student}/promote-visitor', 'AtcTraining\ChecklistController@promoteVisitor')->name('training.students.promoteVisitor');
        Route::post('/students/{student}/assign-t2', 'AtcTraining\ChecklistController@assignT2Checklist')->name('training.students.assignT2');
    });

    // Student-only +
    Route::group(['middleware' => 'student'], function () {
        Route::get('/api/training-notes', 'AtcTraining\VatcanController@getVatcanNotes')->name('vatcan.notes.all');
        Route::get('/resources', 'AtcTraining\TrainingController@viewResources')->name('training.resources');
        Route::get('/students/{id}/allnotes', 'AtcTraining\TrainingController@allNotes')->name('training.students.allnotes');
        Route::get('/renew/{token}', 'AtcTraining\TrainingController@renewTraining')->name('training.renew');
        Route::post('/students/{student}/edittimes', 'AtcTraining\TrainingController@editTimes')->name('training.students.editTimes');
    });
});

//ALL Public Views
Route::get('/', 'HomeController@view')->name('index');
Route::view('/airports', 'airports')->name('airports');
Route::get('/roster', 'AtcTraining\RosterController@showPublic')->name('roster.public');
Route::get('/roster/{id}', 'Users\UserController@viewProfile');
Route::get('/roster/{id}/connections', 'Users\UserController@viewConnections');
Route::get('/join', 'AtcTraining\TrainingController@joinvancouver')->name('join.public');
Route::get('/staff', 'Users\StaffListController@index')->name('staff');
Route::get('/policies', 'Publications\PoliciesController@index')->name('policies');
Route::get('/meetingminutes', 'News\NewsController@minutesIndex')->name('meetingminutes');
Route::view('/privacy', 'privacy')->name('privacy');
Route::get('/yourfeedback', 'Feedback\FeedbackController@yourFeedback')->name('yourfeedback');
Route::get('/events', 'Events\EventController@index')->name('events.index');
Route::get('/event-coverage', 'Events\EventController@coverage')->name('events.coverage');
Route::get('/events/{slug}', 'Events\EventController@viewEvent')->name('events.view');
Route::view('/about', 'about')->name('about');
Route::view('/branding', 'branding')->name('branding');
Route::get('/news/{slug}', 'News\NewsController@viewArticlePublic')->name('news.articlepublic');
Route::get('/news', 'News\NewsController@viewAllPublic')->name('news');
Route::get('/trainingtimes', 'AtcTraining\TrainingController@trainingTime')->name('trainingtimes');
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
    Route::post('/', 'AtcTraining\TeachersController@store')->name('instructors.store')->middleware('staff');
    Route::get('{id}', 'AtcTraining\TeachersController@delete')->name('instructors.delete')->middleware('staff');
});

//Redirects

Route::get('/github', function () {
    return redirect()->to('https://github.com/vancouverfir/czvr-core');
});

//Authentication

Route::get('/connect/login', 'Auth\LoginController@AuthLogin')->middleware('guest')->name('auth.connect.login');
Route::get('/connect/validate', 'Auth\LoginController@validateAuthLogin')->middleware('guest');
Route::get('/logout', 'Auth\LoginController@logout')->middleware('auth')->name('auth.logout');

//Feedback

Route::middleware(['auth_check'])->group(function () {
    Route::get('/feedback', 'Feedback\FeedbackController@create')->name('feedback.create');
    Route::post('/feedback', 'Feedback\FeedbackController@createPost')->name('feedback.create.post');
});

//Base level authentication
Route::group(['middleware' => 'auth'], function () {
    //Privacy accept
    Route::get('/privacyaccept', 'Users\UserController@privacyAccept');
    Route::get('/privacydeny', 'Users\UserController@privacyDeny');

    //User Tickets
    Route::get('/dashboard/tickets', 'Tickets\TicketsController@index')->name('tickets.index');
    Route::get('/dashboard/tickets/{id}', 'Tickets\TicketsController@viewTicket')->name('tickets.viewticket');
    Route::post('/dashboard/tickets', 'Tickets\TicketsController@startNewTicket')->name('tickets.startticket');
    Route::post('/dashboard/tickets/{id}', 'Tickets\TicketsController@addReplyToTicket')->name('tickets.reply');

    Route::group(['middleware' => 'staff'], function () {
        Route::prefix('admin')->group(function () {
            //Uploads
            Route::get('/upload', 'Publications\UploadController@upload')->middleware('staff')->name('dashboard.upload');
            Route::post('/upload', 'Publications\UploadController@uploadPost')->middleware('staff')->name('dashboard.upload.post');
            Route::get('/upload/manage', 'Publications\UploadController@manageuploads')->middleware('staff')->name('dashboard.uploadmanage');
            Route::post('/upload/delete/{filename}', 'Publications\UploadController@deletepost')->middleware('staff')->name('dashboard.uploaddelete');
            //View Feedback
            Route::get('/feedback', 'Feedback\FeedbackController@index')->name('staff.feedback.index');
            Route::get('/feedback/controller/{id}', 'Feedback\FeedbackController@viewControllerFeedback')->name('staff.feedback.controller');
            Route::post('/feedback/controller/{id}', 'Feedback\FeedbackController@editControllerFeedback')->name('staff.feedback.controller.edit');
            Route::get('/feedback/controller/{id}/approve', 'Feedback\FeedbackController@approveControllerFeedback');
            Route::get('/feedback/controller/{id}/deny', 'Feedback\FeedbackController@denyControllerFeedback');
            Route::get('/feedback/controller/{id}/delete', 'Feedback\FeedbackController@deleteControllerFeedback');
            Route::get('/feedback/event/{id}', 'Feedback\FeedbackController@viewEventFeedback')->name('staff.feedback.event');
            Route::get('/feedback/event/{id}/delete', 'Feedback\FeedbackController@deleteEventFeedback');
            Route::get('/feedback/website/{id}', 'Feedback\FeedbackController@viewWebsiteFeedback')->name('staff.feedback.website');
            Route::get('/feedback/website/{id}/delete', 'Feedback\FeedbackController@deleteWebsiteFeedback');
        });

        //Closing, re-opening, and placing tickets on hold
        Route::prefix('dashboard/tickets')->group(function () {
            Route::post('/{id}/close', 'Tickets\TicketsController@closeTicket')->name('tickets.closeticket');
            Route::get('/{id}/hold', 'Tickets\TicketsController@onholdTicket')->name('tickets.onholdticket');
            Route::get('/{id}/open', 'Tickets\TicketsController@openTicket')->name('tickets.openticket');
        });

        //View Tickets (staff)
        Route::get('/dashboard/staff/tickets', 'Tickets\TicketsController@staffIndex')->name('tickets.staff');

        //Staff News
        Route::prefix('admin/news')->group(function () {
            Route::get('/', 'News\NewsController@index')->name('news.index');
            Route::get('/article/create', 'News\NewsController@createArticle')->name('news.articles.create');
            Route::post('/article/create', 'News\NewsController@postArticle')->name('news.articles.create.post');
            Route::get('/article/{slug}', 'News\NewsController@viewArticle')->name('news.articles.view');
            Route::get('/article/delete/{id}', 'News\NewsController@deleteArticle')->name('news.articles.delete');
            Route::post('/article/edit/{id}', 'News\NewsController@editArticle')->name('news.articles.edit');
        });

        //Assigning Instructor
        Route::prefix('instructor')->group(function () {
            Route::post('/add', 'AtcTraining\TrainingController@assignStudent')->name('instructor.student.add');
            Route::get('/delete/{id}', 'AtcTraining\TrainingController@deleteStudent')->name('instructor.student.delete');
        });
    });

    //User Event Applications
    Route::post('/dashboard/events/controllerapplications/ajax', 'Events\EventController@controllerApplicationAjaxSubmit')->name('events.controllerapplication.ajax');
    Route::get('/dashboard/events/view', 'Events\EventController@viewControllers');

    //Staff Events
    Route::group(['prefix' => 'admin/events', 'middleware' => 'staff'], function () {
        Route::get('/', 'Events\EventController@adminIndex')->name('events.admin.index');
        Route::get('/create', 'Events\EventController@adminCreateEvent')->middleware('staff')->name('events.admin.create');
        Route::post('/create', 'Events\EventController@adminCreateEventPost')->middleware('staff')->name('events.admin.create.post');
        Route::post('/{slug}/edit', 'Events\EventController@adminEditEventPost')->middleware('staff')->name('events.admin.edit.post');
        Route::post('/{slug}/update/create', 'Events\EventController@adminCreateUpdatePost')->middleware('staff')->name('events.admin.update.post');
        Route::get('/{slug}', 'Events\EventController@adminViewEvent')->name('events.admin.view');
        Route::get('/{slug}/delete', 'Events\EventController@adminDeleteEvent')->middleware('staff')->name('events.admin.delete');
        Route::get('/{slug}/controllerapps/{cid}/delete', 'Events\EventController@adminDeleteControllerApp')->middleware('staff')->name('events.admin.controllerapps.delete');
        Route::get('/{slug}/updates/{id}/delete', 'Events\EventController@adminDeleteUpdate')->middleware('staff')->name('events.admin.update.delete');
        Route::get('/applications/{id}', 'Events\EventController@viewApplications')->middleware('staff')->name('event.viewapplications');
        Route::post('/applications/confirm/{id}', 'Events\EventController@confirmController')->middleware('staff')->name('event.confirmapplication');
        Route::post('/applications/manualconfirm/{id}', 'Events\EventController@addController')->middleware('staff')->name('event.addcontroller');
        Route::post('/applications/manualconfirm/delete/{id}', 'Events\EventController@deleteController')->middleware('staff')->name('event.deletecontroller');
    });

    //Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', 'DashboardController@index')->name('dashboard.index');
        Route::post('/users/changeavatar', 'Users\UserController@changeAvatar')->name('users.changeavatar');
        Route::get('/users/changeavatar/discord', 'Users\UserController@changeAvatarDiscord')->name('users.changeavatar.discord');
        Route::get('/users/resetavatar', 'Users\UserController@resetAvatar')->name('users.resetavatar');
        Route::post('/users/changedisplayname', 'Users\UserController@changeDisplayName')->name('users.changedisplayname');
        Route::get('/users/defaultavatar/{id}', function ($id) {
            $user = \App\User::whereId($id)->firstOrFail();
            if ($user->isAvatarDefault()) {
                return true;
            }

            return false;
        });

        //Roster
        Route::group(['middleware' => 'instructor'], function () {
            Route::get('/roster', 'AtcTraining\RosterController@index')->name('roster.index');
            Route::post('/roster/controller/add/', 'AtcTraining\RosterController@addController')->name('roster.addcontroller');
            Route::post('/roster/controller/addv/', 'AtcTraining\RosterController@addVisitController')->name('roster.addvisitcontroller');
            Route::post('/roster/edit/{id}', 'AtcTraining\RosterController@editController')->name('roster.editcontroller');
            Route::get('/roster/edit/{id}', 'AtcTraining\RosterController@editControllerForm')->name('roster.editcontrollerform');
            Route::get('/roster/{id}', 'AtcTraining\RosterController@viewController')->name('roster.viewcontroller');
            Route::get('/roster/{id}/delete/', 'AtcTraining\RosterController@deleteController')->name('roster.deletecontroller');
        });

        //Email prefs
        Route::get('/emailpref', 'Users\DataController@emailPref')->name('dashboard.emailpref');
        Route::get('/emailpref/subscribe', 'Users\DataController@subscribeEmails');
        Route::get('/emailpref/unsubscribe', 'Users\DataController@unsubscribeEmails');
    });
    // '/me'
    Route::prefix('dashboard/me')->group(function () {
        Route::post('/editbiography', 'Users\UserController@editBio')->name('me.editbio');
        Route::get('/discord/link', 'Users\UserController@linkDiscord')->name('me.discord.link');
        Route::get('/discord/unlink', 'Users\UserController@unlinkDiscord')->name('me.discord.unlink');
        Route::get('/discord/link/redirect', 'Users\UserController@linkDiscordRedirect')->name('me.discord.link.redirect');
        Route::get('/discord/server/join', 'Users\UserController@joinDiscordServerRedirect')->name('me.discord.join');
        Route::get('/discord/server/join/redirect', 'Users\UserController@joinDiscordServer');
        Route::get('/preferences', 'Users\UserController@preferences')->name('me.preferences');
        Route::post('/preferences', 'Users\UserController@preferencesPost')->name('me.preferences.post');
        //GDPR
        Route::get('/data', 'Users\DataController@index')->name('me.data');
        Route::post('/data/export/all', 'Users\DataController@exportAllData')->name('me.data.export.all');
    });

    //Users View/Edit
    Route::group(['middleware' => 'staff'], function () {
        Route::prefix('admin/users')->Group(function () {
            Route::get('/', 'Users\UserController@viewAllUsers')->name('users.viewall');
            Route::post('/search/ajax', 'Users\UserController@searchUsers')->name('users.search.ajax');
            Route::get('{id}', 'Users\UserController@adminViewUserProfile')->name('users.viewprofile');
            Route::post('/{id}', 'Users\UserController@createUserNote')->name('users.createnote');
            Route::post('/edit/{id}', 'Users\UserController@editPermissions')->name('edit.userpermissions');
            Route::get('/{user_id}/note/{note_id}/delete', 'Users\UserController@deleteUserNote')->name('users.deletenote');
            Route::post('/func/avatarchange', 'Users\UserController@changeUsersAvatar')->name('users.changeusersavatar');
            Route::post('/func/avatarreset', 'Users\UserController@resetUsersAvatar')->name('users.resetusersavatar');
            Route::post('/func/bioreset', 'Users\UserController@resetUsersBio')->name('users.resetusersbio');
            Route::get('/{id}/delete', 'Users\UserController@deleteUser');
            Route::get('/{id}/edit', 'Users\UserController@editUser')->name('users.edit.create');
            Route::post('/{id}/edit', 'Users\UserController@storeEditUser')->name('users.edit.store');
            Route::get('/{id}/email', 'Users\UserController@emailCreate')->name('users.email.create');
            Route::get('/{id}/email', 'Users\UserController@emailStore')->name('users.email.store');
        });
    });

    //Upload and Delete ATC Resources
    Route::group(['middleware' => 'staff'], function () {
        Route::post('/atcresources', 'Publications\AtcResourcesController@uploadResource')->name('atcresources.upload');
        Route::get('/atcresources/delete/{id}', 'Publications\AtcResourcesController@deleteResource')->name('atcresources.delete');
    });

    //ADMIN ONLY
    //Minutes
    Route::group(['middleware' => 'executive'], function () {
        Route::get('/meetingminutes/{id}', 'News\NewsController@minutesDelete')->name('meetingminutes.delete');
        Route::post('/meetingminutes', 'News\NewsController@minutesUpload')->name('meetingminutes.upload');
        //Network
        Route::get('/admin/network', 'Network\NetworkController@index')->name('network.index');
        Route::get('/admin/network/monitoredpositions', 'Network\NetworkController@monitoredPositionsIndex')->name('network.monitoredpositions.index');
        Route::get('/admin/network/monitoredpositions/{position}', 'Network\NetworkController@viewMonitoredPosition')->name('network.monitoredpositions.view');
        Route::post('/admin/network/monitoredpositions/create', 'Network\NetworkController@createMonitoredPosition')->name('network.monitoredpositions.create');
        //Policy creation and settings
        Route::post('/policies', 'Publications\PoliciesController@addPolicy')->name('policies.create');
        Route::post('/policies/{id}/edit', 'Publications\PoliciesController@editPolicy');
        Route::get('/policies/{id}/delete', 'Publications\PoliciesController@deletePolicy');
        Route::post('/policies/section/create', 'Publications\PoliciesController@addPolicySection')->name('policysection.create');
        Route::get('/policies/section/{id}/delete', 'Publications\PoliciesController@deletePolicySection');

        //Settings
        Route::prefix('admin/settings')->group(function () {
            Route::get('/', 'Settings\SettingsController@index')->name('settings.index');
            Route::get('/roles', 'Settings\SettingsController@viewRoles')->name('roles.view');
            Route::post('/addrole', 'Settings\SettingsController@addRole')->name('roles.add');
            Route::get('/deleterole/{id}', 'Settings\SettingsController@deleteRole')->name('roles.delete');
            Route::post('/userrole/add', 'Users\UserController@addRole')->name('user.role.add');
            Route::get('/deleteuserrole/{id}/{user}', 'Users\UserController@deleterole')->name('user.role.delete');
            Route::get('/site-information', 'Settings\SettingsController@siteInformation')->name('settings.siteinformation');
            Route::post('/site-information', 'Settings\SettingsController@saveSiteInformation')->name('settings.siteinformation.post');
            Route::get('/emails', 'Settings\SettingsController@emails')->name('settings.emails');
            Route::post('/emails', 'Settings\SettingsController@saveEmails')->name('settings.emails.post');
            Route::get('/audit-log', 'Settings\SettingsController@auditLog')->name('settings.auditlog');
            Route::get('/staff', 'Users\StaffListController@editIndex')->name('settings.staff');
            Route::post('/staff/{id}', 'Users\StaffListController@editStaffMember')->name('settings.staff.editmember');
            Route::post('/staff/a/add', 'Users\StaffListController@addStaffMember')->name('settings.staff.addmember');
            Route::post('/staff/{id}/delete', 'Users\StaffListController@deleteStaffMember')->name('settings.staff.deletemember');
            Route::get('/banner', 'Settings\SettingsController@banner')->name('settings.banner');
            Route::post('/banner', 'Settings\SettingsController@bannerEdit')->name('settings.banner.edit');
            Route::get('/images', 'Settings\SettingsController@imagesIndex')->name('settings.images');
            Route::post('images', 'Settings\SettingsController@uploadImage')->name('settings.images.upload');
            Route::post('/images/edit/{id}', 'Settings\SettingsController@editImage')->name('settings.images.edit');
            Route::get('/images/test/{id}', 'Settings\SettingsController@testImage')->name('settings.images.test');
            Route::get('/images/delete/{id}', 'Settings\SettingsController@deleteImage')->name('settings.images.delete');
        });
    });
});
