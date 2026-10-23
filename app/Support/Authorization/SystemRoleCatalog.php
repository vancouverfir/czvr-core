<?php

namespace App\Support\Authorization;

final class SystemRoleCatalog
{
    public const CONTROLLER = 'controller';

    public const MENTOR = 'mentor';

    public const INSTRUCTOR = 'instructor';

    public const STAFF = 'staff';

    public const ADMINISTRATOR = 'administrator';

    /** @return array<string, string> */
    public static function all(): array
    {
        return [
            self::CONTROLLER => 'Controller',
            self::MENTOR => 'Mentor',
            self::INSTRUCTOR => 'Instructor',
            self::STAFF => 'Staff',
            self::ADMINISTRATOR => 'Administrator',
        ];
    }

    /** @return list<string> */
    public static function protectedSlugs(): array
    {
        return array_keys(self::all());
    }

    /** @return list<string> */
    public static function administratorOnlySlugs(): array
    {
        return [self::STAFF, self::ADMINISTRATOR];
    }

    /** @return list<string> */
    public static function defaultPermissions(): array
    {
        $mentor = [
            PermissionCatalog::TRAINING_MANAGE,
            PermissionCatalog::INSTRUCTING_SESSIONS_MANAGE,
        ];
        $instructor = [
            ...$mentor,
            PermissionCatalog::TRAINING_INSTRUCT,
            PermissionCatalog::TRAINING_WAITLIST_MANAGE,
            PermissionCatalog::ROSTER_MANAGE,
        ];
        $staff = [
            ...$instructor,
            PermissionCatalog::TRAINING_ADMINISTER,
            PermissionCatalog::TRAINING_STUDENTS_DELETE,
            PermissionCatalog::ATC_RESOURCES_MANAGE,
            PermissionCatalog::MEETING_MINUTES_MANAGE,
            PermissionCatalog::BOOKINGS_MANAGE,
            PermissionCatalog::EVENTS_MANAGE,
            PermissionCatalog::NEWS_MANAGE,
            PermissionCatalog::FEEDBACK_MANAGE,
            PermissionCatalog::TICKETS_MANAGE,
            PermissionCatalog::POLICIES_MANAGE,
            PermissionCatalog::USERS_MANAGE,
        ];

        return [
            self::CONTROLLER => [],
            self::MENTOR => $mentor,
            self::INSTRUCTOR => $instructor,
            self::STAFF => $staff,
            self::ADMINISTRATOR => PermissionCatalog::slugs(),
        ];
    }
}
