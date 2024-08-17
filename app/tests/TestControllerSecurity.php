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
 * Test of the Controller\Users ZnetDK core class
 *
 * File version: 1.0
 * Last update: 08/11/2024
 */

namespace app\tests;
use app\TestCase;

/**
 * Testing controller\Security class
 */
class TestControllerSecurity extends TestCase {

    // Called once before testing all methods
    static protected function beforeAllTests() {
        self::$context['credentials'] = self::addTestUser();
        self::$context['sessionKey'] = \General::getAbsoluteURI() . ZNETDK_APP_NAME;
        self::$context['newPassword'] = 'newPassword0';
        self::$context['uitk'] = \UserSession::getUIToken();
        \UserSession::clearUserSession();
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
    }

    // Called once after testing all methods
    static protected function afterAllTests() {
        self::removeTestUser();
    }

    /**
     * NORMAL CASE: Login success, access mode is 'public'
     */
    static protected function action_login_1() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['credentials']['password'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, TRUE,
            $response->summary, NULL,
            $response->msg, LC_MSG_INF_LOGIN,
            $response->login_with_email, '0',
            \UserSession::getUserId(), self::$context['credentials']['id'],
            \UserSession::getLoginName(), self::TEST_USER_LOGIN,
            \UserSession::getUserEmail(), self::$context['credentials']['email'],
            \UserSession::getUserName(), self::$context['credentials']['name'],
            \Request::getRemoteAddress() === $_SESSION[self::$context['sessionKey']]['ip_address'],
            \General::getAbsoluteURI() === $_SESSION[self::$context['sessionKey']]['application_uri']
        );
    }

    /**
     * NORMAL CASE: user log out, session data is removed.
     */
    static protected function action_logout() {
        $beforeLogout = self::areValuesEqual(
            \UserSession::getUserId(), self::$context['credentials']['id'],
            \UserSession::getLoginName(), self::TEST_USER_LOGIN,
            \UserSession::getUserEmail(), self::$context['credentials']['email'],
            \UserSession::getUserName(), self::$context['credentials']['name']
        );
        $response = \controller\Security::doAction('logout');
        $status = self::areValuesEqual(
            $response->success, TRUE,
            $response->msg, LC_MSG_INF_LOGOUT,
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL,
            $beforeLogout['status'], TRUE
        );
        // UI token restored after logout
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
        // Status returned
        return $status;
    }

    /**
     * NORMAL CASE: Login success with email address, access mode is 'private'
     */
    static protected function action_login_2() {
        $_REQUEST['login_name'] = self::$context['credentials']['email'];
        $_REQUEST['password'] = self::$context['credentials']['password'];
        $_REQUEST['access'] = 'private';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, TRUE,
            $response->summary, NULL,
            $response->msg, LC_MSG_INF_LOGIN,
            $response->login_with_email, '1',
            \UserSession::getUserId(), self::$context['credentials']['id'],
            \UserSession::getLoginName(), self::TEST_USER_LOGIN,
            \UserSession::getUserEmail(), self::$context['credentials']['email'],
            \UserSession::getUserName(), self::$context['credentials']['name']
        );
    }

    /**
     * NORMAL CASE: Login success, password changed successfully,
     * access mode is 'public'
     */
    static protected function action_login_3() {
        // Logout
        \controller\Security::doAction('logout');
        // UI token restored after logout
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
        // Test case begins...
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['credentials']['password'];
        $_REQUEST['login_password'] = self::$context['newPassword'];
        $_REQUEST['login_password2'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, TRUE,
            $response->summary, LC_FORM_TITLE_CHANGE_PASSWORD,
            $response->msg, LC_MSG_INF_PWDCHANGED,
            $response->login_with_email, '0',
            \UserSession::getUserId(), self::$context['credentials']['id'],
            \UserSession::getLoginName(), self::TEST_USER_LOGIN,
            \UserSession::getUserEmail(), self::$context['credentials']['email'],
            \UserSession::getUserName(), self::$context['credentials']['name']
        );
    }


    /**
     * ERROR CASE: Login failed: password mismatch
     */
    static protected function action_login_4() {
        // Logout
        \controller\Security::doAction('logout');
        // UI token restored after logout
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
        // Test case begins...
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = 'WrongPassword9';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_LOGIN,
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL,
            $_SESSION[self::$context['sessionKey']]['nbr_of_failed_authent'], 1,
            $_SESSION[self::$context['sessionKey']]['user_failed_authent'], self::TEST_USER_LOGIN
        );
    }

    /**
     * ERROR CASE: Login succeeded, password is changing but new password
     * mismatches with confirmation
     */
    static protected function action_login_5() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = 'newPassword1';
        $_REQUEST['login_password2'] = 'newPassword2'; // Is different
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PWD_MISMATCH,
            $response->ename, 'login_password2',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login succeeded, password is changing but no lower case 
     * letter found in the new password (see CFG_CHECK_PWD_LOWERCASE_REGEXP)
     */
    static protected function action_login_5b() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = 'NEWPASSWORD1';
        $_REQUEST['login_password2'] = 'NEWPASSWORD1';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PASSWORD_INVALID . ' ' . LC_FORM_LBL_PASSWORD_EXPECTED_LOWERCASE,
            $response->ename, 'login_password',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login succeeded, password is changing but no upper case 
     * letter found in the new password (CFG_CHECK_PWD_UPPERCASE_REGEXP).
     */
    static protected function action_login_5c() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = 'newpassword1';
        $_REQUEST['login_password2'] = 'newpassword1';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PASSWORD_INVALID . ' ' . LC_FORM_LBL_PASSWORD_EXPECTED_UPPERCASE,
            $response->ename, 'login_password',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }
    
    /**
     * ERROR CASE: Login succeeded, password is changing but no number found in
     * the new password (CFG_CHECK_PWD_NUMBER_REGEXP).
     */
    static protected function action_login_5d() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = 'newPassword';
        $_REQUEST['login_password2'] = 'newPassword';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PASSWORD_INVALID . ' ' . LC_FORM_LBL_PASSWORD_EXPECTED_NUMBER,
            $response->ename, 'login_password',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }
    
    /**
     * ERROR CASE: Login succeeded, password is changing but the new password is
     * too short (CFG_CHECK_PWD_LENGTH_REGEXP).
     */
    static protected function action_login_5e() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = 'newPwd1';
        $_REQUEST['login_password2'] = 'newPwd1';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PASSWORD_INVALID . ' ' . LC_FORM_LBL_PASSWORD_EXPECTED_LENGTH,
            $response->ename, 'login_password',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }
    
    /**
     * ERROR CASE: Login succeeded, password is changing but the new password is
     * the same than the previous one.
     */
    static protected function action_login_5f() {
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['login_password'] = self::$context['newPassword'];
        $_REQUEST['login_password2'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        unset($_REQUEST['login_password'], $_REQUEST['login_password2']);
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_PWD_IDENTICAL,
            $response->ename, 'login_password',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login succeeded but user account is disabled
     */
    static protected function action_login_6() {
         // User is disabled.
        $user = new \User(self::$context['credentials']['id']);
        $user->user_enabled = '0';
        $user->update();
        // Login
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        $errorMsg =  is_int(CFG_NBR_FAILED_AUTHENT) && CFG_NBR_FAILED_AUTHENT > 0
                ? LC_MSG_ERR_LOGIN_DISABLED : LC_MSG_ERR_LOGIN;
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, $errorMsg,
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login succeeded but user account is archived
     */
    static protected function action_login_7() {
        // User is archived.
        $user = new \User(self::$context['credentials']['id']);
        $user->user_enabled = '-1';
        $user->update();
        // Login
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_LOGIN,
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login failed because login name does not exist
     */
    static protected function action_login_8() {
        // User is enabled.
        $user = new \User(self::$context['credentials']['id']);
        $user->user_enabled = '1';
        $user->update();
        // Login
        $_REQUEST['login_name'] = 'fakelogin';
        $_REQUEST['password'] = 'fakePassword';
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_LOGIN,
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login failed because login name is entered in uppoercase
     */
    static protected function action_login_9() {
        $_REQUEST['login_name'] = strtoupper(self::TEST_USER_LOGIN);
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_LOGIN,
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login failed because the password has expired
     */
    static protected function action_login_a() {
        // User's password expiration date set to today.
        $user = new \User(self::$context['credentials']['id']);
        $user->expiration_date = \General::getCurrentW3CDate();
        $user->update();
        // Login
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['password'] = self::$context['newPassword'];
        $_REQUEST['access'] = 'public';
        $response = \controller\Security::doAction('login');
        return self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            $response->msg, LC_MSG_ERR_LOGIN_EXPIRATION,
            $response->ename, 'login_name',
            $response->newpasswordrequired, TRUE,
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
    }

    /**
     * ERROR CASE: Login failed and user account is disabled because the
     * number of allowed attempts has been exceeded (see CFG_NBR_FAILED_AUTHENT
     * when set to a value > 0).
     */
    static protected function action_login_b() {
        // User account is enabled and the password expiration date is not reached.
        $user = new \User(self::$context['credentials']['id']);
        $oneMonthAfterToday = new \DateTime('today');
        $oneMonthAfterToday->add(new \DateInterval('P1M'));
        $user->expiration_date = $oneMonthAfterToday->format('Y-m-d');
        $user->user_enabled = '1';
        $user->update();
        // User session is cleared
        \UserSession::clearUserSession();
        // UI token restored after logout
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
        // Test case begins...
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['access'] = 'public';
        for ($index = 1; $index <= CFG_NBR_FAILED_AUTHENT; $index++) {
            $_REQUEST['password'] = "wrongPassword{$index}";
            $response = \controller\Security::doAction('login');
            $user = new \User(self::$context['credentials']['id']);
            $test = self::areValuesEqual(
                $user->user_enabled, $index === CFG_NBR_FAILED_AUTHENT ? '0' : '1',
                $response->success, FALSE,
                $response->summary, LC_FORM_TITLE_LOGIN,
                $response->msg, $index === CFG_NBR_FAILED_AUTHENT ? LC_MSG_ERR_LOGIN_TOO_MUCH_ATTEMPTS : LC_MSG_ERR_LOGIN,
                $response->ename, 'login_name',
                $index === CFG_NBR_FAILED_AUTHENT ? $response->toomuchattempts : TRUE, TRUE,
                \UserSession::getUserId(), NULL,
                \UserSession::getLoginName(), NULL,
                \UserSession::getUserEmail(), NULL,
                \UserSession::getUserName(), NULL,
                $_SESSION[self::$context['sessionKey']]['nbr_of_failed_authent'], $index,
                $_SESSION[self::$context['sessionKey']]['user_failed_authent'], self::TEST_USER_LOGIN
            );
            if ($test['status'] === FALSE) {
                return $test;
            }
        }
        return self::NOT_TESTED;
    }
    
    /**
     * ERROR CASE: Login failed and user account is locked out because the
     * number of allowed attempts has been exceeded.
     * see CFG_LOGIN_THROTTLING_ATTEMPTS_BEFORE_LOCKOUT, 
     * CFG_LOGIN_THROTTLING_LOCKOUT_DELAY and
     * CFG_LOGIN_THROTTLING_ATTEMPTS_WINDOW_TIME
     * Only testable if CFG_NBR_FAILED_AUTHENT is not > 0.
     */
    static protected function action_login_c() {
        if (CFG_NBR_FAILED_AUTHENT > 0) {
            return self::NOT_TESTED;
        }
        // User account is enabled and the password expiration date is not reached.
        $user = new \User(self::$context['credentials']['id']);
        $oneMonthAfterToday = new \DateTime('today');
        $oneMonthAfterToday->add(new \DateInterval('P1M'));
        $user->expiration_date = $oneMonthAfterToday->format('Y-m-d');
        $user->user_enabled = '1';
        $user->update();
        // User session is cleared
        \UserSession::clearUserSession();
        // UI token restored after logout
        $_SESSION[self::$context['sessionKey']]['ui_token'] = self::$context['uitk'];
        // Login throttle history file removed
        $loginThrottling = new class(self::TEST_USER_LOGIN) extends \LoginThrottling {
            public function resetForTests() {
                $this->reset();
            }
        };
        $loginThrottling->resetForTests();
        // Test case begins...
        $_REQUEST['login_name'] = self::TEST_USER_LOGIN;
        $_REQUEST['access'] = 'public';
        for ($index = 1; $index <= CFG_LOGIN_THROTTLING_ATTEMPTS_BEFORE_LOCKOUT; $index++) {
            $_REQUEST['password'] = "wrongPassword{$index}";
            $response = \controller\Security::doAction('login');
            $test = self::areValuesEqual(
                $response->success, FALSE,
                $response->summary, LC_FORM_TITLE_LOGIN,
                $response->msg, LC_MSG_ERR_LOGIN,
                $response->ename, 'login_name',
                \UserSession::getUserId(), NULL,
                \UserSession::getLoginName(), NULL,
                \UserSession::getUserEmail(), NULL,
                \UserSession::getUserName(), NULL
            );
            if ($test['status'] === FALSE) {
                return $test;
            }
        }
        $lastIndex = CFG_LOGIN_THROTTLING_ATTEMPTS_BEFORE_LOCKOUT + 1;
        $_REQUEST['password'] = "wrongPassword{$lastIndex}";
        $response = \controller\Security::doAction('login');
        $lastTest = self::areValuesEqual(
            $response->success, FALSE,
            $response->summary, LC_FORM_TITLE_LOGIN,
            substr($response->msg, 0, 10), substr(LC_MSG_ERR_LOGIN_THROTTLING_TOO_MUCH_ATTEMPTS, 0, 10),
            $response->ename, 'login_name',
            \UserSession::getUserId(), NULL,
            \UserSession::getLoginName(), NULL,
            \UserSession::getUserEmail(), NULL,
            \UserSession::getUserName(), NULL
        );
        // Login throttle history file removed for next test cases
        $loginThrottling->resetForTests();
        // Last test status is returned
        return $lastTest;
    }

}
