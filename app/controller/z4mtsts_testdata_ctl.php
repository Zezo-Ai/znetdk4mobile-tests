<?php

namespace app\controller;

class z4mtsts_testdata_ctl extends \AppController {

    /**
     * Adds 2 profiles for testing Adding user test case
     * @return \Response
     */
    static protected function action_add2profiles() {
        $testData = self::getProfileTestData();
        $profilesFound = self::getExistingProfiles();
        $existingProfiles = 0;
        foreach ($profilesFound as $profile) {
            if ($profile['profile_name'] === $testData['row1']['profile_name']
                    || $profile['profile_name'] === $testData['row2']['profile_name']) {
                $existingProfiles++;
            }
        }
        if ($existingProfiles === 0) {
            \ProfileManager::storeProfile($testData['row1'], $testData['menuItems']);
            \ProfileManager::storeProfile($testData['row2'], NULL);
        }
        $response = new \Response();
        $response->success = $existingProfiles === 0 || $existingProfiles = 2;
        return $response;
    }

    static protected function action_remove2profiles() {
        $testData = self::getProfileTestData();
        $profilesFound = self::getExistingProfiles();
        foreach ($profilesFound as $profile) {
            if ($profile['profile_name'] === $testData['row1']['profile_name']
                    || $profile['profile_name'] === $testData['row2']['profile_name']) {
                \ProfileManager::removeProfile($profile['profile_id']);
            }
        }
        $response = new \Response();
        $response->success = TRUE;
        return $response;
    }

    static private function getProfileTestData() {
        return [
            'row1' => [
                'profile_name' => 'Z4M Test Profile #1',
                'profile_description' => 'ZnetDK 4 Mobile Test Profile number 1'
            ],
            'menuItems' => ['z4musers', 'z4mprofiles'],
            'row2' => [
                'profile_name' => 'Z4M Test Profile #2',
                'profile_description' => 'ZnetDK 4 Mobile Test Profile number 2'
            ]
        ];
    }

    static private function getExistingProfiles() {
        $profilesFound = [];
        \ProfileManager::getAllProfiles(NULL, NULL, 'profile_name', $profilesFound);
        return $profilesFound;
    }

}
