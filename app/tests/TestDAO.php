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
 * Test of the DAO ZnetDK core class
 *
 * File version: 1.0
 * Last update: 04/02/2023
 */

namespace app\tests;
use app\TestCase;
class TestDAO extends TestCase {

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
            ['profile_id' => 4, 'profile_name' => 'Profile D', 'profile_description' => 'Description of profile D']
        ]);
    }
    
    static private function getNewProfileDao() {
        return new class extends \DAO {
            protected function initDaoProperties() {
                $this->table = 'zdk_profiles';
                $this->IdColumnName = 'profile_id';
                $this->filterClause = "WHERE profile_name = ?";
            }
        };
    }

    // TESTED METHODS
    static protected function construct() {
        try {
            self::getNewProfileDao();
        } catch (\Exception $ex) {
            return parent::setFailed($ex->getMessage());
        }
        return self::SUCCESS;
    }
    
    static protected function doesTableExist() {
        $dao = self::getNewProfileDao();
        return $dao->doesTableExist();
    }
    
    static protected function getCount() {
        $dao = self::getNewProfileDao();
        return self::areValuesEqual($dao->getCount(), 4);
    }

    static protected function getResult() {
        $dao = self::getNewProfileDao();
        return self::areValuesEqual($dao->getResult()['profile_name'], 'Profile A');
    }

    static protected function getById() {
        $dao = self::getNewProfileDao();
        return self::areValuesEqual($dao->getById(3)['profile_name'], 'Profile C');
    }
    
    static protected function setFilterCriteria() {
        $dao = self::getNewProfileDao();
        $dao->setFilterCriteria('Profile B');
        $count = $dao->getCount(); // 1 expected
        $row = $dao->getResult();
        return self::areValuesEqual(
            $count, 1,
            $row['profile_name'], 'Profile B'
        );
    }

    static protected function setSortCriteria() {
        $dao = self::getNewProfileDao();
        $dao->setSortCriteria('profile_name DESC');
        return self::areValuesEqual(
            $dao->getResult()['profile_name'], 'Profile D',
            $dao->getResult()['profile_name'], 'Profile C',
            $dao->getResult()['profile_name'], 'Profile B',
            $dao->getResult()['profile_name'], 'Profile A'
        );
    }

    static protected function setLimit() {
        $dao = self::getNewProfileDao();
        $dao->setLimit(1, 2);
        return self::areValuesEqual(
            $dao->getCount(), 2,
            $dao->getResult()['profile_name'], 'Profile B',
            $dao->getResult()['profile_name'], 'Profile C'
        );
    }

    static protected function setSelectedColumns() {
        $dao = self::getNewProfileDao();
        $dao->setSelectedColumns(['profile_name']);
        $row = $dao->getById(2);
        if ($row === FALSE) {
            return self::setFailed('No row returned for ID=2.');
        }
        return self::areValuesEqual(
            key_exists('profile_name', $row), TRUE,
            key_exists('profile_id', $row), FALSE,
            key_exists('profile_description', $row), FALSE
        );
    }

    static protected function store() {
        $dao = self::getNewProfileDao();
        $newId = $dao->store(['profile_name' => 'Profile E']);
        return self::areValuesEqual(
            $dao->getById($newId)['profile_name'], 'Profile E'
        );
    }

    static protected function beginTransaction() {
        try {
            $dao = self::getNewProfileDao();
            $dao->beginTransaction();
            $dao->rollback();
        } catch (\Exception $ex) {
            return parent::setFailed($ex->getMessage());
        }
        return self::SUCCESS;
    }

    static protected function commit() {
        $dao = self::getNewProfileDao();
        $row = $dao->getById(2);
        if ($row === FALSE) {
            return self::setFailed('Row ID=2 is missing.');
        }
        $dao->beginTransaction();
        $updatedDescription = 'Updated description for Profile B';
        $row['profile_description'] = $updatedDescription;
        $updatedId = $dao->store($row, FALSE);
        $dao->commit();
        return self::areValuesEqual(
            $updatedId, '2',
            $dao->getById($updatedId)['profile_description'], $updatedDescription
        );
    }

    static protected function rollback() {
        self::resetData();
        $dao = self::getNewProfileDao();
        $rowCountBeforeInsert = $dao->getCount(); // 4 is expected;
        $dao->beginTransaction();
        $dao->store(['profile_name' => 'Profile F'], FALSE);
        $rowCountAfterInsert = $dao->getCount(); // 5 is expected;
        $dao->rollback();
        $rowCountAfterRollback = $dao->getCount(); // 4 is expected;
        return self::areValuesEqual(
                $rowCountBeforeInsert, 4,
                $rowCountAfterInsert, 5,
                $rowCountAfterRollback, 4
        );
    }

    static protected function setForUpdate() {
        $dao = self::getNewProfileDao();
        $dao->beginTransaction();
        $dao->setForUpdate(TRUE);
        $row = $dao->getById(2);
        $row['profile_name'] = 'Profile modified';
        $dao->store($row, FALSE);
        $dao->commit();
        $updatedRow = $dao->getById(2);
        return self::areValuesEqual(
            $updatedRow['profile_name'], 'Profile modified'
        );
    }

    static protected function isForUpdate() {
        $dao = self::getNewProfileDao();
        $dao->beginTransaction();
        $dao->setForUpdate(TRUE);
        $isTrue = $dao->isForUpdate();
        $dao->setForUpdate(FALSE);
        $isFalse = $dao->isForUpdate();
        $dao->commit();
        return $isTrue && !$isFalse;
    }

    static protected function remove() {
        $dao = self::getNewProfileDao();
        if ($dao->getById(1)['profile_id'] !== '1') {
            return self::setFailed("Row ID=1 is missing.");
        }
        $removeCount = $dao->remove(1);
        return self::areValuesEqual(
                $removeCount, 1,
                $dao->getById(1), FALSE
        );
    }
    
    static protected function setProfileCriteria() {
        return self::NOT_TESTED;
    }

    static protected function setStoredProfiles() {
        return self::NOT_TESTED;
    }
    
    static protected function setAmountColumns() {
        return self::NOT_TESTED;
    }
    
    static protected function setMoneyColumns() {
        return self::NOT_TESTED;
    }
    
    static protected function setDateColumns() {
        return self::NOT_TESTED;
    }

}
