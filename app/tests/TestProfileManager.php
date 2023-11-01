<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website http://www.znetdk.fr
 * Copyright (C) 2023 Pascal MARTINEZ (contact@znetdk.fr)
 * License GNU GPL http://www.gnu.org/licenses/gpl-3.0.html GNU GPL
 * --------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * --------------------------------------------------------------------
 * Test of the ProfileManager ZnetDK core class
 *
 * File version: 1.0
 * Last update: 04/01/2023
 */

namespace app\tests;
use app\TestCase;
class TestProfileManager extends TestCase {

    // Called once before testing all methods
    static protected function beforeAllTests() {
        self::emptyTable('zdk_profile_menus');
        self::emptyTable('zdk_profile_rows');
        self::emptyTable('zdk_user_profiles');
    }

    // Called once after testing all methods
    static protected function afterAllTests() {
        self::emptyTable('zdk_profile_menus');
        self::emptyTable('zdk_profile_rows');
        self::emptyTable('zdk_user_profiles');
        self::emptyTable('zdk_profiles');
    }


    static protected function getAllProfiles() {
        self::setSqlData('zdk_profiles', [
            ['profile_id' => 1, 'profile_name' => 'Profile A', 'profile_description' => 'Description of profile A'],
            ['profile_id' => 2, 'profile_name' => 'Profile B', 'profile_description' => 'Description of profile B']
        ]);

        $profiles = [];
        $count = \ProfileManager::getAllProfiles(0, 10, 'profile_id', $profiles);
        return self::areValuesEqual(
            2, $count,
            '1', $profiles[0]['profile_id'],
            'Profile A', $profiles[0]['profile_name'],
            'Description of profile A', $profiles[0]['profile_description'],
            '2', $profiles[1]['profile_id'],
            'Profile B', $profiles[1]['profile_name'],
            'Description of profile B', $profiles[1]['profile_description']
        );
    }

    static protected function getById() {
        $profile = \ProfileManager::getById(2);
        return self::areValuesEqual(
            '2', $profile['profile_id'],
            'Profile B', $profile['profile_name'],
            'Description of profile B', $profile['profile_description']
        );
    }

    static protected function getProfileInfos() {
        $profile = \ProfileManager::getProfileInfos('Profile B');
        return self::areValuesEqual(
            '2', $profile['profile_id'],
            'Profile B', $profile['profile_name'],
            'Description of profile B', $profile['profile_description']
        );
    }

    static protected function storeProfile() {
        self::setSqlData('zdk_profile_menus', []);
        $rowId = \ProfileManager::storeProfile([
            'profile_name' => 'Profile C',
            'profile_description' => 'Description of profile C'
        ], ['menuItem1', 'menuItem2']);
        $newProfile = \ProfileManager::getById($rowId);
        return self::areValuesEqual(
            $rowId, $newProfile['profile_id'],
            'Profile C', $newProfile['profile_name'],
            'Description of profile C', $newProfile['profile_description']
        );
    }

    static protected function removeProfile() {
        \ProfileManager::removeProfile(3);
        $removedRow = \ProfileManager::getById(3);
        return self::areValuesEqual(FALSE, $removedRow);
    }

    static protected function isProfileGrantedToUsers() {
        self::setSqlData('zdk_user_profiles', []);
        $isGranted = \ProfileManager::isProfileGrantedToUsers(1);
        return self::areValuesEqual(FALSE, $isGranted);
    }

    static protected function isProfileAssociatedToRows() {
        self::setSqlData('zdk_profile_rows', []);
        $isAssociated = \ProfileManager::isProfileAssociatedToRows(1);
        return self::areValuesEqual(FALSE, $isAssociated);
    }

    static protected function getProfiles() {
        $profiles = \ProfileManager::getProfiles();
        return self::areValuesEqual(
            2, count($profiles),
            '1', $profiles[0]['value'],
            'Profile A', $profiles[0]['label'],
            '2', $profiles[1]['value'],
            'Profile B', $profiles[1]['label']
        );
    }

    static protected function removeProfilesRow() {
        self::setSqlData('zdk_profile_rows', [
            ['profile_rows_id' => 1, 'profile_id' => 1, 'table_name' => 'mytable', 'row_id' => 1]
        ]);
        $rowCount = \ProfileManager::removeProfilesRow('mytable', 1);
        return self::areValuesEqual(1, $rowCount);
    }

    static protected function isMenuItemSetForProfile() {
        self::setSqlData('zdk_profile_menus', [
            ['profile_menus_id' => 1, 'profile_id' => 1, 'menu_id' => 'menuItem1'],
            ['profile_menus_id' => 2, 'profile_id' => 1, 'menu_id' => 'menuItem2']
        ]);        
        $isMenuItemSet3 = \ProfileManager::isMenuItemSetForProfile('Profile A', 'menuItem3');
        $isMenuItemSet1 = \ProfileManager::isMenuItemSetForProfile('Profile A', 'menuItem1');
        return self::areValuesEqual(
                FALSE, $isMenuItemSet3,
                TRUE, $isMenuItemSet1
        );
    }
}
