<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website https://www.znetdk.fr
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
 * Test of the AppController ZnetDK core classes
 *
 * File version: 1.1
 * Last update: 08/13/2024
 */

namespace app\controller;

class z4mtsts_server_ctl extends \AppController {
    
    static protected function setAllowedActions() {
        self::setRequiredMenuItemForAction('required_menu_item', 'menu_item_for_test');
    }
    
    static protected function action_required_menu_item() {
        $response = new \Response();
        $response->success = TRUE;
        return $response;
    }

    static protected function action_testrequest() {
        return \app\tests\TestRequest::runTests();
    }
    
    static protected function action_testresponse() {
        return \app\tests\TestResponse::runTests();
    }
    
    static protected function action_testconvert() {
        return \app\tests\TestConvert::runTests();
    }
    
    static protected function action_testappcontroller() {
        return \app\tests\TestAppController::runTests();
    }
    
    static protected function action_testcontrollersecurity() {
        return \app\tests\TestControllerSecurity::runTests();
    }
    
    static protected function action_testcontrollerusers() {
        return \app\tests\TestControllerUsers::runTests();
    }
    
    static protected function action_testcontrollerprofiles() {
        return \app\tests\TestControllerProfiles::runTests();
    }

    static protected function action_testgeneral() {
        return \app\tests\TestGeneral::runTests();
    }
    
    static protected function action_testusersession() {
        return \app\tests\TestUserSession::runTests();
    }
    
    static protected function action_testdao() {
        return \app\tests\TestDAO::runTests();
    }
    
    static protected function action_testsimpledao() {
        return \app\tests\TestSimpleDAO::runTests();
    }
    
    static protected function action_testprofilemanager() {
        return \app\tests\TestProfileManager::runTests();
    }
    
    static protected function action_testusermanager() {
        return \app\tests\TestUserManager::runTests();
    }
    
    static protected function action_testuser() {
        return \app\tests\TestUser::runTests();
    }
    
    static protected function action_testvalidator() {
        return \app\tests\TestValidator::runTests();
    }

}
