<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website http://www.znetdk.fr
 * ------------------------------------------------------------
 * Custom navigation menu of the application
 * YOU CAN FREELY CUSTOMIZE THE CONTENT OF THIS FILE
 */
namespace app;
class Menu implements \iMenu {

    static public function initAppMenuItems() {
        \MenuManager::addMenuItem(NULL, '_tests', 'Tests', 'fa-cogs');
        \MenuManager::addMenuItem('_tests', 'runtests', 'Run the tests', 'fa-cogs');
        \MenuManager::addMenuItem('_tests', 'helpruntests', 'Help on tests', 'fa-question-circle');
        //\MenuManager::addMenuItem(NULL, 'home', 'Home', 'fa-home');
        
        \MenuManager::addMenuItem(NULL, '_authorizations', LC_MENU_AUTHORIZATION, 'fa-unlock-alt');
        
        /* DYNAMIC MENU CHANGES ON EXECUTION */
        if (!key_exists('no_users_view', $_REQUEST)) {
            \MenuManager::addMenuItem('_authorizations', 'z4musers', LC_MENU_AUTHORIZ_USERS, 'fa-user');
        }
        if (!key_exists('no_profiles_view', $_REQUEST)) {
            \MenuManager::addMenuItem('_authorizations', 'z4mprofiles', LC_MENU_AUTHORIZ_PROFILES, 'fa-key');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' 
                && key_exists('control', $_REQUEST) && $_REQUEST['control'] === 'z4mtsts_testview1') {
            \MenuManager::addMenuItem(NULL, 'z4mtsts_testview1', '** FOR TESTING PURPOSE **');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET' 
                && key_exists('control', $_REQUEST) && $_REQUEST['control'] === 'z4mtsts_testview2') {
            \MenuManager::addMenuItem(NULL, 'z4mtsts_testview2', '** FOR TESTING PURPOSE **');
        }
    }

}