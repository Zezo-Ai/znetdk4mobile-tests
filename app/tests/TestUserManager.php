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
 * Test of the UserManager ZnetDK core class
 *
 * File version: 1.0
 * Last update: 09/09/2023
 */

namespace app\tests;
use app\TestCase;
class TestUserManager extends TestCase {

    // Called once before testing all methods
    static protected function beforeAllTests() {
        self::emptyTable('zdk_user_profiles');
        self::emptyTable('zdk_profiles');
        self::emptyTable('zdk_users');
        // Profiles
        self::setSqlData('zdk_profiles', [
            ['profile_id' => 1, 'profile_name' => 'Profile A', 'profile_description' => 'Description of profile A'],
            ['profile_id' => 2, 'profile_name' => 'Profile B', 'profile_description' => 'Description of profile B']
        ]);
        // Users
        self::setSqlData('zdk_users', [
            ['user_id' => 1, 'login_name' => 'userlogin1', 'login_password' => 'fakepassword1', 'expiration_date' => '2029-01-01', 'user_name' => 'Fake user 1', 'user_email' => 'fakeuser1@fakedomain.com', 'user_phone' => '1234567890', 'notes' => 'Be aware 1', 'full_menu_access' => '1', 'user_enabled' => '1'],
            ['user_id' => 2, 'login_name' => 'userlogin2', 'login_password' => 'fakepassword2', 'expiration_date' => '2029-01-01', 'user_name' => 'Fake user 2', 'user_email' => 'fakeuser2@fakedomain.com', 'user_phone' => '0987654321', 'notes' => 'Be aware 2', 'full_menu_access' => '0', 'user_enabled' => '0']
        ]);
        // User's profiles
        self::setSqlData('zdk_user_profiles', [
            ['user_profile_id' => 1, 'user_id' => 1, 'profile_id' => 1],
            ['user_profile_id' => 2, 'user_id' => 1, 'profile_id' => 2],
            ['user_profile_id' => 3, 'user_id' => 2, 'profile_id' => 2]
        ]);
    }

    // Called once after testing all methods
    static protected function afterAllTests() {
        self::emptyTable('zdk_user_profiles');
        self::emptyTable('zdk_profiles');
        self::emptyTable('zdk_users');
    }

    static protected function changeUserPassword() {
        return self::NOT_TESTED;
    }

    static protected function disableUser() {
        return self::NOT_TESTED;
    }

    static protected function getAllUsers() {
        return self::NOT_TESTED;
    }

    static protected function getFoundKeywords() {
        return self::NOT_TESTED;
    }

    static protected function getGrantedMenuItemsToUser () {
        return self::NOT_TESTED;
    }

    static protected function getResetPasswordConfirmationUrl() {
        return self::NOT_TESTED;
    }

    static protected function getSearchedUsers() {
        return self::NOT_TESTED;
    }

    static protected function getUserEmail() {
        return self::NOT_TESTED;
    }

    static protected function getUserIdByEmail() {
        return self::NOT_TESTED;
    }

    static protected function getUserInfos() {
        return self::NOT_TESTED;
    }
    
    static protected function getUserInfosByEmail() {
        return self::NOT_TESTED;
    }
    
    static protected function getUserInfosById() {
        return self::NOT_TESTED;
    }
    
    static protected function getUserInfosByName() {
        return self::NOT_TESTED;
    }
    
    static protected function getUserName() {
        return self::NOT_TESTED;
    }
    
    static protected function getUserProfilesAsArray() {
        $userProfiles = \UserManager::getUserProfilesAsArray(1);
        return self::areValuesEqual(
                count($userProfiles), 2,
                $userProfiles[1], 'Profile A',
                $userProfiles[2], 'Profile B',
                array_keys($userProfiles)[0], 1,
                array_keys($userProfiles)[1], 2
        );
    }
    
    static protected function getUserProfiles() {
        $profileNamesAsList = '';
        $profileIDs = [];
        \UserManager::getUserProfiles(1, $profileNamesAsList, $profileIDs);
        return self::areValuesEqual(
            $profileNamesAsList, 'Profile A, Profile B',
            count($profileIDs), 2,
            $profileIDs[0], 1,
            $profileIDs[1], 2,
        );
    }
    
    static protected function getUsersHavingProfile() {
        $users = \UserManager::getUsersHavingProfile('Profile A');
        return count($users) === 1 
            && $users[0]['user_phone'] === '1234567890' 
            && $users[0]['notes'] === 'Be aware 1';
    }
    
    static protected function hasUserFullMenuAccess() {
        $hasFMAuser1 = \UserManager::hasUserFullMenuAccess('userlogin1');
        $hasFMAuser2 = \UserManager::hasUserFullMenuAccess('userlogin2');
        return self::areValuesEqual(
              $hasFMAuser1, TRUE,
              $hasFMAuser2, FALSE
        );
    }
    
    static protected function hasUserMenuItem() {
        return self::NOT_TESTED;
    }
    
    static protected function hasUserProfile() {
        return self::NOT_TESTED;
    }
    
    static protected function removeUser() {
        return self::NOT_TESTED;
    }
    
    static protected function resetPassword() {
        return self::NOT_TESTED;
    }
    
    static protected function storeUser() {
        return self::NOT_TESTED;
    }
    
}
