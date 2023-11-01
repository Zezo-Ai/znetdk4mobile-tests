<?php

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
    
    static protected function action_testvalidator() {
        return \app\tests\TestValidator::runTests();
    }

}
