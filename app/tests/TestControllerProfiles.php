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
 * Test of the Controller\Profiles ZnetDK core class
 *
 * File version: 1.0
 * Last update: 09/09/2023
 */

namespace app\tests;
use app\TestCase;
class TestControllerProfiles extends TestCase {

    static protected function isActionAllowed() {
        $menuItem = CFG_PAGE_LAYOUT === 'mobile' ? 'z4mprofiles' : 'profiles';
        // TEST 1: test user has access to profile menu item: controller's action is executed
        $credentials = self::addTestUser([$menuItem]);
        $urlAsArray = explode('://', $_SERVER['HTTP_REFERER']);
        $swUrl = $urlAsArray[0] . '://' . $credentials['login'] . ':' . $credentials['password'] . '@' . $urlAsArray[1];
        $method = 'POST';
        $controller = 'profiles';
        $action = 'detail';
        $extraParameters = [
            'id' => $credentials['profile_id'],
            'CFG_AUTHENT_REQUIRED' => 'true' /*,
            'XDEBUG_SESSION_START' => 'netbeans-xdebug' */
        ];
        $jsonResponse = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters);
        $response = json_decode($jsonResponse, TRUE);
        $test1 = is_array($response) && $response['profile_id'] === $credentials['profile_id'];
        
        // TEST 2: test if user has no access to the user menu item: HTTP error 403
        $credentials2 = self::addTestUser(['menu_item_for_test000']);
        $jsonResponse2 = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters);       
        
        
        // TEST 3: no auth, profiles view menu item is declared in the menu.php
        $extraParameters3 = [
            'id' => $credentials2['id']/*,
            'XDEBUG_SESSION_START' => 'netbeans-xdebug' */
        ];
        $jsonResponse3 = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters3);
        $response3 = json_decode($jsonResponse3, TRUE);
        $test3 = is_array($response3) && $response3['profile_id'] === $credentials2['profile_id'];
        
        // TEST 4: no auth, menu item is missing in the menu.php
        $extraParameters4 = [
            'id' => $credentials2['id'],
            'no_profiles_view' => 'true'/*,
            'XDEBUG_SESSION_START' => 'netbeans-xdebug'*/
        ];
        $jsonResponse4 = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters4);
        
        self::removeTestUser();
        return $test1 && $jsonResponse2 === FALSE && $test3 && $jsonResponse4 === FALSE;
    }
    
}
