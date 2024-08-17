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
 * Test of the Users Controller ZnetDK core classes
 *
 * File version: 1.0
 * Last update: 08/15/2024
 */

namespace app\controller;


class Users extends \AppController {
    
    /**
     * Notifications triggered when a user is added or their password is changed
     * are memorized in the global '$users_notify_args' variable for testing
     * purpose (see TestUser testing class).
     * @global array $users_notify_args Global variable memorizing  the method's
     * arguments.
     * @param boolean $isNewUser If TRUE, user has been created otherwise their
     * password has been changed.
     * @param string $passwordInClear New password in clear
     * @param array $userRow User data
     */
    static public function notify($isNewUser, $passwordInClear, $userRow) {
        global $users_notify_args;
        $users_notify_args = [
            $isNewUser, $passwordInClear, $userRow
        ];
    }
}
