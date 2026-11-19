<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unique('id');
            $table->string('email')->unique();
            $table->rememberToken();
            $table->string('fname');
            $table->string('lname');
            $table->integer('rating_id')->nullable();
            $table->string('rating_short')->nullable();
            $table->string('rating_long')->nullable();
            $table->string('rating_GRP')->nullable();
            $table->dateTime('reg_date')->nullable();
            $table->string('region_code')->nullable();
            $table->string('region_name')->nullable();
            $table->string('division_code')->nullable();
            $table->string('division_name')->nullable();
            $table->string('subdivision_code')->nullable();
            $table->string('subdivision_name')->nullable();
            $table->integer('gdpr_subscribed_emails')->default(0);
            $table->boolean('deleted')->default(false);
            $table->integer('init')->default(0);
            $table->string('avatar')->default('/img/default-profile-img.jpg');
            $table->longText('bio')->nullable();
            $table->tinyInteger('display_cid_only')->default(0);
            $table->string('display_fname')->nullable();
            $table->tinyInteger('display_last_name')->default(1);
            $table->bigInteger('discord_user_id')->nullable();
            $table->bigInteger('discord_dm_channel_id')->nullable();
            $table->integer('avatar_mode')->default(0);
            $table->tinyInteger('used_connect')->default(0);
            $table->integer('visitor')->default(0);
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->integer('secure')->nullable();
        });

        Schema::create('users_permissions', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('permission_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('users_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('staff_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->tinyInteger('can_receive_tickets')->default(1);
            $table->timestamps();
        });

        Schema::create('staff_member', function (Blueprint $table) {
            $table->increments('id');
            $table->text('position');
            $table->text('group');
            $table->text('description')->nullable();
            $table->text('email')->nullable();
            $table->timestamps();
            $table->text('shortform');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('staff_groups');
        });

        Schema::create('instructors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('qualification');
            $table->timestamps();
            $table->string('email');
            $table->boolean('active')->default(true);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('times')->nullable();
            $table->json('availability')->nullable();
            $table->integer('position')->nullable();
            $table->integer('status');
            $table->integer('instructor_id')->unsigned()->nullable();
            $table->string('consolidation_rating', 8)->nullable();
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->string('renewal_token')->nullable();
            $table->dateTime('renewed_at')->nullable();
            $table->dateTime('renewal_notified_at')->nullable();
            $table->timestamp('renewal_reminded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('instructing_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('instructor_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')->references('id')->on('students');
            $table->text('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->longText('instructor_comment')->nullable();
            $table->timestamps();
        });

        Schema::create('student_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // The user who wrote the note; NULL is a System note
            $table->unsignedInteger('author_cid')->nullable();
            $table->foreign('author_cid')->references('id')->on('users')->nullOnDelete();
            $table->text('title');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('monitored_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->timestamps();
        });

        Schema::create('roster', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid')->unsigned()->unique('cid');
            $table->foreign('cid')->references('id')->on('users');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('full_name');
            $table->string('status');
            $table->tinyInteger('active')->default(1);
            $table->double('currency')->nullable();
            $table->double('rating_hours')->nullable();
            $table->integer('delgnd')->default(0);
            $table->integer('delgnd_t2')->default(0);
            $table->integer('twr')->default(0);
            $table->integer('twr_t2')->default(0);
            $table->integer('dep')->default(0);
            $table->integer('app')->default(0);
            $table->integer('app_t2')->default(0);
            $table->integer('ctr')->default(0);
            $table->integer('fss')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('visit');
            $table->timestamps();
        });

        Schema::create('session_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('roster_member_id')->unsigned()->nullable();
            $table->foreign('roster_member_id')->references('id')->on('roster')->onDelete('cascade');
            $table->integer('cid');
            $table->dateTime('session_start');
            $table->dateTime('session_end')->nullable();
            $table->integer('monitored_position_id')->unsigned();
            $table->foreign('monitored_position_id')->references('id')->on('monitored_positions');
            $table->text('callsign')->nullable();
            $table->double('duration')->nullable();
            $table->integer('emails_sent');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->dateTime('start_timestamp');
            $table->dateTime('end_timestamp');
            $table->integer('user_id')->unsigned();
            $table->longText('description');
            $table->string('image_url')->nullable();
            $table->tinyInteger('controller_applications_open')->default(0);
            $table->string('departure_icao')->nullable();
            $table->string('arrival_icao')->nullable();
            $table->string('slug');
            $table->timestamp('subscribers_notified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('event_confirms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('start_timestamp');
            $table->dateTime('end_timestamp');
            $table->mediumText('airport')->nullable();
            $table->timestamps();
        });

        Schema::create('event_controller_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('start_availability_timestamp');
            $table->dateTime('end_availability_timestamp');
            $table->mediumText('airport')->nullable();
            $table->text('comments')->nullable();
            $table->dateTime('submission_timestamp');
            $table->timestamps();
        });

        Schema::create('event_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id')->nullable();
            $table->mediumText('position');
            $table->timestamps();
        });

        Schema::create('event_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('title')->nullable();
            $table->longText('content');
            $table->timestamp('created_timestamp')->useCurrent()->useCurrentOnUpdate();
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('event_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('deleted_at')->nullable();
            $table->text('subject');
            $table->longText('content');
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('show_author')->default(0);
            $table->text('image')->nullable();
            $table->longText('content')->nullable();
            $table->text('summary')->nullable();
            $table->dateTime('published');
            $table->dateTime('edited')->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->integer('email_level')->default(0);
            $table->tinyInteger('certification')->default(0);
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('policies_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->text('section_name');
            $table->timestamps();
        });

        Schema::create('policies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_id')->unsigned()->nullable();
            $table->foreign('section_id')->references('id')->on('policies_sections');
            $table->text('name');
            $table->text('details');
            $table->text('link');
            $table->integer('embed')->default(0);
            $table->integer('author')->unsigned();
            $table->foreign('author')->references('id')->on('users');
            $table->date('releaseDate');
            $table->timestamps();
            $table->integer('staff_only')->default(0);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ticket_id')->unique();
            $table->text('title');
            $table->text('message');
            $table->integer('status')->default(0);
            $table->dateTime('submission_time');
            $table->timestamps();
            $table->integer('staff_member_id')->unsigned();
            $table->foreign('staff_member_id')->references('id')->on('staff_member');
            $table->integer('staff_member_cid');
        });

        Schema::create('ticket_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ticket_id');
            $table->foreign('ticket_id')->references('ticket_id')->on('tickets');
            $table->text('message');
            $table->dateTime('submission_time');
            $table->timestamps();
        });

        Schema::create('controller_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('deleted_at')->nullable();
            $table->integer('controller_cid')->unsigned();
            $table->foreign('controller_cid')->references('id')->on('users');
            $table->text('position');
            $table->longText('content');
            $table->tinyInteger('approval')->default(0);
            $table->timestamps();
        });

        Schema::create('website_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('deleted_at')->nullable();
            $table->text('subject');
            $table->longText('content');
            $table->timestamps();
        });

        Schema::create('atc_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('title');
            $table->string('font_awesome');
            $table->text('description')->nullable();
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('audit_log_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('action');
            $table->integer('affected_id')->unsigned();
            $table->foreign('affected_id')->references('id')->on('users');
            $table->dateTime('time');
            $table->integer('private')->default(0);
            $table->timestamps();
        });

        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('title');
            $table->string('link');
            $table->timestamps();
        });

        Schema::create('core_info', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sys_name');
            $table->text('release');
            $table->text('sys_build');
            $table->text('copyright_year');
            $table->timestamps();
            $table->text('banner')->nullable();
            $table->text('bannerMode')->nullable();
            $table->text('bannerLink')->nullable();
            $table->text('emailfirchief');
            $table->text('emaildepfirchief');
            $table->text('emailcinstructor');
            $table->text('emaileventc');
            $table->text('emailfacilitye');
            $table->text('emailwebmaster');
        });

        Schema::create('homepage_images', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->text('credit');
            $table->text('css')->nullable();
            $table->timestamps();
        });

        Schema::create('training_waittimes', function (Blueprint $table) {
            $table->id();
            $table->text('wait_length');
            $table->text('colour');
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_cid');
            $table->foreign('user_cid')->references('id')->on('users');
            $table->boolean('is_instructor')->nullable();
            $table->bigInteger('is_local');
            $table->bigInteger('is_radar');
            $table->bigInteger('is_enroute');
            $table->timestamps();
        });

        Schema::create('student_label', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fa_icon');
            $table->string('color');
            $table->boolean('visible_home');
            $table->boolean('exclusive');
            $table->boolean('visitor');
            $table->unsignedTinyInteger('new_status')->nullable();
            $table->timestamps();
        });

        Schema::create('student_interactive_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_label_id');
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            $table->timestamps();
        });

        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained()->onDelete('cascade');
            $table->text('item');
            $table->timestamps();
        });

        Schema::create('student_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });

        Schema::create('label_checklist_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('student_label');
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->string('tier_type');
            $table->timestamps();
        });

        Schema::create('label_checklist_visitor_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('student_label');
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->string('tier_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_checklist_visitor_map');
        Schema::dropIfExists('label_checklist_map');
        Schema::dropIfExists('student_checklist_items');
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('checklists');
        Schema::dropIfExists('student_interactive_labels');
        Schema::dropIfExists('student_label');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('training_waittimes');
        Schema::dropIfExists('homepage_images');
        Schema::dropIfExists('core_info');
        Schema::dropIfExists('meeting_minutes');
        Schema::dropIfExists('audit_log_entries');
        Schema::dropIfExists('atc_resources');
        Schema::dropIfExists('website_feedback');
        Schema::dropIfExists('controller_feedback');
        Schema::dropIfExists('ticket_reply');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('policies_sections');
        Schema::dropIfExists('news');
        Schema::dropIfExists('event_feedback');
        Schema::dropIfExists('event_updates');
        Schema::dropIfExists('event_positions');
        Schema::dropIfExists('event_controller_applications');
        Schema::dropIfExists('event_confirms');
        Schema::dropIfExists('events');
        Schema::dropIfExists('session_logs');
        Schema::dropIfExists('roster');
        Schema::dropIfExists('monitored_positions');
        Schema::dropIfExists('student_notes');
        Schema::dropIfExists('instructing_sessions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('staff_member');
        Schema::dropIfExists('staff_groups');
        Schema::dropIfExists('roles_permissions');
        Schema::dropIfExists('users_roles');
        Schema::dropIfExists('users_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('users');
    }
};
