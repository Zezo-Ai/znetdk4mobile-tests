<?php
/**
* ZnetDK, Starter Web Application for rapid & easy development
* See official website http://www.znetdk.fr 
* ------------------------------------------------------------
* Custom parameters of the application
* YOU CAN FREELY CUSTOMIZE THE CONTENT OF THIS FILE
*/

/** Default selected language when the browser language is not supported by the
 * application
 * @return string 2-character code in ISO 639-1, for example 'fr'
 */ 
define('CFG_DEFAULT_LANGUAGE','en');

/** Relative path or URL of the W3CSS theme file */
define('CFG_MOBILE_W3CSS_THEME','resources/w3css/themes/w3-theme-black.css');

/** Specifies whether the user session expires or not
 * @return 'public'|'private' When set to 'public', the user session expires.
 * <br>When set to 'private', the user session never expires.    
 */
define('CFG_SESSION_DEFAULT_MODE','public');

/** Session Time out in minutes
 * @return integer Number of minutes without user activity before his session expires
 */
define('CFG_SESSION_TIMEOUT', 10);

/** Host name of the machine where the database MySQL is installed.
 * @return string For example, '127.0.0.1' or 'mysql78.perso'
 */
define('CFG_SQL_HOST', '127.0.0.1');

/** TCP/IP port number on which the SQL Server listens.
 * @return string For example, '35105'
 */
define('CFG_SQL_PORT',NULL);

/** Database which contains the tables specially created for the application.
 * @return string For example 'znetdk-db'
 */
define('CFG_SQL_APPL_DB', 'z4m-tests-db');

/** User declared in the database of the application to access to the tables
 *  specially created for business needs
 * @return string For example 'app'
 */
define('CFG_SQL_APPL_USR', 'root');

/** User's password declared in the database of the application.
 * @return string For example 'password'
 */
define('CFG_SQL_APPL_PWD', '');

/** Hides the message to install App */
define('CFG_MOBILE_INSTALL_MESSAGE_DISPLAY_AUTO', FALSE);

define('CFG_APP_JS', serialize(array(
    'applications/' . ZNETDK_APP_NAME . '/public/js/testcases.js',
    'applications/' . ZNETDK_APP_NAME . '/public/js/testrunner.js'
)));

define('CFG_DEV_JS_ENABLED', FALSE);

/* Rights given to the Test User when controller actions are called as web service */
define('CFG_HTTP_BASIC_AUTHENTICATION_ENABLED', TRUE);
define('CFG_ACTIONS_ALLOWED_FOR_WEBSERVICE_USERS', serialize([
    'test_user|z4mtsts_ui_ctl:ajax1',
    'test_user|z4mtsts_server_ctl:required_menu_item',
    'test_user|users:detail',
    'test_user|profiles:detail',
]));

/* DYNAMIC PARAMETERS CHANGES ON EXECUTION */
if (key_exists('CFG_DOWNLOAD_AS_POST_REQUEST_ENABLED', $_REQUEST) && $_REQUEST['CFG_DOWNLOAD_AS_POST_REQUEST_ENABLED'] === 'true') {
    define('CFG_DOWNLOAD_AS_POST_REQUEST_ENABLED', TRUE);
}
if (key_exists('CFG_DISPLAY_ERROR_DETAIL', $_REQUEST) && $_REQUEST['CFG_DISPLAY_ERROR_DETAIL'] === 'true') {
    define('CFG_DISPLAY_ERROR_DETAIL', TRUE);
}
if (key_exists('CFG_VIEW_PAGE_RELOAD', $_REQUEST) && $_REQUEST['CFG_VIEW_PAGE_RELOAD'] === 'true') {
    define('CFG_VIEW_PAGE_RELOAD', TRUE);
}
if (key_exists('CFG_AUTHENT_REQUIRED', $_REQUEST) && $_REQUEST['CFG_AUTHENT_REQUIRED'] === 'true') {
    define('CFG_AUTHENT_REQUIRED', TRUE);
}
if (key_exists('CFG_VIEW_PRELOAD', $_REQUEST) && $_REQUEST['CFG_VIEW_PRELOAD'] === 'true') {
    define('CFG_VIEW_PRELOAD', TRUE);
}
if (key_exists('CFG_PAGE_LAYOUT', $_REQUEST) && $_REQUEST['CFG_PAGE_LAYOUT'] === 'office') {
    define('CFG_PAGE_LAYOUT', 'office');
} else {
    define('CFG_PAGE_LAYOUT', 'mobile');
}
