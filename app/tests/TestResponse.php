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
 * Test of the Response ZnetDK core class
 *
 * File version: 1.0
 * Last update: 09/09/2023
 */

namespace app\tests;
use app\TestCase;
class TestResponse extends TestCase {

    static protected function set() {
        return self::NOT_TESTED;
    }
    
    static protected function get() {
        $isOK = FALSE;
        $response = new \Response();
        try {
            $response->notExists;
        } catch (\Exception $ex) {
            $isOK = strpos($ex->getMessage(), 'RSP-005') !== FALSE;
        }
        return $isOK;
    }
    
    static protected function setView() {
        return self::NOT_TESTED;
    }
    
    static protected function setResponse() {
        return self::NOT_TESTED;
    }
    
    static protected function setSuccessMessage() {
        return self::NOT_TESTED;
    }
    
    static protected function setWarningMessage() {
        return self::NOT_TESTED;
    }
    
    static protected function setFailedMessage() {
        return self::NOT_TESTED;
    }
    
    static protected function setCriticalMessage() {
        return self::NOT_TESTED;
    }
    
    static protected function setFileToDownload() {
        return self::NOT_TESTED;
    }
    
    static protected function setPrinting() {
        return self::NOT_TESTED;
    }
    
    static protected function setDataForCsv() {
        return self::NOT_TESTED;
    }
    
    static protected function setCustomContent() {
        return self::NOT_TESTED;
    }
    
    static protected function doHttpError() {
        return self::NOT_TESTED;
    }
    
    static protected function output() {
        return self::NOT_TESTED;
    }
    
 }
