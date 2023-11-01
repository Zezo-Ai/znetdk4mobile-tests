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
 * Test of the AppController ZnetDK core class
 *
 * File version: 1.0
 * Last update: 09/08/2023
 */

namespace app\tests;
use app\TestCase;
class TestAppController extends TestCase {

    static protected function setRequiredMenuItemForAction() {
        // TEST 1: test user has access to 'menu_item_for_test' menu item: controller's action is executed
        $credentials = self::addTestUser(['menu_item_for_test']);
        $urlAsArray = explode('://', $_SERVER['HTTP_REFERER']);
        $swUrl = $urlAsArray[0] . '://' . $credentials['login'] . ':' . $credentials['password'] . '@' . $urlAsArray[1];
        $method = 'GET';
        $controller = 'z4mtsts_server_ctl';
        $action = 'required_menu_item';
        $extraParameters = ['CFG_AUTHENT_REQUIRED' => 'true'/*, 'XDEBUG_SESSION_START' => 'netbeans-xdebug'*/];
        $jsonResponse = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters);
        $response = json_decode($jsonResponse, TRUE);
        $test1 = is_array($response) && $response['success'] === TRUE;
        // TEST 2: test user has access to a menu item that is not 'menu_item_for_test': HTTP error 403
        self::addTestUser(['menu_item_for_test000']);
        $jsonResponse2 = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters);       
        self::removeTestUser();
        return $test1 && $jsonResponse2 === FALSE;
    }

    static protected function setRequiredProfileForAction() {
        return parent::NOT_TESTED;
    }
    
    static protected function setForbiddenProfileForAction() {
        return parent::NOT_TESTED;
    }
    
    static protected function doAction() {
        return parent::NOT_TESTED;
    }
    
    static protected function doAsynchronousAction() {
        return parent::NOT_TESTED;
    }
    
    static protected function isAction() {
        return parent::NOT_TESTED;
    }
    
    static protected function isActionAllowed() {
        return parent::NOT_TESTED;
    }
    
    static protected function isAsynchronousAction() {
        return parent::NOT_TESTED;
    }
    
}
