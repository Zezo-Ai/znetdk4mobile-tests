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
 * Test of the SimpleDAO ZnetDK core class
 *
 * File version: 1.0
 * Last update: 04/02/2023
 */

namespace app\tests;
use app\TestCase;
class TestSimpleDAO extends TestCase {

    // Called once before testing all methods
    static protected function beforeAllTests() {
        self::emptyTable('zdk_profile_menus');
        self::emptyTable('zdk_profile_rows');
        self::emptyTable('zdk_user_profiles');
        self::resetData();
    }

    // Called once after testing all methods
    static protected function afterAllTests() {
        self::emptyTable('zdk_profile_menus');
        self::emptyTable('zdk_profile_rows');
        self::emptyTable('zdk_user_profiles');
        self::emptyTable('zdk_profiles');
    }

    // PRIVATE METHODS
    static private function resetData() {
        self::setSqlData('zdk_profiles', [
            ['profile_id' => 1, 'profile_name' => 'Profile A', 'profile_description' => 'Description of profile A'],
            ['profile_id' => 2, 'profile_name' => 'Profile B', 'profile_description' => 'Description of profile B'],
            ['profile_id' => 3, 'profile_name' => 'Profile C', 'profile_description' => 'Description of profile C'],
            ['profile_id' => 4, 'profile_name' => 'Profile D', 'profile_description' => 'Description of profile D'],
            ['profile_id' => 5, 'profile_name' => 'Profile E', 'profile_description' => 'Description of profile E'],
            ['profile_id' => 6, 'profile_name' => 'Profile F', 'profile_description' => 'Description of profile F'],
            ['profile_id' => 7, 'profile_name' => 'Profile G', 'profile_description' => 'Description of profile G'],
            ['profile_id' => 8, 'profile_name' => 'Profile H', 'profile_description' => 'Description of profile H']
        ]);
    }

    // TESTED METHODS
    static protected function construct() {
        try {
            new \SimpleDAO('zdk_profiles');
        } catch (\Exception $ex) {
            return self::setFailed($ex->getMessage());
        }
        return self::SUCCESS;

    }

    static protected function getRows() {
        $dao = new \SimpleDAO('zdk_profiles');
        $rowsFound = [];
        $count = $dao->getRows($rowsFound, 'profile_name');
        if (count($rowsFound) !== 5) {
            return self::setFailed('Expected number of rows is different than 5.');
        }
        return self::areValuesEqual(
                $count, 8,
                $rowsFound['0']['profile_id'], '6',
                $rowsFound['0']['profile_name'], 'Profile F',
                $rowsFound['0']['profile_description'], 'Description of profile F',
                $rowsFound['1']['profile_id'], '5',
                $rowsFound['1']['profile_name'], 'Profile E',
                $rowsFound['1']['profile_description'], 'Description of profile E',
                $rowsFound['2']['profile_id'], '4',
                $rowsFound['2']['profile_name'], 'Profile D',
                $rowsFound['2']['profile_description'], 'Description of profile D',
                $rowsFound['3']['profile_id'], '3',
                $rowsFound['3']['profile_name'], 'Profile C',
                $rowsFound['3']['profile_description'], 'Description of profile C',
                $rowsFound['4']['profile_id'], '2',
                $rowsFound['4']['profile_name'], 'Profile B',
                $rowsFound['4']['profile_description'], 'Description of profile B'
        );
    }

    static protected function setKeywordSearchColumn() {
        $dao = new \SimpleDAO('zdk_profiles');
        $dao->setKeywordSearchColumn('profile_name');
        $_REQUEST['first'] = '0';
        $_REQUEST['count'] = '20';
        $_REQUEST['keyword'] = 'D';
        $rowsFound = [];
        $count = $dao->getRows($rowsFound, 'profile_name');
        if (count($rowsFound) !== 1) {
            return self::setFailed('Expected number of rows is different than 1.');
        }
        return self::areValuesEqual(
                $count, 1,
                $rowsFound['0']['profile_id'], '4',
                $rowsFound['0']['profile_name'], 'Profile D',
                $rowsFound['0']['profile_description'], 'Description of profile D'
        );
    }

    static protected function getSuggestions() {
        $dao = new \SimpleDAO('zdk_profiles');
        $dao->setKeywordSearchColumn('profile_name');
        $_REQUEST['query'] = 'D';
        $suggestions = $dao->getSuggestions();
        if (count($suggestions) !== 1) {
            return self::setFailed('Expected number of suggestions is different than 1.');
        }
        return self::areValuesEqual(
                key_exists('value', $suggestions['0']), FALSE,
                $suggestions['0']['profile_id'], '4',
                $suggestions['0']['profile_name'], 'Profile D',
                $suggestions['0']['profile_description'], 'Description of profile D',
                $suggestions['0']['label'], 'Profile D'
        );
    }

    static protected function getRowsForCondition() {
        $dao = new \SimpleDAO('zdk_profiles');
        $condition = 'profile_name IN (?, ?)';
        $result = $dao->getRowsForCondition($condition, 'Profile C', 'Profile E');
        if (count($result) !== 2) {
            return self::setFailed('Expected number of rows is different than 2.');
        }
        return self::areValuesEqual(
                $result['0']['profile_id'], '3',
                $result['0']['profile_name'], 'Profile C',
                $result['0']['profile_description'], 'Description of profile C',
                $result['1']['profile_id'], '5',
                $result['1']['profile_name'], 'Profile E',
                $result['1']['profile_description'], 'Description of profile E',
        );
    }

}
