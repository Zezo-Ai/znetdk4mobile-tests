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
 * Test of the Request ZnetDK core class
 *
 * File version: 1.0
 * Last update: 03/29/2023
 */

namespace app\tests;
use app\TestCase;
class TestRequest extends TestCase {

    static protected function construct() {
        return self::NOT_TESTED;
    }

    static protected function setVariableFilteringLevel() {
        return self::NOT_TESTED;
    }
    
    static protected function setTrimedCharacters() {
        return self::NOT_TESTED;
    }
    
    static protected function get() {
        $request = new \Request();
        return self::areValuesEqual(
                $request->control, $_REQUEST['control']
        );
    }
    
    static protected function getMethod() {        
        return self::areValuesEqual(\Request::getMethod(), $_SERVER['REQUEST_METHOD']);
    }
    
    static protected function getController() {
        return self::areValuesEqual(\Request::getController(), $_REQUEST['control']);
    }
    
    static protected function isControllerReservedNameForGetMethod() {
        return self::NOT_TESTED;
    }
    
    static protected function getAction() {
        return self::areValuesEqual(\Request::getAction(), $_REQUEST['action']);
    }
    
    static protected function getOtherApplication() {
        return self::areValuesEqual(\Request::getOtherApplication(), 
            key_exists('appl', $_REQUEST) ? $_REQUEST['appl'] : NULL);
    }
    
    static protected function setHttpError() {
        return self::NOT_TESTED;
    }
    
    static protected function getHttpErrorCode() {
        // TODO: error  E_WARNING - Undefined array key "httperror" - Request.php(272)
        //return self::areValuesEqual(\Request::getHttpErrorCode(), '500');
        return self::NOT_TESTED;
    }
    
    static protected function getLanguage() {
        return self::NOT_TESTED;
    }
    
    static protected function getFilteredServerValue() {
        return self::areValuesEqual(
            \Request::getFilteredServerValue('REQUEST_URI', FILTER_SANITIZE_URL),
                $_SERVER['REQUEST_URI']);
    }
    
    static protected function getAcceptLanguage() {
        return self::areValuesEqual(\Request::getAcceptLanguage(), 
                $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
    
    static protected function getRemoteAddress() {
        return self::areValuesEqual(\Request::getRemoteAddress(), 
                $_SERVER['REMOTE_ADDR']);
    }
    
    static protected function getHttpBasicAuthCredentials() {
        return self::NOT_TESTED;
    }
    
    static protected function getValuesAsMap() {
        $request = new \Request();
        $result = $request->getValuesAsMap('control', 'action');        
        return self::areValuesEqual(            
            is_array($result) && key_exists('control', $result) 
                ? $result['control'] : 'Controller ????', $_REQUEST['control'],
            is_array($result) && key_exists('action', $result) 
                ? $result['action'] : 'Action ????', $_REQUEST['action']
        );
    }
    
    static protected function getArrayAsMap() {
        $request = new \Request();
        $result = $request->getArrayAsMap(['control', 'action']);        
        return self::areValuesEqual(            
            is_array($result) && key_exists('control', $result) 
                ? $result['control'] : 'Controller ????', $_REQUEST['control'],
            is_array($result) && key_exists('action', $result) 
                ? $result['action'] : 'Action ????', $_REQUEST['action']
        );
    }
    
    static protected function isUploadedFile() {
        $request = new \Request();
        return $request->isUploadedFile('fake_file') === FALSE;
    }
    
    static protected function getUploadedFileInfos() {
        return self::NOT_TESTED;
    }
    
    static protected function moveImageFile() {
        return self::NOT_TESTED;
    }
    
 }
