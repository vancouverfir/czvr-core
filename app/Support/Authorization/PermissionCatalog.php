<?php

namespace App\Support\Authorization;

final class PermissionCatalog
{
    public const ROSTER_MANAGE = 'roster.manage';

    public const TRAINING_MANAGE = 'training.manage';

    public const TRAINING_INSTRUCT = 'training.instruct';

    public const TRAINING_WAITLIST_MANAGE = 'training.waitlist.manage';

    public const TRAINING_ADMINISTER = 'training.administer';

    public const TRAINING_STUDENTS_DELETE = 'training.students.delete';

    public const INSTRUCTING_SESSIONS_MANAGE = 'instructing-sessions.manage';

    public const BOOKINGS_MANAGE = 'bookings.manage';

    public const EVENTS_MANAGE = 'events.manage';

    public const NEWS_MANAGE = 'news.manage';

    public const POLICIES_MANAGE = 'policies.manage';

    public const ATC_RESOURCES_MANAGE = 'atc-resources.manage';

    public const FEEDBACK_MANAGE = 'feedback.manage';

    public const TICKETS_MANAGE = 'tickets.manage';

    public const USERS_MANAGE = 'users.manage';

    public const ROLES_MANAGE = 'roles.manage';

    public const STAFF_MANAGE = 'staff.manage';

    public const SITE_SETTINGS_MANAGE = 'site-settings.manage';

    public const AUDIT_LOG_ACCESS = 'audit-log.access';

    public const NETWORK_MANAGE = 'network.manage';

    public const MEETING_MINUTES_MANAGE = 'meeting-minutes.manage';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::USERS_MANAGE => ['label' => 'Manage users', 'group' => 'Administration', 'description' => 'Search users and open their admin profile: certificate names, notes, avatar and bio resets, emailing them, and deleting the account!'],
            self::ROLES_MANAGE => ['label' => 'Manage roles and permissions', 'group' => 'Administration', 'description' => 'Create, edit, and delete roles, and give users roles and individual permissions!'],
            self::STAFF_MANAGE => ['label' => 'Edit staff directory', 'group' => 'Administration', 'description' => 'Add, edit, and remove people on the public staff page!'],
            self::SITE_SETTINGS_MANAGE => ['label' => 'Manage site settings', 'group' => 'Administration', 'description' => 'Change site information, staff email addresses, the site banner, and homepage images!'],
            self::AUDIT_LOG_ACCESS => ['label' => 'Access audit log', 'group' => 'Administration', 'description' => 'Read the audit log of administrative changes!'],
            self::NETWORK_MANAGE => ['label' => 'Manage network data', 'group' => 'Administration', 'description' => 'Open Network data (controller activity, position sessions, statistics) and add, edit, and remove monitored positions!'],
            self::TRAINING_MANAGE => ['label' => 'Access Training Centre', 'group' => 'Training Centre', 'description' => 'View the student list, waitlist, and individual students, add new students, assign instructors and labels, complete checklists, promote, start recertification, complete training, and preview a student\'s view!'],
            self::TRAINING_INSTRUCT => ['label' => 'Manage staff comments', 'group' => 'Training Centre', 'description' => 'Open the instructors page, read and add staff comments, and see student activity!', 'requires' => [self::TRAINING_MANAGE]],
            self::TRAINING_WAITLIST_MANAGE => ['label' => 'Manage waitlist order', 'group' => 'Training Centre', 'description' => 'Reorder the waitlists, see waitlisted students\' email and Discord, and mark them contacted!', 'requires' => [self::TRAINING_MANAGE]],
            self::INSTRUCTING_SESSIONS_MANAGE => ['label' => 'Manage instructing sessions', 'group' => 'Training Centre', 'description' => 'Open the instructing sessions calendar and create, edit, and cancel sessions!', 'requires' => [self::TRAINING_MANAGE]],
            self::TRAINING_ADMINISTER => ['label' => 'Edit wait times', 'group' => 'Training Centre', 'description' => 'Edit the estimated wait time on the Training FAQ page!'],
            self::TRAINING_STUDENTS_DELETE => ['label' => 'Delete students', 'group' => 'Training Centre', 'description' => 'Delete a student and their training records, from the Delete Student button on the student page!', 'requires' => [self::TRAINING_MANAGE]],
            self::NEWS_MANAGE => ['label' => 'Manage news', 'group' => 'Content', 'description' => 'Create, edit, publish, and delete news articles, and see unpublished ones!'],
            self::POLICIES_MANAGE => ['label' => 'Manage policies', 'group' => 'Content', 'description' => 'See staff-only policies and create, edit, and delete policies and policy sections!'],
            self::MEETING_MINUTES_MANAGE => ['label' => 'Manage meeting minutes', 'group' => 'Content', 'description' => 'Upload and delete meeting minutes!'],
            self::ROSTER_MANAGE => ['label' => 'Manage controller roster', 'group' => 'Controllers', 'description' => 'Add, edit, and remove controllers on the roster, see ATC-inactive members, and edit certifications!'],
            self::BOOKINGS_MANAGE => ['label' => 'Manage all bookings', 'group' => 'Controllers', 'description' => 'Edit or delete anyone\'s booking!'],
            self::ATC_RESOURCES_MANAGE => ['label' => 'Manage ATC resources', 'group' => 'Controllers', 'description' => 'Upload, edit, and remove ATC resources!'],
            self::EVENTS_MANAGE => ['label' => 'Manage events', 'group' => 'Operations', 'description' => 'Create, edit, and delete events and their updates, confirm and remove controller applications, and edit the West Coast Madness map!'],
            self::TICKETS_MANAGE => ['label' => 'Manage staff tickets', 'group' => 'Operations', 'description' => 'See every staff ticket and reply to, hold, reopen, and close them!'],
            self::FEEDBACK_MANAGE => ['label' => 'Manage submitted feedback', 'group' => 'Operations', 'description' => 'Read submitted feedback and approve, deny, edit, or delete it!'],
        ];
    }

    /** @return array<string, array<string, array{label: string, group: string, description: string, requires?: list<string>}>> */
    public static function grouped(): array
    {
        return collect(self::all())->groupBy('group', preserveKeys: true)->all();
    }

    /** @return list<string> */
    public static function slugs(): array
    {
        return array_keys(self::all());
    }
}
