<?php

use App\Jobs\ProcessDataExport;
use App\Models\AtcTraining\Checklist;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use App\Models\Events\ControllerApplication;
use App\Models\Events\Event;
use App\Models\Tickets\Ticket;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Notifications\DataExportRequest;
use Database\Seeders\AuthorizationSeeder;
use Database\Seeders\StudentLabelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(AuthorizationSeeder::class);
    Notification::fake();
});

it('reserves administrator role identifiers and keeps the canonical role authorized', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo('roles.manage');

    $this->actingAs($manager)
        ->from('/admin/settings/roles')
        ->post('/admin/settings/addrole', ['name' => 'Admin'])
        ->assertSessionHasErrors('name');

    $legacy = Role::create(['name' => 'Legacy Admin', 'slug' => 'admin']);
    $legacy->permissions()->attach($manager->directPermissions()->pluck('permissions.id'));
    $someone = User::factory()->create();
    $someone->assignRole($legacy);
    $collision = Role::create(['name' => 'Administrator', 'slug' => 'custom-administrator']);
    $someone->assignRole($collision);

    expect($someone->isAdministrator())->toBeFalse()
        ->and($someone->hasRole('administrator'))->toBeFalse()
        ->and($someone->hasPermissionTo('roles.manage'))->toBeFalse()
        ->and($manager->canAssignRole($legacy))->toBeFalse()
        ->and(User::factory()->administrator()->create()->isAdministrator())->toBeTrue();
});

it('prevents delegated role managers from handing out permissions they lack', function () {
    $manager = User::factory()->create();
    $recipient = User::factory()->create();
    $manager->givePermissionTo('roles.manage');
    $role = Role::create(['name' => 'Site Operator', 'slug' => 'site-operator']);
    $role->permissions()->attach(\App\Models\Users\Permission::where('slug', 'site-settings.manage')->value('id'));

    $this->actingAs($manager)->put("/admin/users/{$recipient->id}/access", ['roles' => [$role->id]])->assertRedirect();

    expect($recipient->fresh()->hasRole('site-operator'))->toBeFalse();
});

it('renders flash text as text and keeps safe student note formatting', function () {
    $this->withSession(['success' => '<strong>Example name</strong>'])
        ->get('/privacy')
        ->assertOk()
        ->assertSee('<strong>Example name</strong>')
        ->assertDontSee('<strong>Example name</strong>', false);

    $note = new StudentNote(['content' => '<p class="legacy">An <u>existing note</u> &amp; its formatting</p>']);
    expect($note->formattedContent()->toHtml())->toBe('<p>An <u>existing note</u> &amp; its formatting</p>');

    $ticket = new Ticket(['message' => '[Help](https://example.com/help)']);
    expect((string) $ticket->toHtml('message'))->toContain('href="https://example.com/help"');
    $ticket->message = '[Unsupported link](data:text/plain,sample)';
    expect((string) $ticket->toHtml('message'))->not->toContain('href="data:');
});

it('requires privacy acceptance and rejects retained sessions for deleted accounts', function () {
    $pending = User::factory()->create(['init' => 0]);
    $this->actingAs($pending)->getJson('/dashboard')->assertForbidden();

    $deleted = User::factory()->create(['deleted' => true]);
    $this->actingAs($deleted)->getJson('/dashboard')->assertUnauthorized();
    $this->assertGuest();
});

it('handles privacy denial when a roster entry needs to retain its CID', function () {
    $user = User::factory()->create(['init' => 0]);
    RosterMember::create(['cid' => $user->id, 'user_id' => $user->id, 'full_name' => 'Roster Member', 'status' => 'home', 'visit' => 0]);
    $this->actingAs($user)->delete('/privacydeny')->assertRedirect(route('index'));
    $this->assertGuest();
    expect($user->fresh()->deleted)->toBeTruthy()->and($user->fresh()->remember_token)->toBeNull();
});

it('hides inactive and non-rostered public profiles', function () {
    $user = User::factory()->create();
    $this->get("/roster/{$user->id}")->assertNotFound();
    RosterMember::create(['cid' => $user->id, 'user_id' => $user->id, 'full_name' => 'Roster Member', 'status' => 'home', 'visit' => 0, 'active' => false]);
    $this->get("/roster/{$user->id}/connections")->assertNotFound();
});

it('exports account data with the relationships supported by the application', function () {
    $user = User::factory()->create();
    (new ProcessDataExport($user))->handle();
    Notification::assertSentTo($user, DataExportRequest::class);
});

it('retains the other event application when confirming a controller', function () {
    $staff = User::factory()->staff()->create();
    $controller = User::factory()->controller()->create();
    $makeEvent = fn (string $slug) => Event::create([
        'name' => $slug, 'slug' => $slug, 'start_timestamp' => now(),
        'end_timestamp' => now()->addHour(), 'description' => 'Event', 'user_id' => $staff->id,
    ]);
    $first = $makeEvent('first');
    $second = $makeEvent('second');

    foreach ([$first, $second] as $event) {
        ControllerApplication::create([
            'event_id' => $event->id, 'user_id' => $controller->id, 'submission_timestamp' => now(),
            'start_availability_timestamp' => now(), 'end_availability_timestamp' => now()->addHour(),
        ]);
    }

    $this->actingAs($staff)->post(route('event.confirmapplication', $second), [
        'user_cid' => $controller->id, 'start_timestamp' => now()->toDateTimeString(),
        'end_timestamp' => now()->addHour()->toDateTimeString(), 'airport' => 'CYVR',
    ])->assertRedirect();

    $this->assertDatabaseHas('event_controller_applications', ['event_id' => $first->id, 'user_id' => $controller->id]);
    $this->assertDatabaseMissing('event_controller_applications', ['event_id' => $second->id, 'user_id' => $controller->id]);
    $this->assertDatabaseHas('event_confirms', ['event_id' => $second->id, 'user_id' => $controller->id]);
});

it('matches exact booking positions to a solo or full certification', function () {
    $member = new RosterMember(['active' => true, 'delgnd_t2' => 2, 'app_t2' => 1, 'fss' => 3]);

    expect($member->canBookCallsign('CYVR_N_GND'))->toBeTrue()
        ->and($member->canBookCallsign('CYVR_APP'))->toBeFalse()
        ->and($member->canBookCallsign('CYVR_UNKNOWN_GND'))->toBeFalse()
        ->and($member->canBookCallsign('CZVR_F_CTR'))->toBeTrue()
        ->and($member->canBookCallsign('CZVR_CTR'))->toBeFalse();
});

it('keeps main-site routes on the configured main host', function () {
    $this->get('http://'.config('app.training_domain').'/privacy')->assertNotFound();
    expect(parse_url(route('training.index'), PHP_URL_HOST))->toBe(config('app.training_domain'));
});

it('requires checklist administration permission for changes to global checklists', function () {
    $mentor = User::factory()->mentor()->create();
    $checklist = Checklist::create(['name' => 'Shared checklist']);

    $this->actingAs($mentor)->delete(route('training.checklists.destroy', $checklist))->assertForbidden();
    $this->assertModelExists($checklist);
});

it('preserves a checklist when an observer in training earns S1', function () {
    $this->seed(StudentLabelSeeder::class);
    $user = User::factory()->create(['rating_id' => 1, 'rating_short' => 'OBS']);
    $student = Student::create(['user_id' => $user->id, 'status' => Student::STATUS_ACTIVE]);
    $student->addLabel('S1 Training');
    $item = Checklist::create(['name' => 'Optional checklist'])->items()->create(['item' => 'Completed']);
    $student->checklistItems()->create(['checklist_item_id' => $item->id, 'completed' => true]);

    app(\App\Services\TrainingProgressionService::class)->synchronizeRating($user, 2);

    expect($student->checklistItems()->where('completed', true)->count())->toBe(1)
        ->and($student->completionLockReasons())->not->toBeEmpty();
});

it('can reinstate an expired visiting controller to the visitor queue', function () {
    $this->seed(StudentLabelSeeder::class);
    $user = User::factory()->create();
    $student = Student::create(['user_id' => $user->id, 'status' => Student::STATUS_MARKED_FOR_REMOVAL]);
    $student->addLabel('Visitor VATCAN');
    $student->addLabel('Marked for Removal');
    $student->addLabel('Inactive');

    expect($student->isVisitor())->toBeTrue()
        ->and(\App\Models\AtcTraining\StudentLabel::assignableTo($student)->where('name', 'Visitor Waitlist')->exists())->toBeTrue()
        ->and(\App\Models\AtcTraining\StudentLabel::assignableTo($student)->where('name', 'Waitlist')->exists())->toBeFalse();

    $labelId = \App\Models\AtcTraining\StudentLabel::where('name', 'Visitor Waitlist')->value('id');
    $this->actingAs(User::factory()->staff()->create())
        ->post(route('training.students.assign.label', $student), ['student_label_id' => $labelId])
        ->assertRedirect();

    expect((int) $student->fresh()->status)->toBe(Student::STATUS_VISITOR_WAITLIST);
});

it('records the instructor CID on recertification notes and requires completed checklists', function () {
    $this->seed(StudentLabelSeeder::class);
    $actor = User::factory()->instructor()->create();
    \App\Models\AtcTraining\Instructor::create([
        'user_id' => $actor->id, 'qualification' => 'ALL', 'email' => $actor->email,
    ]);

    $user = User::factory()->create(['rating_id' => 2, 'rating_short' => 'S1']);
    $student = Student::create(['user_id' => $user->id, 'status' => Student::STATUS_WAITLIST]);
    $student->addLabel('Waitlist');
    $checklist = Checklist::create(['name' => 'S1 prerequisites']);
    $checklist->items()->create(['item' => 'Required competency']);
    \App\Models\AtcTraining\LabelChecklistMap::create([
        'label_id' => \App\Models\AtcTraining\StudentLabel::where('name', 'S1 Training')->value('id'),
        'checklist_id' => $checklist->id,
        'tier_type' => 'T1',
    ]);

    $progression = app(\App\Services\TrainingProgressionService::class);
    expect($progression->startRecertification($student, $actor))->toBeTrue()
        ->and($progression->completeRecertification($student, $actor))->toBeFalse();
    $this->assertDatabaseHas('student_notes', ['student_id' => $student->id, 'author_cid' => $actor->id, 'title' => 'Recertification Started']);
    expect($student->fresh()->isRecertification())->toBeTrue();
});

it('sends controller policy notices only to eligible subscribers', function () {
    $author = User::factory()->create();
    $author->assignRole('staff');
    $subscriber = User::factory()->controller()->create();
    $unsubscribed = User::factory()->controller()->create(['gdpr_subscribed_emails' => false]);
    $deleted = User::factory()->controller()->create(['deleted' => true]);
    $placeholder = User::factory()->controller()->create(['email' => 'placeholder@invalid.local']);
    $news = \App\Models\News\News::create([
        'title' => 'A policy update', 'slug' => 'a-policy-update', 'content' => 'Update',
        'summary' => 'Update', 'published' => now(), 'visible' => true, 'user_id' => $author->id,
        'email_level' => \App\Models\News\News::EMAIL_SUBSCRIBERS,
    ]);

    (new \App\Jobs\ProcessArticlePublishing($news, controllersOnly: true))->handle();
    Notification::assertSentTo($subscriber, \App\Notifications\News::class);
    foreach ([$author, $unsubscribed, $deleted, $placeholder] as $excluded) {
        Notification::assertNotSentTo($excluded, \App\Notifications\News::class);
    }
});

it('shares the feed across scheduler commands and keeps distinct sessions for a callsign change', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);
    $now = now()->setMinute(15)->startOfMinute();
    $this->travelTo($now);

    $user = User::factory()->create();
    $member = RosterMember::create([
        'cid' => $user->id, 'user_id' => $user->id, 'full_name' => 'Controller',
        'status' => 'home', 'visit' => 0, 'active' => true, 'currency' => 0,
    ]);
    $old = \App\Models\Network\MonitoredPosition::create(['identifier' => 'CYVR_GND']);
    $previous = \App\Models\Network\SessionLog::create([
        'cid' => $user->id, 'roster_member_id' => $member->id, 'callsign' => 'CYVR_GND',
        'monitored_position_id' => $old->id, 'emails_sent' => 0, 'session_start' => $now->copy()->subHour(),
    ]);

    $feed = 'https://data.vatsim.net/v3/vatsim-data.json';
    \Illuminate\Support\Facades\Cache::put('vatsim-datafeed-url', $feed, 60);
    \Illuminate\Support\Facades\Http::fake([$feed => \Illuminate\Support\Facades\Http::response(['controllers' => [[
        'cid' => $user->id, 'callsign' => 'CYVR_TWR', 'facility' => 4,
        'logon_time' => $now->toIso8601String(),
    ]]])]);

    $this->artisan('vancouver:activity')->assertSuccessful();
    $this->artisan('vancouver:cachevatsim')->assertSuccessful();
    \Illuminate\Support\Facades\Http::assertSentCount(1);

    expect($previous->fresh()->session_end)->not->toBeNull()
        ->and((float) $member->fresh()->currency)->toBe(1.0);
    $this->assertDatabaseHas('session_logs', ['callsign' => 'CYVR_TWR', 'cid' => $user->id, 'session_end' => null]);

    $this->artisan('vancouver:currency')->assertSuccessful();
    $this->assertModelExists($previous);
    $this->assertDatabaseHas('session_logs', ['callsign' => 'CYVR_TWR', 'cid' => $user->id, 'session_end' => null]);
    expect((float) $member->fresh()->currency)->toBe(0.0);
});
