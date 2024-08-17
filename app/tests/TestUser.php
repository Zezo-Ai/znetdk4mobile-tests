<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website https://www.znetdk.fr
 * Copyright (C) 2024 Pascal MARTINEZ (contact@znetdk.fr)
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
 * Test of the User ZnetDK core class
 *
 * File version: 1.0
 * Last update: 08/15/2024
 */

namespace app\tests;
use app\TestCase;

/**
 * Description of TestUser
 *
 * @author Pascal
 */
class TestUser extends TestCase {

    // Called once before testing all methods
    static protected function beforeAllTests() {
        self::$context['credentials'] = self::addTestUser();
        // Extra profile
        $profileRow = [
            'profile_name' => 'Other profile',
            'profile_description' => 'Other profile for testing purpose'
        ];
        \ProfileManager::storeProfile($profileRow);
        self::$context['extraProfileName'] = 'Other profile';
        // Added users by the test cases
        self::$context['addedUsers'] = [];
    }

    // Called once after testing all methods
    static protected function afterAllTests() {
        // Added users by test cases are removed
        foreach (self::$context['addedUsers'] as $userID) {
            \UserManager::removeUser($userID);
        }
        // Test user removed
        self::removeTestUser();
        // Extra profile removed
        $infos = \ProfileManager::getProfileInfos(self::$context['extraProfileName']);
        if (is_array($infos)) {
            \ProfileManager::removeProfile($infos['profile_id']);
        }
    }

    /**
     * NORMAL: existing user id passed to the constructor.
     */
    static protected function construct() {
        try {
            new \User(self::$context['credentials']['id']);
            return self::SUCCESS;
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }
    /**
     * NORMAL: no argument passed to the constructor
     */
    static protected function construct2() {
        try {
            new \User();
            return self::SUCCESS;
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * ERROR: invalid user id passed to the constructor (URA-003).
     */
    static protected function construct3() {
        try {
            new \User(0);
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual($ex->getCode(), 'URA-003');
        }
    }

    /**
     * NORMAL: value set to a valid property name
     */
    static protected function set(){
        $user = new \User();
        try {
            $user->login_name = 'hello_guy';
            return self::areValuesEqual($user->login_name, 'hello_guy');
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * ERROR: value set to an invalid property name (URA-008)
     */
    static protected function set2(){
        $user = new \User();
        try {
            $user->unknownCol = 'a value';
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual($ex->getCode(), 'URA-008');
        }
    }

    /**
     * NORMAL: adds a new user with minimal property set and valid values
     */
    static protected function add(){
        global $users_notify_args;
        $users_notify_args = [];
        $newUser = new \User();
        $newUser->login_name = 'john_doe';
        $newUser->user_name = 'John DOE';
        $newUser->user_email = 'johndoe@myemail.xyz';
        try {
            $userId = $newUser->add();
            self::$context['addedUsers'][] = $userId;
            return self::areValuesEqual(
                $userId > 0, TRUE,
                $newUser->expiration_date, \General::getCurrentW3CDate(),
                $newUser->full_menu_access, 0,
                $newUser->user_enabled, 1,
                strlen($newUser->login_password) > 1, TRUE,
                strlen($newUser->getPasswordInClear()) > 1, TRUE,
                $newUser->login_password !== $newUser->getPasswordInClear(), TRUE,
                $users_notify_args[0], TRUE,
                $users_notify_args[1], $newUser->getPasswordInClear(),
                $users_notify_args[2]['user_id'], $userId,
                $users_notify_args[2]['login_name'], 'john_doe',
                $users_notify_args[2]['user_name'], 'John DOE',
                $users_notify_args[2]['user_email'], 'johndoe@myemail.xyz'
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * NORMAL: adds a new user with all properties set with valid values
     */
    static protected function add2(){
        $newUser = new \User();
        $expirationDate = new \DateTime();
        $expirationDate->add(new \DateInterval('P6M'));
        $newUser->login_name = 'anna_doe';
        $newUser->user_name = 'Anna DOE';
        $newUser->user_email = 'annadoe@myemail.xyz';
        $newUser->login_password = 'Password1';
        $newUser->expiration_date = $expirationDate->format('Y-m-d');
        $newUser->user_phone = '1234567890';
        $newUser->notes = 'Notes about Anna DOE';
        $newUser->full_menu_access = '0';
        $newUser->user_enabled = '1';
        try {
            $userId = $newUser->add();
            self::$context['addedUsers'][] = $userId;
            return self::areValuesEqual(
                $userId > 0, TRUE
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * ERROR: adds a new user with missing mandatory property (URA-007)
     */
    static protected function add3(){
        $newUser = new \User();
        $newUser->login_name = 'ali_mcbean';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-007'
            );
        }
    }

    /**
     * ERROR: adds a new user with login name too short (URA-009)
     */
    static protected function add4(){
        $newUser = new \User();
        $newUser->login_name = 'jd';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_name] ' . LC_MSG_ERR_LOGIN_BADLENGTH
            );
        }
    }

    /**
     * ERROR: adds a new user with invalid email address (URA-009)
     */
    static protected function add5(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[user_email] ' . LC_MSG_ERR_EMAIL_INVALID
            );
        }
    }

    /**
     * ERROR: adds a new user with password too short (URA-009)
     */
    static protected function add6(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        $newUser->login_password = 'Pass1';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_password] '
                    . LC_MSG_ERR_PASSWORD_INVALID . ' '
                    . LC_FORM_LBL_PASSWORD_EXPECTED_LENGTH
            );
        }
    }

    /**
     * ERROR: adds a new user with password without uppercase letter (URA-009)
     */
    static protected function add7(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        $newUser->login_password = 'password1';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_password] '
                    . LC_MSG_ERR_PASSWORD_INVALID . ' '
                    . LC_FORM_LBL_PASSWORD_EXPECTED_UPPERCASE
            );
        }
    }

    /**
     * ERROR: adds a new user with password without lowercase letter (URA-009)
     */
    static protected function add8(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        $newUser->login_password = 'PASSWORD1';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_password] '
                    . LC_MSG_ERR_PASSWORD_INVALID . ' '
                    . LC_FORM_LBL_PASSWORD_EXPECTED_LOWERCASE
            );
        }
    }

    /**
     * ERROR: adds a new user with password without number (URA-009)
     */
    static protected function add9(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        $newUser->login_password = 'Password';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_password] '
                    . LC_MSG_ERR_PASSWORD_INVALID . ' '
                    . LC_FORM_LBL_PASSWORD_EXPECTED_NUMBER
            );
        }
    }

    /**
     * ERROR: adds a new user with login_name that already exists (URA-009)
     */
    static protected function addA(){
        $newUser = new \User();
        $newUser->login_name = 'john_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'joahannadoe@myemail.xyz';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[login_name] ' . LC_MSG_ERR_LOGIN_EXISTS
            );
        }
    }

    /**
     * ERROR: adds a new user with email address that already exists (URA-009)
     */
    static protected function addB(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'johndoe@myemail.xyz';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[user_email] ' . LC_MSG_ERR_EMAIL_EXISTS
            );
        }
    }

    /**
     * ERROR: adds a new user with invalid expiration date format (URA-009)
     */
    static protected function addC(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'johannadoe@myemail.xyz';
        $newUser->expiration_date = '2024-13-32';
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[expiration_date] ' . LC_MSG_ERR_DATE_INVALID
            );
        }
    }

    /**
     * ERROR: adds a new user with invalid user enabled value (URA-009)
     */
    static protected function addD(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'johannadoe@myemail.xyz';
        $newUser->user_enabled = 2;
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[user_enabled] '
                    . LC_FORM_LBL_USER_STATUS . ' - ' . LC_MSG_ERR_VALUE_INVALID
            );
        }
    }

    /**
     * ERROR: adds a new user with invalid full menu access value (URA-009)
     */
    static protected function addE(){
        $newUser = new \User();
        $newUser->login_name = 'johanna_doe';
        $newUser->user_name = 'Johanna DOE';
        $newUser->user_email = 'johannadoe@myemail.xyz';
        $newUser->full_menu_access = 2;
        try {
            $newUser->add();
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-009',
                $ex->getMessageWithoutCode(), '[full_menu_access] '
                    . LC_FORM_LBL_USER_MENU_ACCESS . ' - ' . LC_MSG_ERR_VALUE_INVALID
            );
        }
    }

    /**
     * NORMAL: gets infos of the second added user
     */
    static protected function get() {
        $expirationDate = new \DateTime();
        $expirationDate->add(new \DateInterval('P6M'));
        try {
            $user = new \User(self::$context['addedUsers'][1]);
            return self::areValuesEqual(
                $user->user_id, self::$context['addedUsers'][1],
                $user->login_name, 'anna_doe',
                $user->user_email, 'annadoe@myemail.xyz',
                $user->user_name, 'Anna DOE',
                $user->expiration_date, $expirationDate->format('Y-m-d'),
                $user->user_phone, '1234567890',
                $user->notes, 'Notes about Anna DOE',
                $user->full_menu_access, '0',
                $user->user_enabled, '1'
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * ERROR: requested value is missing (URA-010)
     */
    static protected function get2() {
        $user = new \User();
        try {
            $loginName = $user->login_name;
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                    $ex->getCode(), 'URA-010'
            );
        }
    }

    /**
     * ERROR: requested property is unknown (URA-008)
     */
    static protected function get3() {
        $user = new \User();
        try {
            $value = $user->unknown_property;
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                    $ex->getCode(), 'URA-008'
            );
        }
    }

    /**
     * NORMAL: checks whether test user has 'Test profile' profile
     */
    static protected function hasProfile() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        return self::areValuesEqual(
            $user->hasProfile(self::TEST_PROFILE_NAME), TRUE
        );
    }

    /**
     * NORMAL: grants profile to test user
     */
    static protected function addProfile() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->addProfile(self::$context['extraProfileName']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        return self::SUCCESS;
    }

    /**
     * ERROR: grants a profile already granted (URA-013)
     */
    static protected function addProfile2() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->addProfile(self::$context['extraProfileName']);
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-013'
            );
        }
    }

    /**
     * ERROR: grants an unknown profile (URA-012)
     */
    static protected function addProfile3() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->addProfile('Unknown profile');
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-012'
            );
        }
    }

    /**
     * NORMAL: get granted profiles
     */
    static protected function getProfiles() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        $userProfiles = $user->getProfiles();
        return self::areValuesEqual(
            is_array($userProfiles), TRUE,
            count($userProfiles), 2,
            in_array(self::TEST_PROFILE_NAME, $userProfiles), TRUE,
            in_array(self::$context['extraProfileName'], $userProfiles), TRUE
        );
    }

    /**
     * NORMAL: remove a granted profile
     */
    static protected function removeProfile() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->removeProfile(self::$context['extraProfileName']);
            $user->removeProfile(self::TEST_PROFILE_NAME);
            return self::SUCCESS;
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * ERROR: remove a profile that is not granted (URA-014)
     */
    static protected function removeProfile2() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->removeProfile(self::$context['extraProfileName']);
            return self::FAILED;
        } catch (\ZDKException $ex) {
            return self::areValuesEqual(
                $ex->getCode(), 'URA-014'
            );
        }
    }

    /**
     * NORMAL: grants two profiles
     */
    static protected function grantProfiles() {
        try {
            $user = new \User(self::$context['credentials']['id']);
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        try {
            $user->grantProfiles([self::$context['extraProfileName'], self::TEST_PROFILE_NAME]);
            return self::SUCCESS;
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * NORMAL: updates user property
     */
    static protected function  update() {
        try {
            $user = new \User(self::$context['credentials']['id']);
            $user->user_email = 'testuser@fakemail.xyz';
            $user->update();
            return self::SUCCESS;
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }
    
    /**
     * NORMAL: updates user's password
     */
    static protected function  update2() {
        global $users_notify_args;
        $users_notify_args = [];
        try {
            $user = new \User(self::$context['credentials']['id']);
            $user->login_password = 'newPassword2';
            $user->update();
            return self::areValuesEqual(
                $users_notify_args[0], FALSE,
                $users_notify_args[1], $user->getPasswordInClear(),
                $users_notify_args[2]['user_id'], self::$context['credentials']['id']
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * NORMAL: validates user infos without error
     */
    static protected function validate() {
        $newUser = new \User();
        $newUser->login_name = 'amelie_horse';
        $newUser->user_name = 'Amelie HORSE';
        $newUser->user_email = 'ameliehorse@email.xyz';
        $newUser->login_password = 'Password1';
        $newUser->expiration_date = \General::getCurrentW3CDate();
        $newUser->user_enabled = 1;
        $result = $newUser->validate(FALSE);
        return self::areValuesEqual(
            $result, TRUE
        );
    }

    /**
     * ERROR: validates user infos, login name already exists (error as array)
     */
    static protected function validate2() {
        $newUser = new \User();
        $newUser->login_name = 'john_doe';
        $newUser->user_name = 'Amelie HORSE';
        $newUser->user_email = 'ameliehorse@email.xyz';
        $newUser->login_password = 'Password1';
        $newUser->expiration_date = \General::getCurrentW3CDate();
        $newUser->user_enabled = 1;
        $result = $newUser->validate(FALSE);
        return self::areValuesEqual(
            $result['property'], 'login_name',
            $result['message'], LC_MSG_ERR_LOGIN_EXISTS
        );
    }

    /**
     * NORMAL: set password expiration date from the number of months set via
     * the CFG_DEFAULT_PWD_VALIDITY_PERIOD parameter.
     */
    static protected function setExpirationDate() {
        $user = new \User();
        $user->setExpirationDate();
        $newDateTime = new \DateTime('now');
        $newDateTime->add(new \DateInterval('P' . CFG_DEFAULT_PWD_VALIDITY_PERIOD . 'M'));
        return self::areValuesEqual(
            $user->expiration_date, \Convert::toW3CDate($newDateTime)
        );
    }

    /**
     * NORMAL: generate a login name from the user name
     */
    static protected function generateLoginName() {
        $user = new \User();
        $loginName = $user->generateLoginName('John DOE');
        return self::areValuesEqual(
                $loginName, 'john_doe1'
        );
    }

    /**
     * NORMAL: generate a new password
     */
    static protected function generateNewPassword() {
        $user = new \User();
        $passwordInClear = $user->generateNewPassword();
        return self::areValuesEqual(
            strlen($passwordInClear) > 8, TRUE,
            strlen($user->login_password) > 0, TRUE
        );
    }

    /**
     * NORMAL: returns the last generated password in clear
     */
    static protected function getPasswordInClear() {
        $user = new \User();
        $passwordInClear = $user->generateNewPassword();
        return self::areValuesEqual(
            $passwordInClear, $user->getPasswordInClear()
        );
    }

    /**
     * NORMAL: user is removed
     */
    static protected function remove() {
        $userId = self::$context['addedUsers'][1];
        $user = new \User($userId);
        $user->remove();
        try {
            new \User($userId);
            return self::FAILED;
        } catch (\ZDKException $ex) {
            unset(self::$context['addedUsers'][1]);
            return self::areValuesEqual(
                $ex->getCode(), 'URA-003'
            );
        }
    }

    /**
     * NORMAL: no notification after password changed if notification is disabled
     */
    static protected function disableNotification() {
        global $users_notify_args;
        $users_notify_args = [];
        try {
            $user = new \User(self::$context['credentials']['id']);
            $user->login_password = 'newPassword3';
            $user->disableNotification();
            $user->update();
            return self::areValuesEqual(
                count($users_notify_args), 0
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    /**
     * NORMAL: Notifies user when notification is disabled
     */
    static protected function notify() {
        global $users_notify_args;
        $users_notify_args = [];
        try {
            $user = new \User(self::$context['credentials']['id']);
            $user->login_password = 'newPassword3';
            $user->disableNotification();
            $user->update();
            $user->notify(FALSE);
            return self::areValuesEqual(
                $users_notify_args[0], FALSE,
                $users_notify_args[1], $user->getPasswordInClear(),
                $users_notify_args[2]['user_id'], self::$context['credentials']['id']
            );
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
    }

    static protected function setCustomDatabaseConnexion() {
        return self::NOT_TESTED;
    }

}
