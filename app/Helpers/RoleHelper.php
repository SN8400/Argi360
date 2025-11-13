<?php
namespace App\Helpers;

class RoleHelper
{
    public static function getGroupByRole($role)
    {
        foreach (config('role_groups.roles') as $group => $roles) {
            if (in_array($role, $roles)) {
                return $group;
            }
        }
        return null;
    }

    public static function getDepartmentByRole($role)
    {
        foreach (config('role_groups.departments') as $department => $departments) {
            if (in_array($role, $departments)) {
                return $department;
            }
        }
        return null;
    }

    public static function getLayoutByRole($role)
    {
        $group = self::getGroupByRole($role);
        return $group ? config('role_groups.layouts')[$group] : 'layouts.app';
    }
}
