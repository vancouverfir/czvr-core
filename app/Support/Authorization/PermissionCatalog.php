<?php

namespace App\Support\Authorization;

final class PermissionCatalog
{
    public const ROSTER_MANAGE = 'roster.manage';

    public const TRAINING_MANAGE = 'training.manage';

    public const TRAINING_INSTRUCT = 'training.instruct';

    public const TRAINING_ADMINISTER = 'training.administer';

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

    /** @return array<string, array{label: string, group: string, description: string}> */
    public static function all(): array
    {
        return [
            self::ROSTER_MANAGE => ['label' => 'Manage controller roster', 'group' => 'Controllers', 'description' => 'View, add, edit, and remove controller roster entries.'],
            self::TRAINING_MANAGE => ['label' => 'Manage training', 'group' => 'Controllers', 'description' => 'Manage students, instructors, wait times, and training records.'],
            self::TRAINING_INSTRUCT => ['label' => 'Instructor tools', 'group' => 'Controllers', 'description' => 'Instructors page, staff comments and activity on students, waitlist emails and waitlist order.'],
            self::TRAINING_ADMINISTER => ['label' => 'Training administration', 'group' => 'Controllers', 'description' => 'Delete students and edit the training wait time.'],
            self::INSTRUCTING_SESSIONS_MANAGE => ['label' => 'Manage instructing sessions', 'group' => 'Controllers', 'description' => 'Create, edit, and cancel instructing sessions.'],
            self::BOOKINGS_MANAGE => ['label' => 'Manage all bookings', 'group' => 'Controllers', 'description' => 'Edit or delete any controller booking.'],
            self::ATC_RESOURCES_MANAGE => ['label' => 'Manage ATC resources', 'group' => 'Controllers', 'description' => 'Access, upload, and remove controller training resources.'],
            self::EVENTS_MANAGE => ['label' => 'Manage events', 'group' => 'Operations', 'description' => 'Create and edit events and manage controller applications.'],
            self::TICKETS_MANAGE => ['label' => 'Manage staff tickets', 'group' => 'Operations', 'description' => 'Access, reply to, hold, reopen, and close staff tickets.'],
            self::FEEDBACK_MANAGE => ['label' => 'Manage submitted feedback', 'group' => 'Operations', 'description' => 'Access, approve, edit, and delete submitted feedback.'],
            self::NEWS_MANAGE => ['label' => 'Manage news', 'group' => 'Content', 'description' => 'Create, edit, publish, and delete news articles.'],
            self::POLICIES_MANAGE => ['label' => 'Manage policies', 'group' => 'Content', 'description' => 'Access private policies and create, edit, and delete policy documents and sections.'],
            self::MEETING_MINUTES_MANAGE => ['label' => 'Manage meeting minutes', 'group' => 'Content', 'description' => 'Upload and delete meeting minutes.'],
            self::USERS_MANAGE => ['label' => 'Manage users', 'group' => 'Administration', 'description' => 'Search users and edit their accounts and profile data.'],
            self::ROLES_MANAGE => ['label' => 'Manage roles and permissions', 'group' => 'Administration', 'description' => 'Create roles, choose permissions, and assign access.'],
            self::STAFF_MANAGE => ['label' => 'Edit staff directory', 'group' => 'Administration', 'description' => 'Add, edit, and remove staff directory entries.'],
            self::SITE_SETTINGS_MANAGE => ['label' => 'Manage site settings', 'group' => 'Administration', 'description' => 'Change site information, email contacts, banners, and homepage images.'],
            self::AUDIT_LOG_ACCESS => ['label' => 'Access audit log', 'group' => 'Administration', 'description' => 'Access the site administration audit trail.'],
            self::NETWORK_MANAGE => ['label' => 'Manage network data', 'group' => 'Administration', 'description' => 'Access and configure monitored network positions.'],
        ];
    }

    /** @return array<string, array<string, array{label: string, group: string, description: string}>> */
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
