<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website http://www.znetdk.fr
 * Copyright (C) 2019 Pascal MARTINEZ (contact@znetdk.fr)
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
 * ZnetDK 4 Mobile View: running acceptance tests 
 *
 * File version: 1.2
 * Last update: 06/27/2023
 */

// Test Database status
$connectionString = CFG_SQL_APPL_USR === NULL || CFG_SQL_HOST === NULL || CFG_SQL_APPL_DB === NULL 
        ? NULL : 'User: <b>' . CFG_SQL_APPL_USR . '@' . CFG_SQL_HOST . '</b>, DB: <b>' . CFG_SQL_APPL_DB
        . '</b>, Status: ';
$databaseError = NULL;
$isDatabaseOk = FALSE;
if (is_null($connectionString)) {
   $testDbStatus = '<span class="w3-text-red"><b>not configured properly.</b></span>';
} else {
    try { // Check database connection...
        \Database::getCoreDbConnection();
        \Database::getApplDbConnection();
        if (\Database::areCoreTablesProperlyInstalled($databaseError)) {
            $testDbStatus = $connectionString . '<span class="w3-tag w3-green">OK</span>';
            $isDatabaseOk = TRUE;
        } else {
            $testDbStatus = $connectionString 
                    . '<span class="w3-text-red"><b>Security SQL tables not installed properly.</b></span>';
        }
    } catch (\Exception $e) {
        $testDbStatus = $connectionString . '<span class="w3-text-red"><b>failed to connect.</b></span>';
        $databaseError = $e->getMessage();
    }
}
?>
<h3>Running the tests...</h3>
<p>Welcome to the Acceptance Testing application.</p>
<p>Database: <?php echo $testDbStatus; ?><br>
    PHP Version: <b><?php echo phpversion(); ?></b><br>
    ZnetDK library: <b id="znetdk-library">????</b><br>
ZnetDK version: <b><?php echo ZNETDK_VERSION; ?></b>
</p>
<div class="w3-row-padding w3-stretch">
    <div class="w3-col l2">        
        <select id="z4mtsts-filter-domain" class="w3-select">
            <option value="">All domains</option>
            <option value="action">action</option>
            <option value="ajax">ajax</option>
            <option value="autocomplete">autocomplete</option>
            <option value="form">form</option>
            <option value="list">list</option>
            <option value="modal">modal</option>
            <option value="serverSideCore">serverSideCore</option>
            <option value="z4musers">z4musers</option>
            <option value="z4mprofiles">z4mprofiles</option>
        </select>
    </div>
    <div class="w3-col l2">
        <input id="z4mtsts-filter-testcase" class="w3-input" type="number" min="0" placeholder="all tests">
    </div>
    <div class="w3-col l8">
        <button id="z4mtsts-bt-run" class="w3-btn w3-theme-action" type="button"><i class="fa fa-play"></i> Run</button>
    </div>
</div>
<div id="z4mtsts-console" class="w3-input w3-border w3-margin-top w3-darkgrey w3-hide"></div>
<div id="z4mtsts-progress" class="w3-modal" style="z-index: 10">
    <div class="w3-modal-content">
        <div class="w3-container">
            <p><b>Status</b>: <span class="w3-tag status">???</span></p>
            <p><b>Progress</b>: <span class="percent">0</span>%</p>
            <p><b>Last test run</b>: <span class="last-test">#</span> (<span class="tests-run">#</span> / <span class="test-count">#</span>)</p>
            <button id="z4mtsts-bt-stop" class="w3-button w3-red w3-hide w3-margin-bottom" type="button"><i class="fa fa-stop"></i> Stop</button>
            <button type="button" class="cancel w3-button w3-theme-action w3-margin-bottom"><i class="fa fa-times"></i> Close</button>
        </div>
    </div>    
</div>
<div id="z4mtsts-ui-container" class="w3-hide"></div>
<div class="w3-padding-64"></div>
<script>
    console.log('** Run tests **');
    // Display ZnetDK Mobile library loaded
    (function(){
        let mobileLibraryUri = $('script[src*=engine]').attr('src').replace(
                '/znetdk4mobile/engine/public/js/', '');
        $('#znetdk-library').text(mobileLibraryUri.slice(0, mobileLibraryUri.indexOf('?v')));
    })();
    // Move test execution modal dialog under the body element 
    // (always visible even when another view is displayed) 
    $('#z4mtsts-progress').appendTo('body');
    // Start button click events
    $('#z4mtsts-bt-run').on('click', function(){
        if (!<?php echo $isDatabaseOk ? 'true' : 'false'; ?>) {
            znetdkMobile.messages.removeAll();
            znetdkMobile.messages.add('error', 'Database error',
                "Database for tests is not properly configured or security tables are missing.<br><?php echo $databaseError; ?>", false);
            return;
        }
        if (typeof z4mTestRunner !== 'undefined' && typeof z4mTestCases !== 'undefined') {            
            var currentViewId = znetdkMobile.content.getDisplayedViewId(),
                progressEl = $('#z4mtsts-progress'),
                closeButton = progressEl.find('button.cancel'),
                startButton = $(this), stopButton = $('#z4mtsts-bt-stop'),
                selectedDomain = $('#z4mtsts-filter-domain').val(),
                selectedTestCaseIdx = $('#z4mtsts-filter-testcase').val();
            // Reset
            $('#z4mtsts-console').empty().addClass('w3-hide');
            progressEl.find('.status')
                    .removeClass('w3-red w3-green')
                    .addClass('w3-yellow')
                    .text('Running...');
            progressEl.find('.percent').text('0');
            progressEl.find('.last-test').text('#');
            progressEl.find('.tests-run').text('#');
            progressEl.find('.test-count').text('#');
            closeButton.addClass('w3-hide');
            progressEl.show();
            stopButton.removeClass('w3-hide');
            startButton.prop('disabled', true);
            z4mTestRunner.setTestCases(z4mTestCases);
            z4mTestRunner.setProgressCallback(function(runState){
                progressEl.find('.percent').text(runState.progress);
                progressEl.find('.last-test').text('[' + runState.lastTest.domain + '] ' + runState.lastTest.name);
                progressEl.find('.tests-run').text(runState.run);
                progressEl.find('.test-count').text(runState.total);
            });
            z4mTestRunner.setUIContainerId('z4mtsts-ui-container');
            z4mTestRunner.run(selectedDomain === '' ? undefined : selectedDomain,
                    selectedTestCaseIdx === '' ? undefined : parseInt(selectedTestCaseIdx, 10))
                    .finally(function(){ 
                stopButton.prop('disabled', false).addClass('w3-hide');
                startButton.prop('disabled', false);
                znetdkMobile.content.displayView(currentViewId);
                closeButton.removeClass('w3-hide');
            }).then(function(counters){
                progressEl.find('.status').removeClass('w3-yellow');
                if (counters.progress < 100) {
                    progressEl.find('.status').addClass('w3-pink')
                        .text('Test execution canceled by user');
                } else if (counters.success === counters.total) {
                    progressEl.find('.status').addClass('w3-green')
                        .text('Complete');
                } else if (counters.error > 0) { // Errors
                    progressEl.find('.status').addClass('w3-red')
                        .text(counters.error.toString() + ' tests in error (see console)');
                    logInHtmlConsole('Test cases in error:', z4mTestRunner.getErrors());
                } else if (counters.failed > 0) { // Failed
                    progressEl.find('.status').addClass('w3-orange')
                        .text(counters.failed.toString() + ' tests failed (see console)');
                    logInHtmlConsole('Failed test cases :', z4mTestRunner.getFailed());
                } else if (counters.notTested > 0) { // Not tested
                    progressEl.find('.status').addClass('w3-blue')
                        .text(counters.notTested.toString() + ' cases not tested (see console)');
                    logInHtmlConsole('Cases not tested:', z4mTestRunner.getNotTested());
                } else {
                    progressEl.find('.status').addClass('w3-blue-gray')
                        .text('UNKNOWN STATUS!');
                }
            }).catch (function(error) {                
                progressEl.find('.status').addClass('w3-black')
                        .text('Critical error (see console)');
                console.log('Critical error', error);
            });
        }
        else {
            alert('Testing libraries are missing!');
        }
    });
    // Stop button click events
    $('#z4mtsts-bt-stop').on('click', function(){
        $(this).prop('disabled', true);
        z4mTestRunner.stop();
    });
    // Display failed, errors and not tested in HTML console
    function logInHtmlConsole(title, testCases) {
        console.log(title, testCases);
        $('#z4mtsts-console').append('<h4>' + title + '</h4>');
        $('#z4mtsts-console').append('<ul></ul>');
        testCases.forEach(function(testCase){
            $('#z4mtsts-console ul').append('<li>' 
                + '[' + testCase.domain + '] <b>' + testCase.name + '</b>: ' + testCase.error
                + '</li>');
        });
        $('#z4mtsts-console').removeClass('w3-hide');
    }
</script>