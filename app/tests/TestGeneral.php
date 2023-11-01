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
 * Test of the General ZnetDK core class
 *
 * File version: 1.0
 * Last update: 09/08/2023
 */

namespace app\tests;
use app\TestCase;
class TestGeneral extends TestCase {

    static protected function getAbsoluteURI() {
        return self::areValuesEqual(\General::getAbsoluteURI(),
                str_replace('index.php', '', $_SERVER['REQUEST_URI']));
    }

    static protected function getExtraPartOfURI() {
        return self::NOT_TESTED;
    }

    static protected function getMainScript() {
        return self::areValuesEqual(\General::getMainScript(), $_SERVER['SCRIPT_NAME']);
    }

    static protected function getCurrentW3CDate() {
        return \General::getCurrentW3CDate() === (new \DateTime('now'))->format('Y-m-d');
    }

    static protected function isW3cDateValid() {
        return FALSE === \General::isW3cDateValid('202')
            && FALSE === \General::isW3cDateValid(99)
            && FALSE === \General::isW3cDateValid('2022-13-01')
            && FALSE === \General::isW3cDateValid('2022-02-30')
            && TRUE === \General::isW3cDateValid('2022-02-27');
    }

    static protected function getFilledMessage() {
        return 'Message 12, myValue and myLabel' ===
                \General::getFilledMessage('Message %1, %2 and %3', 12, 'myValue', 'myLabel')
            && NULL === \General::getFilledMessage()
            && 'Message %1, %2 and %3' === \General::getFilledMessage('Message %1, %2 and %3');
    }

    static protected function sanitize() {
        $stringToSanitize = '';
        $sanitizedString = '';
        for ($index = 0; $index <= 255; $index++) {
            $stringToSanitize .= chr($index);
            if ($index >= 32 && $index < 127 && !in_array($index, [60,61,62])) {
                $sanitizedString .= chr($index);
            }
        }
        $exceptionMsg = '';
        try {
            \General::sanitize($stringToSanitize, 'unknownType');
        } catch (\Exception $ex) {
            $exceptionMsg = $ex->getMessage();
        }


        return self::areValuesEqual(
            // NULL Value
            NULL, \General::sanitize(NULL),
            // default, tags strip low and high
            $sanitizedString, \General::sanitize($stringToSanitize, 'default', FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH),
            // Unknown type
            $exceptionMsg, "GEN-002: the specified type 'unknownType' is unknown!",
            // 'controller' type
            'hasta_la_vista-128', \General::sanitize('+>hasta_la_vista-128!£', 'controller'),
            // 'action' type
            'bye_girl128', \General::sanitize('bye_girl-128', 'action'),
            // 'appId' type
            'hello_guy-128' === \General::sanitize('+>hello_guy-128$', 'appId'),
            // 'lang' type
            'ciaobella128' === \General::sanitize('+>ciao_bella-128$', 'lang'),
            // 'acceptLang' type
            'fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5' , \General::sanitize('"fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5>', 'acceptLang')
        );
    }

    static protected function addGetParameterToURI() {
        return '/myuri?myparam=myvalue' ===
                \General::addGetParameterToURI('/myuri', 'myparam', 'myvalue')
            && '/myuri?hello=world&myparam=myvalue' ===
                \General::addGetParameterToURI('/myuri?hello=world', 'myparam', 'myvalue');
    }

    static protected function getURIforDownload() {
        $uri = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
        $app = key_exists('appl', $_REQUEST) && $_REQUEST['appl'] !== 'default' 
                ? '?appl=' . $_REQUEST['appl'] . '&' : '?' ;
        return self::areValuesEqual(\General::getURIforDownload('mycontroller'),
                $uri . $app . 'control=mycontroller&action=download');
    }

    static protected function writeErrorLog() {
        return parent::NOT_TESTED;
    }

    static protected function writeSystemLog() {
        return parent::NOT_TESTED;
    }

    static protected function getApplicationID() {
        $app = key_exists('appl', $_REQUEST) ? $_REQUEST['appl'] : 'default' ;
        return self::areValuesEqual(\General::getApplicationID(), $app);
    }

    static protected function isDefaultApplication() {
        $request = new \Request();
        return self::areValuesEqual(($request->appl === 'default'),
                \General::isDefaultApplication());
    }

    static protected function isToolApplication() {
        return FALSE === \General::isToolApplication();
    }

    static protected function getApplicationRelativePath() {
        return 'applications'. DIRECTORY_SEPARATOR .'myapp' ===
                \General::getApplicationRelativePath('myapp')
            && 'engine' . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'appwiz' ===
                \General::getApplicationRelativePath('appwiz');
    }

    static protected function getApplicationPublicDirRelativeURI() {
        return 'applications/myapp/public/' ===
                \General::getApplicationPublicDirRelativeURI('myapp')
            && 'engine/tools/appwiz/public/' ===
                \General::getApplicationPublicDirRelativeURI('appwiz');
    }

    static protected function getApplicationURI() {
        return self::areValuesEqual(\General::getApplicationURI(), $_SERVER['HTTP_REFERER']);        
    }

    static protected function getModules() {
        return parent::NOT_TESTED;
    }

    static protected function initModuleParameters() {
        return parent::NOT_TESTED;
    }

    static protected function isModule() {
        return FALSE === \General::isModule('testingModule');
    }

    static protected function getDummyPassword() {
        return '____________________' === \General::getDummyPassword();
    }

    static protected function getMimeType() {
        return 'image/png' === \General::getMimeType(ZNETDK_ROOT . 'engine/public/images/logoznetdk.png');
    }

    static protected function reducePictureSize() {
        $picturePath = ZNETDK_ROOT . 'engine/public/images/favicons/favicon-16x16.png';
        return self::areValuesEqual(\General::reducePictureSize($picturePath, 8, 8),
                'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAIAAABLbSncAAAABnRSTlMAAAAAAABupgeRAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAvUlEQVQImQXBv0pCYRzH4c/vFUlFPEihQ0s0uDg4dgENQUNDd9ANuB+6EgdXF6fI8bRI0KK42BZICJXHDqIQ5Z/3/fY8NlqpMyNntCKqefbitMDTEvNBzui+c17CS19ba1bIduCDJJ0MJEmPN5oNNGwnqRyQ7biqA1CscnbNcgLgg+5GWvxJCnqJ9dbX73eSyjnj4ZPaESiQjvFbCseAe85olIlf6c0hajBP+PlY77H7qS5rAAIZiM1Btxf2D2zVXN28ykJEAAAAAElFTkSuQmCC');
    }

    static protected function compareAmounts() {
        if (!defined('LC_LOCALE_NUMBER_OF_DECIMALS')) {
            define('LC_LOCALE_NUMBER_OF_DECIMALS', NULL);
        }
        return \General::compareAmounts(12.36, 12.36) === '='
            && \General::compareAmounts(12.35, 12.36) === '<'
            && \General::compareAmounts(12.36, 12.35) === '>';
    }

    static protected function encrypt() {
        return parent::NOT_TESTED;
    }

    static protected function decrypt() {
        return 'My text for testing purpose' ===
                \General::decrypt('hxr41ts6catfTasFXz2+kZj7v8Nd9kuxxwqKxz9CuPWO8Wugm3Jn6QWhASWF3HK8yXRBKTQJbnpfrkc/jVu8pEQZVR8KnJUSKbaQpsxrpBY=', 'ZnetDK Testing');
    }
    
    static protected function callRemoteAction() {
        // TEST 1: call in HTTP GET
        $credentials = self::addTestUser();
        $urlAsArray = explode('://', $_SERVER['HTTP_REFERER']);
        $swUrl = $urlAsArray[0] . '://' . $credentials['login'] . ':' . $credentials['password'] . '@' . $urlAsArray[1];
        $method = 'GET';
        $controller = 'z4mtsts_ui_ctl';
        $action = 'ajax1';
        $extraParameters = ['value2' => 'ABC', 'value1' => '456'/*, 'XDEBUG_SESSION_START' => 'netbeans-xdebug'*/];
        $jsonResponse = \General::callRemoteAction($swUrl, $method, $controller, $action, $extraParameters);
        $response = json_decode($jsonResponse, TRUE);
        $test1 = is_array($response) && $response['success'] === TRUE;
        
        // TEST 2: call in HTTP POST
        $method2 = 'GET';
        $jsonResponse2 = \General::callRemoteAction($swUrl, $method2, $controller, $action, $extraParameters);
        $response2 = json_decode($jsonResponse2, TRUE);
        $test2 = is_array($response2) && $response2['success'] === TRUE;
        
        self::removeTestUser();
        
        return $test1 && $test2;
    }

}
