<?php

namespace Calendar\Library;

use Craft\Calendar_CalendarModel;
use Craft\Calendar_CalendarsService;
use Craft\Craft;
use Craft\HttpException;

class PermissionsHelper
{
    const PERMISSION_CALENDARS        = 'calendar-manageCalendars';
    const PERMISSION_CREATE_CALENDARS = 'calendar-createCalendars';
    const PERMISSION_EDIT_CALENDARS   = 'calendar-editCalendars';
    const PERMISSION_DELETE_CALENDARS = 'calendar-deleteCalendars';

    const PERMISSION_EVENTS         = 'calendar-manageEvents';
    const PERMISSION_EVENTS_FOR     = 'calendar-manageEventsFor';
    const PERMISSION_EVENTS_FOR_ALL = 'calendar-manageEventsFor:all';

    const PERMISSION_SETTINGS = 'calendar-settings';

    /**
     * Checks a given permission for the currently logged in user
     *
     * @param string $permissionName
     * @param bool   $checkForNested - see nested permissions for matching permission name root
     *
     * @return bool
     */
    public static function checkPermission($permissionName, $checkForNested = false)
    {
        $user           = \Craft\craft()->getUser();
        $permissionName = strtolower($permissionName);

        if (self::permissionsEnabled()) {
            if ($checkForNested) {
                $permissionList = \Craft\craft()->userPermissions->getPermissionsByUserId($user->getId());
                foreach ($permissionList as $permission) {
                    if (strpos($permission, $permissionName) === 0) {
                        return true;
                    }
                }
            }

            return $user->checkPermission($permissionName);
        } else {
            return self::isAdmin();
        }
    }

    /**
     * @param string $permissionName
     *
     * @return null
     * @throws \Craft\HttpException
     */
    public static function requirePermission($permissionName)
    {
        $user           = \Craft\craft()->getUser();
        $permissionName = strtolower($permissionName);

        return $user->requirePermission($permissionName);
    }

    /**
     * Fetches all nested allowed permission IDs from a nested permission set
     *
     * @param string $permissionName
     *
     * @return array|bool
     */
    public static function getNestedPermissionIds($permissionName)
    {
        $user           = \Craft\craft()->getUser();
        $permissionName = strtolower($permissionName);
        $idList         = array();

        if (self::permissionsEnabled()) {
            $permissionList = \Craft\craft()->userPermissions->getPermissionsByUserId($user->getId());
            foreach ($permissionList as $permission) {
                if (strpos($permission, $permissionName) === 0) {
                    list($name, $id) = explode(':', $permission);

                    $idList[] = $id;
                }
            }

            return $idList;
        }

        return self::isAdmin();
    }

    /**
     * @param Calendar_CalendarModel $calendar
     *
     * @throws HttpException
     */
    public static function requireCalendarEditPermissions(Calendar_CalendarModel $calendar)
    {
        if (!self::canEditCalendar($calendar)) {
            throw new HttpException(403);
        }
    }

    /**
     * @param Calendar_CalendarModel $calendar
     *
     * @return bool
     */
    public static function canEditCalendar(Calendar_CalendarModel $calendar = null)
    {
        $canManageAll = PermissionsHelper::checkPermission(PermissionsHelper::PERMISSION_EVENTS_FOR_ALL);

        if ($canManageAll) {
            return true;
        }

        if ($calendar === null) {
            return false;
        }

        return PermissionsHelper::checkPermission(
            PermissionsHelper::prepareNestedPermission(
                PermissionsHelper::PERMISSION_EVENTS_FOR,
                $calendar->id
            )
        );
    }

    /**
     * Combines a nested permission with ID
     *
     * @param string $permissionName
     * @param int    $id
     *
     * @return string
     */
    public static function prepareNestedPermission($permissionName, $id)
    {
        return $permissionName . ':' . $id;
    }

    /**
     * Returns true if the currently logged in user is an admin
     *
     * @return bool
     */
    public static function isAdmin()
    {
        return \Craft\craft()->getUser()->isAdmin();
    }

    /**
     * @return bool
     */
    private static function permissionsEnabled()
    {
        $edition = \Craft\craft()->getEdition();

        return in_array($edition, array(Craft::Pro, Craft::Client));
    }
}
