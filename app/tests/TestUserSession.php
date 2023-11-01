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
 * Test of the UserSession ZnetDK core class
 *
 * File version: 1.1
 * Last update: 11/01/2023
 */

namespace app\tests;
use app\TestCase;
class TestUserSession extends TestCase {
    
    // Called once after testing all methods
    static protected function afterAllTests() {
        \UserSession::clearUserSession();
    }

    static protected function isAuthenticated() {
        return self::NOT_TESTED;
    }
    
    static protected function setLoginName() {
        \UserSession::setLoginName('fakeuser');
        return TRUE;
    }
    
    static protected function getLoginName() {
        return self::areValuesEqual(\UserSession::getLoginName(), 'fakeuser');
    }
    
    static protected function setUserId() {
        \UserSession::setUserId(9999);
        return TRUE;
    }
    
    static protected function getUserId() {
        return self::areValuesEqual(\UserSession::getUserId(), 9999);
    }
    
    static protected function setUserProfiles() {
        \UserSession::setUserProfiles([
            10001 => 'User profile #1 for test',
            10002 => 'User profile #2 for test',
            10003 => 'User profile #3 for test',
        ]);
        return TRUE;
    }
    
    static protected function getUserProfiles() {
        $userProfiles = \UserSession::getUserProfiles();
        return self::areValuesEqual(
                count($userProfiles), 3,
                $userProfiles[10001], 'User profile #1 for test',
                $userProfiles[10002], 'User profile #2 for test',
                $userProfiles[10003], 'User profile #3 for test'
        );
    }
    
    static protected function hasUserProfile() {
        return \UserSession::hasUserProfile('User profile #2 for test') === TRUE;
    }
    
    static protected function setUserName() {
        \UserSession::setUserName('fakeusername');
        return TRUE;
    }
    
    static protected function getUserName() {
        return self::areValuesEqual(\UserSession::getUserName(), 'fakeusername');
    }
    
    static protected function setUserEmail() {
        \UserSession::setUserEmail('unknownuser@fakedomain.fr');
        return TRUE;
    }
    
    static protected function getUserEmail() {
        return self::areValuesEqual(
            \UserSession::getUserEmail(), 'unknownuser@fakedomain.fr'
        );
    }
    
    static protected function setFullMenuAccess() {
        \UserSession::setFullMenuAccess(FALSE);
        return TRUE;
    }
    
    static protected function hasFullMenuAccess() {
        return self::areValuesEqual(
            \UserSession::hasFullMenuAccess(), FALSE
        );
    }
    
    static protected function setLanguage() {
        \UserSession::setLanguage('fr');
        return TRUE;
    }
    
    static protected function getLanguage() {
        return self::areValuesEqual(
            \UserSession::getLanguage(), 'fr'
        );
    }
    
    static protected function setAuthentHasFailed() {
        return self::NOT_TESTED;
    }
    
    static protected function isMaxNbrOfFailedAuthentReached() {
        return self::NOT_TESTED;
    }
    
    static protected function resetAuthentHasFailed() {
        return self::NOT_TESTED;
    }
    
    static protected function setAccessMode() {
        return self::NOT_TESTED;
    }
    
    static protected function clearUserSession() {
        return self::NOT_TESTED;
    }
    
    static protected function setCustomValue() {
        return \UserSession::setCustomValue('z4m_test_case_custom_value',
                'Wonderfull world');        
    }
    
    static protected function getCustomValue() {
        return self::areValuesEqual(
            \UserSession::getCustomValue('z4m_test_case_custom_value'),
            'Wonderfull world'
        );
    }
    
    static protected function removeCustomValue() {
        return self::areValuesEqual(
            \UserSession::removeCustomValue('z4m_test_case_custom_value'), TRUE,
            \UserSession::getCustomValue('z4m_test_case_custom_value'), NULL
        );
    }
    
    static protected function setUIToken() {
        return self::NOT_TESTED;
    }
    
    static protected function getUIToken() {
        return self::NOT_TESTED;
    }
    
    static protected function isUITokenValid() {
        return self::NOT_TESTED;
    }
    
    
    
 }
