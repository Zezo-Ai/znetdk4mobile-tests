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
 * Core Testing class
 *
 * File version: 1.1
 * Last update: 03/22/2024
 */
namespace app;

/**
 * To run tests, extends your Testing Class from this TestCase class.
 * Your Testing class must not be defined with a method named runTests(), 
 * areValuesEqual(), setFailed(), emptyTable() or setSqlData(). 
 * Each method must return the value SUCCESS, FAILED or NOT_TESTED or the 
 * value obtained after calling self::areValuesEqual() or self::setFailed 
 * methods.
 * The $context variable can be used to exchange data between each method of the
 * tested class.
 * The following methods are automatically called by the runTests() method if 
 * they exist in the tested class:
 * - self::beforeAllTests(): executed once before all tested class methods to 
 * initialize data in database.
 * - self::afterAllTests(): executed once after executing all tested class 
 * methods for removing data in database.
 * Finally, the self::emptyTable() and self::setSqlData() are useful methods for updating
 * SQL table content.
 * @author Pascal MARTINEZ
 */
class TestCase {
    
    static protected $context;
    
    const SUCCESS = TRUE;
    const FAILED = FALSE;
    const NOT_TESTED = -1;
    
    const TEST_USER_LOGIN = 'test_user';
    const TEST_PROFILE_NAME = 'Test profile';
    
    /**
     * Run the tests
     * @param string $scope Value 'all' to run the test for all methods,
     * otherwise the name of the only method to test 
     * @return \Response The test running status
     * @throws \Exception A tested method has returned an unexpected value or 
     * the specified method to test does not exist
     */
    static public function runTests($scope = 'all') {
        $thisClassMethods = get_class_methods(__CLASS__);
        $response = new \Response();
        $className = get_called_class();
        $classMethods = get_class_methods($className);
        $failed = [];
        $notTested = [];
        $executedMethodCount = 0;
        if (method_exists(get_called_class(), 'beforeAllTests')) {
            static::beforeAllTests();
        }
        foreach ($classMethods as $method) {
            if (in_array($method, $thisClassMethods)
                    || $method === 'beforeAllTests' 
                    || $method === 'afterAllTests'
                    || ($scope !== 'all' && $method !== $scope)) {
                continue;
            }
            $executedMethodCount++;
            $returnedStatus = static::$method();
            $status = is_array($returnedStatus) && key_exists('status', $returnedStatus)
                    ? $returnedStatus['status'] : $returnedStatus;
            if ($status === self::FAILED) {
                $failedValues = is_array($returnedStatus) && key_exists('failed', $returnedStatus)
                        ? $returnedStatus['failed'] : '';
                $failed[] = $method . $failedValues;
            } elseif ($status === self::NOT_TESTED) {
                $notTested[] = $method;
            } elseif ($status !== self::SUCCESS) {
                throw new \Exception("Unexpected returned value by the {$className}::{$method} tested method.");
            }
        }
        if (method_exists(get_called_class(), 'afterAllTests')) {
            static::afterAllTests();
        }
        if ($executedMethodCount === 0) {
            throw new \Exception("No method to test found!");
        }
        $failedCount = count($failed);
        $notTestedCount = count($notTested);
        if ($failedCount > 0) {
            $failedList = implode(', ', $failed);
            $response->setFailedMessage("{$className} class testing",
                    "{$failedCount} test case(s) failed: {$failedList}");
        } elseif ($notTestedCount > 0) {
            $percentNotTested = round($notTestedCount/$executedMethodCount*100);
            $notTestedList = implode(', ', $notTested);
            $response->setWarningMessage("{$className} class testing",
                    "{$notTestedCount} test case(s) not tested ({$percentNotTested}%): {$notTestedList}");
        } else {
            $response->success = self::SUCCESS;
        }
        return $response;
    }
    
    static protected function areValuesEqual(...$values) {
        if (count($values)%2 !== 0) {
            throw new \Exception('The number of values to compare must be a multiple of two.');
        }
        $valuesNotEqual = [];
        foreach ($values as $key => $value) {
            if ($key > 0 && $key%2 !== 0 && $value !== $values[$key-1]) {
                $obtainedValue = is_string($values[$key-1]) ? "'{$values[$key-1]}'" : strval($values[$key-1]);
                $expectedValue = is_string($value) ? "'{$value}'" : strval($value);
                $valuesNotEqual[] = "{$obtainedValue} !== {$expectedValue}";
            }
        }
        return [
            'status' => count($valuesNotEqual) === 0,
            'failed' => count($valuesNotEqual) > 0 
                ? ' [' . implode(', ', $valuesNotEqual) . ']' : ''
        ];
    }
    
    static protected function setFailed($message) {
        return [
            'status' => false,
            'failed' => ", {$message}"
        ];
    }    

    /**
     * Empty the specified table
     * @param string $tableName The table name to empty
     */
    protected static function emptyTable($tableName) {
        $pdo = \Database::getApplDbConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec("TRUNCATE TABLE $tableName");
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
    
    /**
     * Initializes data for the specified SQL table.
     * The specified table is truncated. The specified rows are inserted in the
     * SQL table.
     * @param string $tableName Name of the SQL table
     * @param array $rows Rows to insert in the specified table (optional)
     */
    protected static function setSqlData($tableName, $rows = []) {
        self::emptyTable($tableName);
        $pdo = \Database::getApplDbConnection();
        foreach ($rows as $row) {
            $columns = implode(',', array_keys($row));
            $markers = array_fill(0, count($row), '?');
            $values = implode(',', $markers);
            $sql = "INSERT INTO {$tableName}({$columns}) VALUES ({$values})";
            $statement = $pdo->prepare($sql);
            $statement->execute(array_values($row));        
        }
    }
    
    protected static function addTestUser($allowedMenuItems = NULL) {
        // User is removed if exists...
        self::removeTestUser();
        // Test Profile is created
        $profileId = self::addTestProfile($allowedMenuItems); 
        // User is created
        $today = new \DateTime('now');
        $today->add(new \DateInterval('P1D'));
        $expirationDate = \Convert::toW3CDate($today);
        $userRow = ['user_name' => 'Test user', 'user_email' => 'testuser@znetdk.fr',
            'login_name' => self::TEST_USER_LOGIN, 'expiration_date' => $expirationDate,
            'full_menu_access' => false, 'user_enabled' => '1',
            'user_phone' => '0123456789', 'notes' => 'For testing purpose'];
        $passwordInClear = '4Testin9';
        $userRow['login_password'] = \MainController::execute('Users', 'hashPassword', $passwordInClear);
        \UserManager::storeUser($userRow, [$profileId]);
        $infos = \UserManager::getUserInfos(self::TEST_USER_LOGIN);
        return [
            'id' => $infos['user_id'],
            'login' => self::TEST_USER_LOGIN,
            'password' => $passwordInClear,
            'profile_name' => self::TEST_PROFILE_NAME,
            'profile_id' => $profileId
        ];
    }
    
    protected static function addTestProfile($allowedMenuItems = NULL) {
        $profileRow = [
            'profile_name' => self::TEST_PROFILE_NAME,
            'profile_description' => 'Profile for testing purpose'
        ];
        return \ProfileManager::storeProfile($profileRow, $allowedMenuItems);
    }
    
    protected static function removeTestUser() {
        $infos = \UserManager::getUserInfos(self::TEST_USER_LOGIN);
        if (is_array($infos)) {
            \UserManager::removeUser($infos['user_id']);
            self::removeTestProfile();
            return TRUE;
        }
        return FALSE;
    }
    
    protected static function removeTestProfile() {
        $infos = \ProfileManager::getProfileInfos(self::TEST_PROFILE_NAME);
        if (is_array($infos)) {
            \ProfileManager::removeProfile($infos['profile_id']);
            return TRUE;
        }
        return FALSE;
    }
    
}
