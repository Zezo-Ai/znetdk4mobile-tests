/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website https://www.znetdk.fr
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
 * ZnetDK Javascript library for mobile page layout
 *
 * File version: 1.3
 * Last update: 08/07/2024
 */
/* global z4m, Promise */

/**
 * Test cases for testing ZnetDK 4 Mobile in version 3.0 and higher
 * Prefixes:
 * 'N - ': Normal case
 * 'A - ': Alternative case
 * 'E - ': Error case
 * @type Object Each property is a domain (action, ajax, autocomplete...) to
 * test. A domain is an array of test cases defined as an object having the
 * following properties:
 * - name: short name of the test case (prefixed if possible by 'N - ', 'A - '
 *   or 'E - ' to indicate the type of case test.
 * - description: the test case description.
 * - testFn: the function to execute for testing the case. This function is
 *   declared with async to be sure a Promise object is returned and accepts a
 *   context variable as parameter. This context variable is an object in which
 *   custom properties can be added to transmit data between test cases.
 *   The default context properties are:
 *   - UIContainerId: id in the DOM of the HTML container dedicated to UI
 *     testing.
 *   - isUIContainerEmptiedBeforeNextTest: if set to false (default value), the
 *     UI container is emptied before execution of the next step.
 *   - prevTestCaseState: execution state of the previous test case (true, false
 *     or -1).
 *   - toggleUIContainer: function to call for showing or hiding the UI
 *     Container.
 *   - pause: stop test case execution for the specified delay in ms. Must be
 *     prefixed by the async JS statement.
 *   - areValuesEqual: function to call to get the expected and obtained values
 *     when test case failed.
 *   - prevTestCaseMessage = error message to display when test case failed or
 *     was not testable;
 */
z4mTestCases = {
/*
    sample: [{
            name: 'N - test case 1',
            description: 'description...',
            testFn: async function(context) {
                await new Promise((resolve) => setTimeout(resolve, 3000));
                return 1===1; // Success
            }
        }, {
            name: 'A - test case 2',
            description: 'description...',
            testFn: async function(context) {
                await new Promise((resolve) => setTimeout(resolve, 3000));
                return -1; // Not tested
            }
        }
    ],
*/
    action: [
        {
            name: 'N - Add custom button',
            description: 'Adds a custom action button in the DOM (addCustomButton)',
            testFn: async function(context) {
                context.customAction = {
                    actionButtonClass: 'z4mtest-action-button',
                    actionButtonIcon: 'fa-diamond',
                    actionButtonColor: 'w3-yellow',
                    isClicked: false
                };
                return context.areValuesEqual(
                    // addCustomButton returns true
                    z4m.action.addCustomButton(
                        context.customAction.actionButtonClass,
                        context.customAction.actionButtonIcon,
                        context.customAction.actionButtonColor), true,
                    // The custom button exists with the expected icon
                    $('.' + context.customAction.actionButtonClass + ' .fa')
                        .hasClass(context.customAction.actionButtonIcon), true);
            }
        }, {
            name: 'N - Register custom button',
            description: 'Registers the custom action button for display on the current view (registerView)',
            testFn: async function(context) {
                const registerViewObj = {},
                        displayedViewId = z4m.content.getDisplayedViewId();
                registerViewObj[context.customAction.actionButtonClass] = {
                    isVisible: true,
                    callback: function () {
                        context.customAction.isClicked = true;
                    }
                };
                return z4m.action.registerView(displayedViewId,
                    registerViewObj);
            }
        }, {
            name: 'N - Display custom button',
            description: 'Display the custom button on the current view (toggle)',
            testFn: async function(context) {
                z4m.action.toggle();
                return $('.' + context.customAction.actionButtonClass).is(':visible');
            }
        }, {
            name: 'N - Click custom button',
            description: 'Click handler executed on custom action button click',
            testFn: async function(context) {
                $('.' + context.customAction.actionButtonClass).trigger('click');
                await new Promise((resolve) => setTimeout(resolve, 1000));
                return context.customAction.isClicked === true;
            }
        }, {
            name: 'A - Hide custom button',
            description: 'Hide all the action buttons of the current view (hide)',
            testFn: async function(context) {
                z4m.action.hide();
                return !$('.' + context.customAction.actionButtonClass).is(':visible');
            }
        },{
            name: 'A - Remove custom button',
            description: 'Removes the custom action button (removeCustomButton)',
            testFn: async function(context) {
                const isRemoved = z4m.action.removeCustomButton(
                        context.customAction.actionButtonClass),
                    isUnregistered = true, // Not testable for the current version
                    doesNotExistInDom = $('.'
                        + context.customAction.actionButtonClass).length === 0;
                // Last test case for the domain, so context is cleaned
                delete (context.customAction);
                // removal confirmed and is unregistered from the views and
                // no longer exists in DOM
                return isRemoved && isUnregistered && doesNotExistInDom;
            }
        }
    ],
    ajax: [
        {
            name: 'N - View loading', //0
            description: 'Loading of the view named \'z4mtsts_testview1\' and adding to the DOM',
            testFn: async function(context) {
                var request = z4m.ajax.loadView('z4mtsts_testview1', $('#' + context.UIContainerId)),
                    result = await request.promise();
                return result === '<p>Test view 1</p>';
            }
        },
        {
            name: 'N - Ajax request to controller, JSON response', //1
            description: 'Retrieving data through an AJAX request sent to \'z4mtsts_ui_ctl:ajax1\'',
            testFn: async function() {
                var request = z4m.ajax.request({
                    controller: 'z4mtsts_ui_ctl',
                    action: 'ajax1',
                    data: {value1: 456, value2: 'ABC'}
                }), result = await request.promise();
                return typeof result === 'object'
                    && result.hasOwnProperty('success')
                    && result.success === true;
            }
        },
        {
            name: 'N - Fetch POST request to download a TEXT file', //2
            description: 'Retrieving TXT file through an AJAX request sent to \'z4mtsts_ui_ctl:file\'',
            testFn: async function() {
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var formData = new FormData();
                formData.append('control', 'z4mtsts_ui_ctl');
                formData.append('action', 'file');
                if (ajaxURL.hasOwnProperty('paramName')) {
                    formData.append(ajaxURL.paramName, ajaxURL.paramValue);
                }
                formData.append('CFG_DOWNLOAD_AS_POST_REQUEST_ENABLED', 'true');
                const request = await window.fetch(ajaxURL.url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 200 
                    && typeof result === 'string'
                    && result === 'Content of the text file.';
            }
        },
        {
            name: 'N - Fetch POST request to download a CSV file', //3
            description: 'Retrieving CSV file through an AJAX request sent to \'z4mtsts_ui_ctl:csv\'',
            testFn: async function() {
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var formData = new FormData();
                formData.append('control', 'z4mtsts_ui_ctl');
                formData.append('action', 'csv');
                if (ajaxURL.hasOwnProperty('paramName')) {
                    formData.append(ajaxURL.paramName, ajaxURL.paramValue);
                }
                formData.append('CFG_DOWNLOAD_AS_POST_REQUEST_ENABLED', 'true');
                const request = await window.fetch(ajaxURL.url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 200 
                    && typeof result === 'string'
                    && result.indexOf('Content of the CSV file.') > -1;
            }
        },
        {
            name: 'A - Fetch POST request without UI token', //4
            description: 'Test token mismatch when a fetch request is sent to \'z4mtsts_ui_ctl:ajax1\'',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var formData = new FormData();
                formData.append('control', 'z4mtsts_ui_ctl');
                formData.append('action', 'ajax1');
                if (ajaxURL.hasOwnProperty('paramName')) {
                    formData.append(ajaxURL.paramName, ajaxURL.paramValue);
                }
                const request = await window.fetch(ajaxURL.url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }), result = await request.json();
                return request.status === 200 
                    && typeof result === 'object'
                    && result.hasOwnProperty('success')
                    && result.success === false
                    && result.hasOwnProperty('msg')
                    && result.msg === 'UI token is invalid.';
            }
        },
        {
            name: 'A - Fetch POST request with controller and action as GET parameters',//5
            description: 'Test if POST request with controller and action as GET parameters is allowed through a fetch request sent to \'z4mtsts_ui_ctl:ajax1\'',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var formData = new FormData();
                formData.append('uitk', $('body').data('ui-token'));
                formData.append('value1', 456);
                formData.append('value2', 'ABC');
                if (ajaxURL.hasOwnProperty('paramName')) {
                    formData.append(ajaxURL.paramName, ajaxURL.paramValue);
                }
                const request = await window.fetch(ajaxURL.url + '?control=z4mtsts_ui_ctl&action=ajax1', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }), result = await request.json();
                return request.status === 200 
                    && typeof result === 'object'
                    && result.hasOwnProperty('success')
                    && result.success === true;
            }
        },{
            name: 'A - Fetch GET request CFG_VIEW_PAGE_RELOAD=TRUE, controller and action specified',//6
            description: 'Testing if GET request is working when controller and action are specified as GET parameters.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url + '?control=z4mtsts_testview1&action=show';
                if (ajaxURL.hasOwnProperty('paramName')) {
                    fullURL += '&' + ajaxURL.paramName + '=' + ajaxURL.paramValue;
                }
                fullURL += '&CFG_VIEW_PAGE_RELOAD=true';
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 200
                    && typeof result === 'string'
                    && result.indexOf('<html') > -1
                    && result.indexOf('<p>Test view 1</p>') > -1
                    && result.indexOf('</html>') > -1;
            }
        },{
            name: 'A - Fetch GET request CFG_VIEW_PAGE_RELOAD=TRUE, existing SEO string specified in URL',//7
            description: 'Testing if GET request is working when SEO string is specified in URL.',
            testFn: async function(context) {                
                context.prevTestCaseMessage = 'Not testable: appl=test not taken in account if SEO string in URL.';
                return -1;
            }
        },{
            name: 'E - Fetch GET request CFG_VIEW_PAGE_RELOAD=TRUE, error 500 (PHP runtime error)',//8
            description: 'Testing if GET request returns generic message when a critical error occurres in the requested view.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url + '?control=z4mtsts_testview2&action=show';
                if (ajaxURL.hasOwnProperty('paramName')) {
                    fullURL += '&' + ajaxURL.paramName + '=' + ajaxURL.paramValue;
                }
                fullURL += '&CFG_VIEW_PAGE_RELOAD=true';
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 500 
                        && typeof result === 'string'
                        && result.indexOf('Technical hitch') > -1
                        && result.indexOf('Please retry later.') > -1
                        && result.indexOf('</html>') > -1;
            }
        },{
            name: 'E - Fetch GET error 403 (missing page)',//9
            description: 'Testing if GET request to a missing page \'byebye\' returns status 403 and expected \'httperror.php\' view.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url.replace('index.php', '') + 'byebye';
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 403
                        && typeof result === 'string'
                        && result.indexOf('HTTP Error 403!') > -1
                        && result.indexOf('</html>') > -1;
            }
        },{
            name: 'E - Fetch GET error 403 (forbidden)',//10
            description: 'Testing if GET request to a forbidden page returns status 403 and expected \'httperror.php\' view.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url.replace('index.php', '') + 'engine/';
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 403
                        && typeof result === 'string'
                        && result.indexOf('HTTP Error 403!') > -1
                        && result.indexOf('</html>') > -1;
            }
        },{
            name: 'E - Fetch GET error 500 (config error PRM-004)',//11
            description: 'Test if GET request to App with CFG_VIEW_PAGE_RELOAD=true & CFG_AUTHENT_REQUIRED=true returns status 500.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url;
                fullURL += '?CFG_VIEW_PAGE_RELOAD=true';
                fullURL += '&CFG_AUTHENT_REQUIRED=true';
                if (ajaxURL.hasOwnProperty('paramName')) {
                    fullURL += '&' + ajaxURL.paramName + '=' + ajaxURL.paramValue;
                }
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 500
                        && typeof result === 'string'
                        && result.indexOf('HTTP Error 500!') > -1
                        && result.indexOf('</html>') > -1;
            }
        },{
            name: 'E - Fetch GET error 500 (config error PRM-005)',//12
            description: 'Test if GET request to App with CFG_VIEW_PAGE_RELOAD=true & CFG_VIEW_PRELOAD=true returns status 500.',
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url;
                fullURL += '?CFG_VIEW_PAGE_RELOAD=true';
                fullURL += '&CFG_VIEW_PRELOAD=true';
                if (ajaxURL.hasOwnProperty('paramName')) {
                    fullURL += '&' + ajaxURL.paramName + '=' + ajaxURL.paramValue;
                }
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 500
                        && typeof result === 'string'
                        && result.indexOf('HTTP Error 500!') > -1
                        && result.indexOf('</html>') > -1;
            }
        },{
            name: 'E - Fetch GET error 500 (config error PRM-006)',//13
            description: 'Test if GET request to App with CFG_VIEW_PAGE_RELOAD=true & CFG_PAGE_LAYOUT=office returns status 500.',
            // Extra errors in 'errors.log': LAY-007 and DEP-001
            testFn: async function() {                
                const ajaxURL = z4m.ajax.getParamsFromAjaxURL();
                var fullURL = ajaxURL.url;
                fullURL += '?CFG_VIEW_PAGE_RELOAD=true';
                fullURL += '&CFG_PAGE_LAYOUT=office';
                if (ajaxURL.hasOwnProperty('paramName')) {
                    fullURL += '&' + ajaxURL.paramName + '=' + ajaxURL.paramValue;
                }
                const request = await window.fetch(fullURL, {
                    method: 'GET',
                    credentials: 'same-origin'
                }), result = await request.text();
                return request.status === 500
                        && typeof result === 'string'
                        && result.indexOf('HTTP Error 500!') > -1
                        && result.indexOf('</html>') > -1;
            }
        },
        {
            name: 'E - Ajax request to a forbidden controller', //14
            description: 'Ajax request to a controller action that is not allowed, Error 403 expected.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_ui_ctl',
                        action: 'not_allowed',
                        errorCallback: function(response) {
                            resolve(response.status === 403);
                            return false;
                        }
                    });
                });
            }
        },
        {
            name: 'E - Ajax request to missing controller',
            description: 'Ajax request to a controller action that does not exist, Error 404 expected.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'missing_controller',
                        action: 'ajax1',
                        errorCallback: function(response) {
                            resolve(response.status === 404);
                            return false;
                        }
                    });
                });
            }
        },
        {
            name: 'E - Ajax request error on PHP exception (CFG_DISPLAY_ERROR_DETAIL = TRUE)',
            description: 'Ajax request to a controller action that triggers a PHP Exception not catched, Error 500 expected with error details.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_ui_ctl',
                        action: 'ajaxexception',
                        data: {
                            CFG_DISPLAY_ERROR_DETAIL: true
                        },
                        errorCallback: function(response) {
                            resolve(response.status === 500
                                    && response.responseJSON.msg.indexOf('This is a not catched exception.') > -1);
                            return false;
                        }
                    });
                });
            }
        },
        {
            name: 'E - Ajax request error on PHP exception (CFG_DISPLAY_ERROR_DETAIL = FALSE)',
            description: 'Ajax request to a controller action that triggers a PHP Exception not catched, Error 500 expected with generic message.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_ui_ctl',
                        action: 'ajaxexception',
                        errorCallback: function(response) {
                            resolve(response.status === 500
                                    && response.responseJSON.msg.indexOf('Please retry later.') > -1);
                            return false;
                        }
                    });
                });
            }
        },
        {
            name: 'E - Ajax request with PHP runtime error (CFG_DISPLAY_ERROR_DETAIL = TRUE)',
            description: 'Ajax request to a controller action that triggers a PHP runtime error, Error 500 expected with error details.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_ui_ctl',
                        action: 'ajax_runtime_error',
                        data: {
                            CFG_DISPLAY_ERROR_DETAIL: true
                        },
                        errorCallback: function(response) {
                            resolve(response.status === 500
                                    && response.responseJSON.msg.indexOf('Call to undefined function') > -1);
                            return false;
                        }
                    });
                });
            }
        },
        {
            name: 'E - Ajax request with PHP runtime error (CFG_DISPLAY_ERROR_DETAIL = FALSE)',
            description: 'Ajax request to a controller action that triggers a PHP runtime error, Error 500 expected with generic message.',
            testFn: async function() {
                return await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_ui_ctl',
                        action: 'ajax_runtime_error',
                        errorCallback: function(response) {
                            resolve(response.status === 500
                                    && response.responseJSON.msg.indexOf('Please retry later.') > -1);
                            return false;
                        }
                    });
                });
            }
        }
    ],
    autocomplete: [{
            name: 'N - Getting suggestions',
            description: 'Suggestions retrieved from the remote controller action (rendering callback)',
            testFn: async function(context) {
                context.isUIContainerEmptiedBeforeNextTest = true;
                context.toggleUIContainer();
                $('#' + context.UIContainerId).append('<input id="my-autocomplete" class="w3-input" type="search">');
                return await new Promise((resolve) => {
                    var suggestions = [];
                    z4m.autocomplete.make('#my-autocomplete', {
                        controller: 'z4mtsts_ui_ctl',
                        action: 'autocomplete1'
                    }, null, function(item){
                        suggestions.push(item.label);
                        if (suggestions.length === 3) {
                            resolve(suggestions[0] === 'abc'
                                    && suggestions[1] === 'abcd'
                                    && suggestions[2] === 'abcde');
                        }
                        return item.label;
                    });
                    $('#my-autocomplete').val('abc');
                    $('#my-autocomplete').trigger('input').trigger('focus');
                });
            }
        }, {
            name: 'N - Displaying suggestions',
            description: 'Suggestions retrieved from the remote controller action (selection callback)',
            testFn: async function(context) {
                $('#' + context.UIContainerId).append('<input id="my-autocomplete" class="w3-input" type="search">');
                return await new Promise((resolve) => {
                    z4m.autocomplete.make('#my-autocomplete', {
                        controller: 'z4mtsts_ui_ctl',
                        action: 'autocomplete1'
                    }, function(selectedItem){
                        resolve(selectedItem.label === 'abcd');
                    });
                    $('#my-autocomplete').val('abc');
                    $('#my-autocomplete').trigger('input').trigger('focus');
                    setTimeout(function(){
                        $('#my-autocomplete').next('ul').find('li:nth-child(2)').trigger('click');
                    }, 1000);
                });
            }
        }
    ],
    form: [{
            name: 'N - Init Form from a single object',
            description: 'Form initialization from a single JS object (init method)',
            testFn: async function(context) {
                const formDef = `<form>
    <input type="text" name="input_text">
    <input type="checkbox" name="input_checkbox_1" value="1">
    <input type="checkbox" name="input_checkbox_2" value="2">
    <input type="checkbox" name="input_checkbox_multiple[]" value="CB1">
    <input type="checkbox" name="input_checkbox_multiple[]" value="CB2">
    <input type="checkbox" name="input_checkbox_multiple[]" value="CB3">
    <input type="radio" name="input_radio" value="1">
    <input type="radio" name="input_radio" value="2">
    <input type="radio" name="input_radio" value="3">
    <textarea name="textarea"></textarea>
    <select name="select_one">
        <option value="">EMPTY</option>
        <option value="1">Label 1</option>
        <option value="2">Label 2</option>
        <option value="3">Label 3</option>
    </select>
    <select name="select_multiple[]" multiple>
        <option>EMPTY</option>
        <option value="A">Label A</option>
        <option value="B">Label B</option>
        <option value="C">Label C</option>
    </select>
    <button type="submit"></button>
</form>`;
                context.isUIContainerEmptiedBeforeNextTest = false;
                context.toggleUIContainer(false);
                var UIContainer = $('#' + context.UIContainerId);
                UIContainer.append(formDef);
                var formObj = z4m.form.make($('#' + context.UIContainerId + ' form'));
                formObj.init({
                    input_text: 'Hello John',
                    input_checkbox_1: '',
                    input_checkbox_2: '2',
                    'input_checkbox_multiple[]': ['CB2', 'CB3'],
                    input_radio: '3',
                    textarea: 'How do you do?',
                    select_one: '2',
                    'select_multiple[]': ['A', 'C']
                });
                return UIContainer.find('input[name=input_text]').val() === 'Hello John'
                    && typeof UIContainer.find('input[name=input_checkbox_1]:checked').val() === 'undefined'
                    && UIContainer.find('input[name=input_checkbox_2]:checked').val() === '2'
                    && UIContainer.find('input[name="input_checkbox_multiple[]"]:checked').length === 2
                        && UIContainer.find('input[name="input_checkbox_multiple[]"]:checked')[0].value === 'CB2'
                        && UIContainer.find('input[name="input_checkbox_multiple[]"]:checked')[1].value === 'CB3'
                    && UIContainer.find('input[name=input_radio]:checked').val() === '3'
                    && UIContainer.find('textarea[name=textarea]').val() === 'How do you do?'
                    && UIContainer.find('select[name=select_one]').val() === '2'
                    && JSON.stringify(UIContainer.find('select[name="select_multiple[]"]').val())
                        === JSON.stringify(['A','C']);
            }
        }, {
            name: 'N - Read individually values of a filled form',
            description: 'Reading filled form values by calling the getInputValue() method for each form\'s entry field',
            testFn: async function(context) {
                var formObj = z4m.form.make($('#' + context.UIContainerId + ' form'));
                return formObj.getInputValue('input_text') === 'Hello John'
                    && formObj.getInputValue('input_checkbox_1') === ''
                    && formObj.getInputValue('input_checkbox_2') === '2'
                    && JSON.stringify(formObj.getInputValue('input_checkbox_multiple[]'))
                        === JSON.stringify(['CB2','CB3'])
                    && formObj.getInputValue('input_radio') === '3'
                    && formObj.getInputValue('textarea') === 'How do you do?'
                    && formObj.getInputValue('select_one') === '2'
                    && JSON.stringify(formObj.getInputValue('select_multiple[]'))
                        === JSON.stringify(['A','C']);
            }
        }, {
            name: 'N - Form reset',
            description: 'Resetting a filled Form by calling the reset() method',
            testFn: async function(context) {
                var formObj = z4m.form.make($('#' + context.UIContainerId + ' form')),
                    UIContainer = $('#' + context.UIContainerId);
                formObj.reset();
                return UIContainer.find('input[name=input_text]').val().length === 0
                    && typeof UIContainer.find('input[name=input_checkbox_1]:checked').val() === 'undefined'
                    && typeof UIContainer.find('input[name=input_checkbox_2]:checked').val() === 'undefined'
                    && typeof UIContainer.find('input[name="input_checkbox_multiple[]"]:checked').val() === 'undefined'
                    && typeof UIContainer.find('input[name=input_radio]:checked').val() === 'undefined'
                    && UIContainer.find('textarea[name=textarea]').val().length === 0
                    && UIContainer.find('select[name=select_one]').val().length === 0
                    && UIContainer.find('select[name="select_multiple[]"]').val().length === 0;
            }
        }, {
            name: 'A - Read individually form values of an empty form',
            description: 'Reading empty form values by calling the getInputValue() method for each form\'s entry field',
            testFn: async function(context) {
                var formObj = z4m.form.make($('#' + context.UIContainerId + ' form'));
                return formObj.getInputValue('input_text') === ''
                    && formObj.getInputValue('input_checkbox_1') === ''
                    && formObj.getInputValue('input_checkbox_2') === ''
                    && JSON.stringify(formObj.getInputValue('input_checkbox_multiple[]'))
                        === JSON.stringify([])
                    && formObj.getInputValue('input_radio') === null
                    && formObj.getInputValue('textarea') === ''
                    && formObj.getInputValue('select_one') === ''
                    && JSON.stringify(formObj.getInputValue('select_multiple[]'))
                        === JSON.stringify([]);
            }
        }, {
            name: 'N - Set input values individually',
            description: 'Settings Form values by calling the setInputValue() method for each form\'s entry field',
            testFn: async function(context) {
                var formObj = z4m.form.make($('#' + context.UIContainerId + ' form'));
                formObj.setInputValue('input_text', 'Hello John');
                formObj.setInputValue('input_checkbox_1', '');
                formObj.setInputValue('input_checkbox_2', '2');
                formObj.setInputValue('input_checkbox_multiple[]', ['CB2','CB3']);
                formObj.setInputValue('input_radio', '3');
                formObj.setInputValue('textarea', 'How do you do?');
                formObj.setInputValue('select_one', '2');
                formObj.setInputValue('select_multiple[]', ['A','C']);

                return formObj.getInputValue('input_text') === 'Hello John'
                    && formObj.getInputValue('input_checkbox_1') === ''
                    && formObj.getInputValue('input_checkbox_2') === '2'
                    && JSON.stringify(formObj.getInputValue('input_checkbox_multiple[]'))
                        === JSON.stringify(['CB2','CB3'])
                    && formObj.getInputValue('input_radio') === '3'
                    && formObj.getInputValue('textarea') === 'How do you do?'
                    && formObj.getInputValue('select_one') === '2'
                    && JSON.stringify(formObj.getInputValue('select_multiple[]'))
                        === JSON.stringify(['A','C']);
            }
        }, {
            name: 'N - Load form values from a remote controller action',
            description: 'Initialization of the Form values by calling the load() method (controller=\'z4mtsts_ui_ctl\' , action=\'form1\')',
            testFn: async function(context) {
                var formEl = $('#' + context.UIContainerId + ' form'), formObj;
                formEl.attr('data-zdk-load', 'z4mtsts_ui_ctl:form1');
                formObj = z4m.form.make(formEl);
                return await new Promise((resolve) => {
                    if (formObj.load(57, function(){
                        resolve(formObj.getInputValue('input_text') === 'Hello John'
                            && formObj.getInputValue('input_checkbox_1') === ''
                            && formObj.getInputValue('input_checkbox_2') === '2'
                            && JSON.stringify(formObj.getInputValue('input_checkbox_multiple[]'))
                                === JSON.stringify(['CB2','CB3'])
                            && formObj.getInputValue('input_radio') === '3'
                            && formObj.getInputValue('textarea') === 'How do you do?'
                            && formObj.getInputValue('select_one') === '2'
                            && JSON.stringify(formObj.getInputValue('select_multiple[]'))
                                === JSON.stringify(['A','C']));
                    }) === false) {
                        resolve(false);
                    }
                });
            }
        }, {
            name: 'A - Display custom error',
            description: 'Form showError method',
            testFn: async function(context) {
                var formEl = $('#' + context.UIContainerId + ' form'), formObj,
                        errorMsg = 'My custom Error';
                formObj = z4m.form.make(formEl);
                formObj.showError(errorMsg);
                return (formEl.find('div.alert').first().text().trim() === errorMsg);
            }
        }, {
            name: 'A - Hide custom error',
            description: 'Form hideError method',
            testFn: async function(context) {
                var formEl = $('#' + context.UIContainerId + ' form'), formObj;
                formObj = z4m.form.make(formEl);
                formObj.hideError();
                return (formEl.find('div.alert').length === 0);
            }
        }, {
            name: 'N - Form submit success',
            description: 'A success response is returned by the remote controller action on form submit.',
            testFn: async function(context) {
                var formEl = $('#' + context.UIContainerId + ' form');
                formEl.attr('data-zdk-submit', 'z4mtsts_ui_ctl:form2');
                return await new Promise((resolve) => {
                    var formObj = z4m.form.make(formEl, function(response){
                        if (response.success !== true) {
                            resolve(false);
                        }
                        setTimeout(function(){
                            const messageReturned = $('body .z4m-snackbar').text().trim();
                            resolve(context.areValuesEqual(messageReturned, 'Form submit succeeded.'));
                        }, 500);
                    });
                    formObj.setInputValue('input_text', 'Hello John');
                    formObj.setInputValue('input_checkbox_1', '');
                    formObj.setInputValue('input_checkbox_2', '2');
                    formObj.setInputValue('input_checkbox_multiple[]', ['CB2','CB3']);
                    formObj.setInputValue('input_radio', '3');
                    formObj.setInputValue('textarea', 'How do you do?');
                    formObj.setInputValue('select_one', '2');
                    formObj.setInputValue('select_multiple[]', ['A','C']);
                    formEl.find('button[type=submit]').trigger('click');
                });
            }
        }, {
            name: 'N - Form submit failed',
            description: 'A failed response is returned by the remote controller action on form submit.',
            testFn: async function(context) {
                context.isUIContainerEmptiedBeforeNextTest = true;
                var formEl = $('#' + context.UIContainerId + ' form');
                formEl.attr('data-zdk-submit', 'z4mtsts_ui_ctl:form2');
                return await new Promise((resolve) => {
                    var formObj = z4m.form.make(formEl, function(response){
                        if (response.success !== false) {
                            resolve(false);
                        }
                        setTimeout(function(){
                            resolve(formEl.find('div.alert').first().text().trim()
                                    === 'Form submit failed.');
                        }, 500);
                    });
                    formObj.setInputValue('input_text', 'Wrong value');
                    formEl.find('button[type=submit]').trigger('click');
                });
            }
        }
    ],
    list: [{
            name: 'N - Data list instantiation',
            description: 'Data list creation and loading',
            testFn: async function(context) {
                context.isUIContainerEmptiedBeforeNextTest = false;
                context.toggleUIContainer(true);
                var UIContainer = $('#' + context.UIContainerId);
                const dataListDef = `<ul id="z4mtsts-data-list" class="w3-ul" data-zdk-load="z4mtsts_ui_ctl:datalist1">
                    <li data-id="{{id}}"><a class="edit">{{label}}</a></li>
                </ul>`;
                UIContainer.append(dataListDef);
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                return await new Promise((resolve) => {
                    dataList.refresh();
                    dataList.loadedCallback = function(rowCount, pageNumber) {
                        // Extract the total number of rows displayed on the horizontal menu
                        const currentViewId = z4m.content.getDisplayedViewId(),
                            horizontalMenu = z4m.navigation.getHorizontalMenu(),
                            menuItem = horizontalMenu.find('a[data-view_id="'+ currentViewId + '"]'),
                            rowTotal = parseInt(menuItem.find('.row-count').text().trim()
                                    .replace('(', '').replace(')',''), 10);
                        // Return test case status according to the expected values
                        resolve(rowCount === 20 && pageNumber === 1 && rowTotal === 100);
                    };
                });
            }
        }, {
            name: 'N - Data list refresh',
            description: 'Data list row values checking',
            testFn: async function(context) {
                var isOk = true;
                $('#z4mtsts-data-list li').each(function(index){
                    const rowId = index + 1;
                    if ($(this).data('id') !== rowId
                            || $(this).text() !== 'Item ' + rowId) {
                        isOk = false;
                        return false;
                    }
                });
                return isOk;
            }
        }, {
            name: 'N - Get Data List Object later',
            description: 'Getting the Data list object of an existing Data List',
            testFn: async function(context) {
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                return context.areValuesEqual(
                        dataList !== null, true,
                        typeof dataList, 'object',
                        dataList.hasOwnProperty('element'), true,
                        dataList.element.attr('id'), 'z4mtsts-data-list'
                );
            }
        }, {
            name: 'A - Catching data loaded events (afterpageloaded event)',
            description: 'Catches the afterpageloaded event after new items are loaded in the list',
            testFn: async function(context) {
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                dataList.refresh();
                return await new Promise(function(resolve){
                    $('#z4mtsts-data-list').one('afterpageloaded', function(event, listObj, rowCount, pageNumber){
                        z4m.navigation.clearRowCountFromHorizontalMenuItem('runtests');
                        resolve(context.areValuesEqual(
                            listObj.element.attr('id'), 'z4mtsts-data-list',
                            rowCount, 20,
                            pageNumber, 1
                        ));
                    });
                });
            }
        }, {
            name: 'N - List scrolling (loadedCallback)',
            description: 'Scrolls the data list until all the rows be displayed.',
            testFn: async function(context) {
                const dataList = z4m.list.make('#z4mtsts-data-list', false, false),
                        listHeight = dataList.element.height();
                var currentTotalLoadedRow = 0, currentPageNumber = 0;
                // Loading callback
                dataList.loadedCallback = function(rowCount, pageNumber) {
                    currentTotalLoadedRow += rowCount;
                    currentPageNumber = pageNumber;
                };
                var newScrollPos = listHeight + listHeight/2;
                while (currentPageNumber < 5) {
                    $(window).scrollTop(newScrollPos);
                    await context.pause(500);
                    newScrollPos += listHeight;
                }
                this.loadedCallback === null;
                return context.areValuesEqual(
                    currentPageNumber, 5,
                    currentTotalLoadedRow, 80
                );
            }
        }, {
            name: 'N - Catching onAdd events (modal dialog opened)',
            description: 'Catches the onAdd event when clicking the Add button and displays the Add modal dialog',
            testFn: async function(context) {
                var UIContainer = $('#' + context.UIContainerId);
                const modalDef = `<div id="z4mtsts-data-list-modal" class="w3-modal">
    <div class="w3-modal-content">
        <div class="w3-container">
            <h4>My modal dialog box</h4>
            <p>Modal dialog for tests.</p>
            <form class="inner-form" data-zdk-load="z4mtsts_ui_ctl:datalistdetail1">
                <input name="id">
                <input name="label">
            </form>
            <button type="button" class="cancel w3-button w3-red w3-margin-bottom">Close</button>
        </div>
    </div>
</div>`;
                UIContainer.append(modalDef);
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                const modalOpenedPromise = new Promise(function(resolve){
                    $('#z4mtsts-data-list-modal').one('aftershow', function(event, modalObj){
                        resolve(true);
                        // The modal is closed
                        modalObj.close();
                    });
                });
                const onAddPromise = new Promise(function(resolve){
                    dataList.setModal('#z4mtsts-data-list-modal', true, function(innerForm){
                        // onAdd callback
                        if (!innerForm.element.hasClass('inner-form')) {
                            context.prevTestCaseMessage = 'CSS class missing for the input form.';
                            resolve(false);
                        } else if (this.element.attr('id') !== 'z4mtsts-data-list-modal') {
                            resolve(false);
                        } else {
                            resolve(true);
                        }
                    });
                    // Click the Add button
                    $('#zdk-mobile-action-add').trigger('click');
                });
                return await Promise.all([modalOpenedPromise, onAddPromise]).then(function(values){
                    // Add button is hidden
                    z4m.action.views[z4m.content.getParentViewId($('#z4mtsts-ui-container'))].add.isVisible = false;
                    z4m.action.toggle();
                    // End of test case
                    return values[0] === true && values[1] === true;
                });
             }
        }, {
            name: 'A - Catching onAdd events (aborting modal dialog opening)',
            description: 'Catches the onAdd event and returns false in the callback function to prevent modal dialog opening',
            testFn: async function(context) {
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                dataList.setModal('#z4mtsts-data-list-modal', true, function(){
                    // onAdd callback
                    return false; // Modal must not be opened
                });
                // Click the Add button
                $('#zdk-mobile-action-add').trigger('click');
                // Pause 500 ms
                await context.pause(500);
                // Add button is hidden
                z4m.action.views[z4m.content.getParentViewId($('#z4mtsts-ui-container'))].add.isVisible = false;
                z4m.action.toggle();
                // Check if modal is opened or not
                return $('#z4mtsts-data-list-modal').is(':visible') === false;
            }
        }, {
            name: 'N - Catching onEdit events (modal dialog opened)',
            description: 'Catches the onEdit event when clicking a list item and displays the Edit modal dialog',
            testFn: async function(context) {
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                const modalOpenedPromise = new Promise(function(resolve){
                    $('#z4mtsts-data-list-modal').one('aftershow', function(event, modalObj){
                        const formObj = modalObj.getInnerForm();
                        if (formObj.getInputValue('id') !== '6') {
                            context.prevTestCaseMessage = 'ID !== 6 in the form';
                            resolve(false);
                        } else if (formObj.getInputValue('label') !== 'Item 6') {
                            context.prevTestCaseMessage = 'Label !== Item 6 in the form';
                            resolve(false);
                        } else {
                            resolve(true);
                        }
                        // The modal is closed
                        modalObj.close();
                    });
                });
                const onEditPromise = new Promise(function(resolve){
                    dataList.setModal('#z4mtsts-data-list-modal', true, null, function(innerForm, response){
                        // onEdit callback
                        if (!innerForm.element.hasClass('inner-form')) {
                            context.prevTestCaseMessage = 'CSS class missing for the input form.';
                            resolve(false);
                        } else if (this.element.attr('id') !== 'z4mtsts-data-list-modal') {
                            context.prevTestCaseMessage = 'The modal element ID is invalid.';
                            resolve(false);
                        } else if (response.id !== '6') {
                            context.prevTestCaseMessage = 'ID !== 6 in the Server response';
                            resolve(false);
                        } else if (response.label !== 'Item 6') {
                            context.prevTestCaseMessage = 'Label !== Item 6 in the Server response';
                            resolve(false);
                        } else {
                            resolve(true);
                        }
                    });
                    // Click the sixth row
                    $('#z4mtsts-data-list li:nth-of-type(6) a').trigger('click');
                });
                return await Promise.all([modalOpenedPromise, onEditPromise]).then(function(values){
                    return context.areValuesEqual(
                            values[0], true,
                            values[1], true
                    );
                });
            }
        }, {
            name: 'A - Catching onEdit events (aborting modal dialog opening)',
            description: 'Catches the onEdit event and returns false in the callback function to prevent modal dialog opening',
            testFn: async function(context) {
                var dataList = z4m.list.make('#z4mtsts-data-list', false, false);
                dataList.setModal('#z4mtsts-data-list-modal', true, null, function(){
                    // onEdit callback
                    return false; // Modal must not be opened
                });
                // Click the sixth row
                $('#z4mtsts-data-list li:nth-of-type(6) a').trigger('click');
                // Pause 500 ms
                await context.pause(500);
                // Add button is hidden
                z4m.action.views[z4m.content.getParentViewId($('#z4mtsts-ui-container'))].add.isVisible = false;
                z4m.action.toggle();
                // Check if modal is opened or not
                return $('#z4mtsts-data-list-modal').is(':visible') === false;
            }
        }
    ],
    modal: [{ /* 100% tested */
            name: 'N - Modal instantiation',
            description: 'Instantiates a modal dialog',
            testFn: async function(context) {
                const modalDef = `
<div class="w3-modal z4mtsts-modal-dialog">
    <div class="w3-modal-content w3-card-4">
        <header class="w3-container w3-theme-d5">
            <span class="close w3-button w3-xlarge w3-hover-theme w3-display-topright"><i class="fa fa-times-circle fa-lg"></i></span>
            <h4>
                <i class="fa fa-diamond fa-lg"></i>
                <span class="title">Modal for testing...</span>
            </h4>
        </header>
        <div class="w3-container">
            <p>Testing modal...</p>
            <form data-zdk-submit="z4mtsts_ui_ctl:modal1">
                <label>Label</label>
                <input class="w3-input" type="text" name="label">
                <label>Value</label>
                <input class="w3-input" type="text" name="value">
                <button class="w3-button w3-green w3-margin-top w3-margin-bottom" type="submit">Submit</button>
            </form>
        </div>
        <div class="w3-container w3-border-top w3-border-theme w3-padding-16 w3-theme-l4">
            <button type="button" class="cancel w3-button w3-red">
                <i class="fa fa-close fa-lg"></i>
                Close
            </button>
        </div>
    </div>
</div>`;
                // Modal HTML definition added to the DOM
                $('#'  + context.UIContainerId).append(modalDef);
                // Modal object is instantiated
                const modalSelector = '#' + context.UIContainerId
                        + ' .z4mtsts-modal-dialog';
                const modal = z4m.modal.make(modalSelector);
                // Modal object memorized in context for next test cases
                context.customModal = {};
                context.customModal.modalObj = modal;
                // The UI container is not emptied at the end of this test case
                context.isUIContainerEmptiedBeforeNextTest = false;
                // Result of the test case...
                if (modal === null) {
                    context.prevTestCaseMessage = 'Modal instantiation failed, value null returned.';
                    return false;
                }
                return context.areValuesEqual(
                        typeof modal, 'object',
                        modal.hasOwnProperty('element'), true,
                        modal.element instanceof jQuery, true
                );
            }
        }, {
            name: 'N - Opening modal dialog',
            description: 'Opens a modal dialog and captures the aftershow event once modal is displayed',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                context.toggleUIContainer(true);
                return await new Promise(function(resolve){
                    context.customModal.modalObj.element.one('aftershow', function(event, modalObj){
                        resolve(modalObj.element.is(':visible'));
                    });
                    // The modal is opened
                    context.customModal.modalObj.open();
                });
            }
        }, {
            name: 'N - Setting the title',
            description: 'Sets the title of the modal dialog',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                const title = 'Here is my custom title';
                context.customModal.modalObj.setTitle(title);
                return context.areValuesEqual(
                    context.customModal.modalObj.element.find('.title').text(), title
                );
            }
        }, {
            name: 'N - Closing the modal dialog (header button)',
            description: 'Closes a modal dialog by clicking the header close button',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                return await new Promise(function(resolve){
                    context.customModal.modalObj.element.one('afterhide', function(event, modalObj){
                        resolve(!modalObj.element.is(':visible'));
                    });
                    // Click on the header close button
                    context.customModal.modalObj.element.find('span.close').trigger('click');
                });
            }
        }, {
            name: 'N - Closing the modal dialog (footer button)',
            description: 'Closes a modal dialog by clicking the footer close button',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                const promiseOpen = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('aftershow', function(event, modalObj){
                        resolve(modalObj.element.is(':visible'));
                    });
                    // The modal is opened
                    context.customModal.modalObj.open();
                });
                const promiseClose = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('afterhide', function(event, modalObj){
                        resolve(!modalObj.element.is(':visible'));
                    });
                    // Click on the footer close button
                    context.customModal.modalObj.element.find('button.cancel').trigger('click');
                });
                return await Promise.all([promiseOpen, promiseClose]).then(function(allStatus){
                    return allStatus[0] === true && allStatus[1] === true;
                });
            }
        }, {
            name: 'N - Closing the modal dialog (programmatically)',
            description: 'Closes a modal dialog by calling the close method',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                const promiseOpen = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('aftershow', function(event, modalObj){
                        resolve(modalObj.element.is(':visible'));
                    });
                    // The modal is opened
                    context.customModal.modalObj.open();
                });
                const promiseClose = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('afterhide', function(event, modalObj){
                        resolve(!modalObj.element.is(':visible'));
                    });
                    // Call the close method
                    context.customModal.modalObj.close();
                });
                return await Promise.all([promiseOpen, promiseClose]).then(function(allStatus){
                    return allStatus[0] === true && allStatus[1] === true;
                });
            }
        }, {
            name: 'A - Aborting close modal action (beforehide event)',
            description: 'Aborts the modal closing by returning false in the beforehide event handler',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                const promiseOpen = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('aftershow', function(event, modalObj){
                        resolve(modalObj.element.is(':visible'));
                    });
                    // The modal is opened
                    context.customModal.modalObj.open();
                });
                const promiseClose = new Promise(function(resolve){
                    context.customModal.modalObj.element.one('beforehide', function(event, modalObj){
                        if (!modalObj.element.is(':visible')) {
                            context.prevTestCaseMessage = 'Modal is not visible (1)';
                            resolve(false);
                        }
                        context.pause(300).then(function(){
                            if (!modalObj.element.is(':visible')) {
                                context.prevTestCaseMessage = 'Modal is not visible (2)';
                                resolve(false);
                            } else {
                                modalObj.close();
                                resolve(true);
                            }
                        });
                        return false; // Abort modal closing
                    });
                    // Call the close method
                    context.customModal.modalObj.close();
                });
                return await Promise.all([promiseOpen, promiseClose]).then(function(allStatus){
                    return allStatus[0] === true && allStatus[1] === true;
                });
            }
        },{
            name: 'A - Aborting UI close modal action (onClose callback)',
            description: 'Aborts the modal closing by returning false in the beforehide event handler',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                return await new Promise(function(resolve){
                    context.customModal.modalObj.element.one('aftershow', function(event, modalObj){
                        if (!modalObj.element.is(':visible')) {
                            context.prevTestCaseMessage = 'Modal is expected to be visible (1)';
                            resolve(false);
                        } else {
                            // The modal is closed
                            context.customModal.modalObj.element.find('span.close').trigger('click');
                        }
                    });
                    // The modal is opened
                    context.customModal.modalObj.open(null, function(){
                        if (!context.customModal.modalObj.element.is(':visible')) {
                            context.prevTestCaseMessage = 'Modal is expected to be visible (2)';
                            resolve(false);
                        }
                        context.pause(300).then(function(){
                            if (!context.customModal.modalObj.element.is(':visible')) {
                                context.prevTestCaseMessage = 'Modal is expected to be visible (3)';
                                resolve(false);
                            } else {
                                context.customModal.modalObj.close();
                                resolve(true);
                            }
                        });
                        return false; // Abort modal closing
                    });
                });
            }
        }, {
            name: 'A - Aborting open modal action (beforeshow event)',
            description: 'Aborts the modal opening by returning false in the beforeshow event handler',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1;
                }
                return await new Promise(function(resolve){
                    context.customModal.modalObj.element.one('beforeshow', function(event, modalObj){
                        if (modalObj.element.is(':visible')) {
                            context.prevTestCaseMessage = 'Modal is not expected to be visible (1)';
                            resolve(false);
                        }
                        context.pause(300).then(function(){
                            if (modalObj.element.is(':visible')) {
                                context.prevTestCaseMessage = 'Modal is not expected to be visible (2)';
                                resolve(false);
                            } else {
                                resolve(true);
                            }
                        });
                        return false; // Abort modal opening
                    });
                    // The modal is opened
                    context.customModal.modalObj.open();
                });
            }
        }, {
            name: 'N - Getting access to the inner form',
            description: 'Gets access to the inner form and set values to the form inputs.',
            testFn: async function(context) {
                const formObj = context.customModal.modalObj.getInnerForm();
                formObj.setInputValue('label', 'Hello');
                formObj.setInputValue('value', '18');
                return context.areValuesEqual(
                    context.customModal.modalObj.element.find('input[name=label]').val(),
                    'Hello',
                    context.customModal.modalObj.element.find('input[name=value]').val(),
                    '18'
                );
            }
        }, {
            name: 'N - Set focus on a form input',
            description: 'Opens a modal and set the focus in the "value" input field',
            testFn: async function(context) {
                context.customModal.modalObj.open(null, null, 'value');
                await context.pause(100);
                const focusInputName = $(document.activeElement).attr('name');
                context.customModal.modalObj.close();
                return context.areValuesEqual(focusInputName, 'value');
            }
        }, {
            name: 'N - Handling form submit (callback function)',
            description: 'Checks if the callback function passed in parameter of the open() method is called on form submit and the modal is then closed automatically',
            testFn: async function(context) {
                return await new Promise(function(resolve){
                    context.customModal.modalObj.open(function(response){// Submit callback
                        // After modal is hidden
                        context.customModal.modalObj.element.one('afterhide', function(){
                            resolve(response.success === true);
                        });
                    });
                    // Click the submit button
                    context.customModal.modalObj.element.find('button[type=submit]')
                            .trigger('click');
                });
            }
        }, {
            name: 'N - Avoiding modal closing on submit (callback function)',
            description: 'Returns false to the submit callback function to avoid closing the modal',
            testFn: async function(context) {
                await context.pause(1000);// Submit button is disabled during 500 ms...
                return await new Promise(function(resolve){
                    // After modal is shown...
                    context.customModal.modalObj.element.one('aftershow', function (event, modalObj) {
                        // Click the submit button
                        modalObj.element.find('button[type=submit]').trigger('click');
                    });
                    // The modal is opened
                    context.customModal.modalObj.open(function(response){// Submit callback
                        // Wait 300 ms the end of the callback function
                        context.pause(300).then(function(){
                            const status = context.areValuesEqual(
                                // The modal is still visible
                                context.customModal.modalObj.element.is(':visible'), true,
                                // The response returned by the controller action is OK
                                response.success, true
                            );
                            // The modal is closed before returning the test case status
                            context.customModal.modalObj.close();
                            resolve(status);
                        });
                        return false;
                    });
                });
            }
        }, {
            name: 'A - Loading a modal from the web server',
            description: 'Loads the modal dialog from the remote web server and instantiates it',
            testFn: async function(context) {
                var modal, callbackThis;
                const promiseLoading = await new Promise(function(resolve){
                    modal = z4m.modal.make('#z4mtsts-modal-1', 'z4mtsts_modal1', function(){
                        callbackThis = this;
                        resolve();
                    });
                });
                const status = context.areValuesEqual(
                    $('#z4mtsts-modal-1').length, 1,
                    modal === null, true, // Unknown for the modal is loaded in Ajax
                    typeof callbackThis === 'object', true,
                    callbackThis !== null, true,
                    callbackThis.hasOwnProperty('element'), true,
                    callbackThis.element.attr('id'), 'z4mtsts-modal-1'
                );
                $('#z4mtsts-modal-1').remove();
                return status;
            }
        }
    ],
    serverSideCore:[ // TODO: delete (context.customServerSideCore);
        {
            name: 'N - Request methods',
            description: 'PHP Request class methods of ZnetDK',
            testFn: async function(context) {
                // Go back to the test view
                z4m.content.displayView('runtests');
                context.customServerSideCore = {};
                context.customServerSideCore.run = function(controller, action, parameters) {
                    const options = {
                        controller: controller,
                            action: action
                    };
                    if (typeof parameters === 'object' && parameters !== null) {
                        options.data = parameters;
                    }
                    return new Promise((resolve, reject) => {
                        options.callback = function(response) {
                            if (response.success === false || response.warning === true) {
                                context.prevTestCaseMessage = response.msg;
                            }
                            resolve(response.warning === true ? -1 : response.success);
                        };
                        options.errorCallback = function(response) {
                            console.log(response);
                            reject(new Error('Error ' + response.status + ' (see console)'));
                            return false;
                        };
                        z4m.ajax.request(options);
                    });
                };
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testrequest');
            }
        }, {
            name: 'N - Response methods',
            description: 'PHP Response class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testresponse');
            }
        }, {
            name: 'N - Controller\\Security methods',
            description: 'PHP controller\\Security class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testcontrollersecurity');
            }
        }, {
            name: 'N - Convert methods',
            description: 'PHP Convert class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testconvert');
            }
        }, {
            name: 'N - General methods',
            description: 'PHP General class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testgeneral');
            }
        }, {
            name: 'N - UserSession methods',
            description: 'PHP UserSession class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testusersession');
            }
        }, {
            name: 'N - DAO methods',
            description: 'PHP DAO class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testdao');
            }
        }, {
            name: 'N - SimpleDAO methods',
            description: 'PHP SimpleDAO class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testsimpledao', {
                        first: 2,
                        count: 5,
                        sortfield: 'profile_name',
                        sortorder: '-1'
                    });
            }
        },{
            name: 'N - Validator methods',
            description: 'PHP Validator class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testvalidator', {
                        param1: 'Value param 1',
                        param2: 'Value param 2',
                        param3: 'Value param 3'
                    });
            }
        }, {
            name: 'N - ProfileManager methods',
            description: 'PHP ProfileManager class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testprofilemanager');
            }
        }, {
            name: 'N - UserManager methods',
            description: 'PHP UserManager class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testusermanager');
            }
        }, {
            name: 'N - AppController methods',
            description: 'PHP AppController class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testappcontroller');
            }
        }, {
            name: 'N - controller\\Users methods',
            description: 'PHP controller\\Users class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testcontrollerusers');
            }
        }, {
            name: 'N - User methods',
            description: 'PHP User class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testuser');
            }
        }, {
            name: 'N - controller\\Profiles methods',
            description: 'PHP controller\\Profiles class methods of ZnetDK',
            testFn: async function(context) {
                return await context.customServerSideCore.run(
                    'z4mtsts_server_ctl', 'testcontrollerprofiles');
            }
        }
    ],
    z4mprofiles: [{
            name: 'N - List of profiles is empty',
            description: 'Display the user profiles view, no profile exists.',
            testFn: async function(context) {
                // Display of the 'z4mprofiles' view
                return await new Promise((resolve) => {
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-profile-list',
                            function(event, listObj, loadedRowCount, pageNbr){
                        if (loadedRowCount === 0) {
                            resolve(true); // No profile returned
                        } else {
                            console.log('Profiles exist so the case is not testable.');
                            resolve(-1);
                        }
                    });
                    z4m.content.displayView('z4mprofiles');
                });
            }
        },{
            name: 'N - Add a user profile',
            description: 'Add a user profile from the built-in form',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    return false; // Previous test case failed,
                }
                // Menu item labels memorized in context
                context.custom_z4mprofiles = {};
                context.custom_z4mprofiles.profileMenuLabel =
                    $('#zdk-tab-menu a[data-view_id=z4mprofiles] span:first-of-type').text();
                context.custom_z4mprofiles.userMenuLabel =
                    $('#zdk-tab-menu a[data-view_id=z4musers] span:first-of-type').text();
                // Test case begin
                return await new Promise((resolve) => {
                    $('#mzdk-profile-modal').one('aftershow', function () {
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-profile-list',
                                function(){
                            // After data list refresh
                            const rowEl = $(this).find('li');
                            if (rowEl.length !== 1) {
                                resolve(false);
                            }
                            const rowCells = rowEl.find('a.edit > div');
                            resolve(context.areValuesEqual(
                                    // First cell value: profile name
                                    rowCells.first().text().trim(), '4 Test',
                                    // Second cell value: description
                                    rowCells.eq(1).text().trim(), '4 Test description',
                                    // Third cell value: menu items
                                    rowCells.eq(2).find('.menu-items').text(),
                                        context.custom_z4mprofiles.userMenuLabel
                            ));
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            resolve(false);
                        }
                        form.init({
                            profile_name: '4 Test',
                            profile_description: '4 Test description',
                            'menu_ids[]': ['z4musers']
                        });
                        // Click Submit button
                        form.element.find('button[type=submit]').trigger('click');
                    });
                    // Click Add action button
                    $('#zdk-mobile-action-add').trigger('click');
                });
            }
        },
        {
            name: 'Update User profile',
            description: 'Update a user profile from the buit-in form',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return false;
                }
                await context.pause(2000);
                return await new Promise((resolve) => {
                    $('#mzdk-profile-modal').one('aftershow', function () {
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-profile-list',
                                function(){
                            // After data list refresh
                            const rowEl = $(this).find('li');
                            if (rowEl.length !== 1) {
                                resolve(context.areValuesEqual(rowEl.length, 1));
                            }
                            const rowCells = rowEl.find('a.edit > div');
                            resolve(context.areValuesEqual(
                                    // First cell value: profile name
                                    rowCells.first().text().trim(), '4 Test',
                                    // Second cell value: description
                                    rowCells.eq(1).text().trim(), '4 Test description',
                                    // Third cell value: menu items
                                    rowCells.eq(2).find('.menu-items').text(),
                                        context.custom_z4mprofiles.profileMenuLabel
                                        + ', '
                                        + context.custom_z4mprofiles.userMenuLabel
                            ));
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            resolve(context.areValuesEqual(form !== null, false));
                        }
                        form.setInputValue('menu_ids[]', ['z4musers', 'z4mprofiles']);
                        //Click Submit button
                        form.element.find('button[type=submit]').trigger('click');
                    });
                    // Click the first row for editing
                    $('#mzdk-profile-list li:first-of-type a.edit').trigger('click');
                });
            }
        }, {
            name: 'A - Remove User profile',
            description: 'Remove the user profile added previously.',
            testFn: async function(context) {
                // Custom context removed
                delete(context.custom_z4mprofiles);
                // Test success of previous test case
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return false; // Previous test case failed,
                }
                // Wait 1 second...
                await context.pause(1000);
                return await new Promise((resolve) => {
                    // 2) Profile modal dialog is displayed
                    $('#mzdk-profile-modal').one('aftershow', function () {
                        // 6) Data list refreshed
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-profile-list',
                                function(){
                            // After data list refresh
                            const rowEl = $(this).find('li');
                            resolve(context.areValuesEqual(
                                rowEl.length, 1,
                                rowEl.text().trim(), context.noRowMsg[context.lang]
                            ));
                        });
                        // 3) Click Remove button
                        $(this).find('button.remove').trigger('click');
                        // 4) Waiting for confirmation modal dialog display
                        context.pause(2000).then(function(){
                            // 5) Click yes button for confirmation
                            $('#zdk-confirmation-modal').find('button.yes').trigger('click');
                        });
                    });
                    // 1) Click the first row for editing
                    $('#mzdk-profile-list li:first-of-type a.edit').trigger('click');
                });
            }
        }
    ],
    z4musers:[{
            name: 'N - List of users is empty',
            description: 'Display the user view, no user exists.',
            testFn: async function(context) {
                context.custom_z4musers = {};
                context.custom_z4musers.userInfos = {
                    user_name: 'John DOE',
                    user_email: 'john.doe@fakemail.zzz',
                    user_phone: '+61812436598',
                    notes: 'This a user for testing',
                    login_name: 'john_doe_fakemail',
                    login_password: 'Fake_password123',
                    login_password2: 'Fake_password123' /* ,
                    expiration_date: '',
                    user_enabled: ''*/,
                    full_menu_access: '1'/*,
                    'profiles[]': '' */
                };
                context.custom_z4musers.expirationISODate = null;
                context.custom_z4musers.existingProfiles = null;
                context.custom_z4musers.labelEnabled =  {
                    fr: 'Activé',
                    en: 'Enabled',
                    es: 'Activado'
                };
                context.custom_z4musers.labelFullMenuAccess = {
                    fr: 'Complet',
                    en: 'Full',
                    es: 'Completo'
                };
                context.custom_z4musers.getIsoDate = function(localeDate) {
                    const dateAsArray = localeDate.split('/');
                    if (dateAsArray.length !== 3) {
                        return localeDate;
                    } else if (context.lang === 'en') { // m/d/yy
                        return '20' + dateAsArray[2] + '-'
                            + dateAsArray[0].padStart(2, '0') + '-'
                            + dateAsArray[1].padStart(2, '0');
                    } else if (context.lang === 'es') { // d/m/yy
                        return '20' + dateAsArray[2] + '-'
                            + dateAsArray[1].padStart(2, '0') + '-'
                            + dateAsArray[0].padStart(2, '0');
                    } else if (context.lang === 'fr') { // dd/mm/yyyy
                        return dateAsArray[2] + '-' + dateAsArray[1] + '-'
                            + dateAsArray[0];
                    } else {
                        return localeDate;
                    }
                };
                return await new Promise((resolve) => {
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                            function(event, listObj, loadedRowCount, pageNbr){
                        if (loadedRowCount === 0) {
                            resolve(true); // No user returned
                        } else {
                            context.prevTestCaseMessage = 'Users exist so the case is not testable.';
                            resolve(-1);
                        }
                    });
                    z4m.content.displayView('z4musers');
                });
            }
        },{
            name: 'N - Check existing user profiles',
            description: 'Check if the list of user profiles is correctly loaded in the built-in form',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                // Add two profiles required for this test case and the following
                await new Promise((resolve) => {
                    z4m.ajax.request({
                        controller: 'z4mtsts_testdata_ctl',
                        action: 'add2profiles',
                        callback: resolve
                    });
                });
                // Display the user form
                return await new Promise((resolve) => {
                    $('#mzdk-user-modal').one('aftershow', function (event, modalObj) {
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                            resolve(false);
                        }
                        context.custom_z4musers.existingProfiles = form.element.find('select[name="profiles[]"] option');
                        if (context.custom_z4musers.existingProfiles.length !== 2) {
                            context.prevTestCaseMessage = '2 user profiles are expected for testing the case.';
                            modalObj.close();
                            resolve(false);
                        } else {
                            resolve(true);
                        }
                    });
                    // Click Add action button
                    $('#zdk-mobile-action-add').trigger('click');
                });
            }
        },{
            name: 'N - Add a user ',
            description: 'Add a user from the built-in form',
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                // Add new user
                return await new Promise((resolve) => {
                    const userInfos = context.custom_z4musers.userInfos,
                        labelFullMenuAccess = context.custom_z4musers.labelFullMenuAccess,
                        getIsoDate = context.custom_z4musers.getIsoDate;
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                            function(){
                        // After data list refresh
                        const rowEl = $(this).find('li');
                        if (rowEl.length !== 1) {
                            context.prevTestCaseMessage = 'Number of rows is not equal to 1.';
                            resolve(false);
                        }
                        const rowCells = rowEl.find('.w3-col');
                        resolve(context.areValuesEqual(
                            rowCells.first().find('strong').text().trim(), userInfos.user_name,
                            rowCells.first().find('span').first().text().trim(), userInfos.login_name,
                            rowCells.first().find('span').eq(1).text().trim(), userInfos.notes,
                            rowCells.eq(1).find('a').first().text().trim(), userInfos.user_email,
                            rowCells.eq(1).find('a').eq(1).text().trim(), userInfos.user_phone,
                            rowCells.eq(2).find('.w3-tag').hasClass('user-enabled-1'), true,
                            getIsoDate(rowCells.eq(2).find('.expiration-date span').text()),
                                context.custom_z4musers.expirationISODate,
                            rowCells.eq(3).find('span').first().text(), labelFullMenuAccess[context.lang],
                            rowCells.eq(3).find('span.user-profiles').text(), 'Z4M Test Profile #2'
                        ));
                    });
                    const form = z4m.form.make($('#mzdk-user-modal').find('form'));
                    if (form === null) {
                        context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                        resolve(false);
                    }
                    // Default expiration date is memorized
                    context.custom_z4musers.expirationISODate = form.getInputValue('expiration_date');
                    // Form is initialized from the user infos stored in context.
                    form.init(userInfos, false /* form not reset */);
                    form.setInputValue('profiles[]', [context.custom_z4musers.existingProfiles.eq(1).val()]);
                    // Click Submit button
                    form.element.find('button[type=submit]').trigger('click');
                });
            }
        }, {
            name: "N - Update user's profiles",
            description: "Update the user's profiles from the built-in form",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                // Edit user
                return await new Promise((resolve) => {
                    $('#mzdk-user-modal').one('aftershow', function () {
                        const userInfos = context.custom_z4musers.userInfos,
                            expirationISODate = context.custom_z4musers.expirationISODate,
                            existingProfiles = context.custom_z4musers.existingProfiles,
                            labelFullMenuAccess = context.custom_z4musers.labelFullMenuAccess,
                            getIsoDate = context.custom_z4musers.getIsoDate;
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                                function(){
                            // After data list refresh
                            const rowEl = $(this).find('li');
                            if (rowEl.length !== 1) {
                                context.prevTestCaseMessage = 'Number of rows is not equal to 1.';
                                resolve(false);
                            }
                            const rowCells = rowEl.find('.w3-col');
                            context.pause(300).then(function(){
                                resolve(context.areValuesEqual(
                                    rowCells.first().find('strong').text().trim(), userInfos.user_name,
                                    rowCells.first().find('span').first().text().trim(), userInfos.login_name,
                                    rowCells.first().find('span').eq(1).text().trim(), userInfos.notes,
                                    rowCells.eq(1).find('a').first().text().trim(), userInfos.user_email,
                                    rowCells.eq(1).find('a').eq(1).text().trim(), userInfos.user_phone,
                                    rowCells.eq(2).find('.w3-tag').hasClass('user-enabled-1'), true,
                                    getIsoDate(rowCells.eq(2).find('.expiration-date span').text()), expirationISODate,
                                    rowCells.eq(3).find('span').first().text(), labelFullMenuAccess[context.lang],
                                    rowCells.eq(3).find('span.user-profiles').text(), 'Z4M Test Profile #1, Z4M Test Profile #2'
                                ));
                            });
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                            resolve(false);
                        }
                        form.setInputValue('profiles[]', [
                            existingProfiles.eq(0).val(),
                            existingProfiles.eq(1).val()
                        ]);
                        context.pause(300).then(function(){
                            // Click Submit button
                            form.element.find('button[type=submit]').trigger('click');
                        });
                    });
                    // Click the first row for editing
                    $('#mzdk-user-list li:first-of-type a.edit').trigger('click');
                });
            }
        }, {
            name: "N - Update user's status to Archived",
            description: "Update the user's status to Archived from the built-in form",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                await context.pause(500); // Waiting for 500 ms because multiple clicks are prevented
                // Edit user
                return await new Promise((resolve) => {
                    $('#mzdk-user-modal').one('aftershow', function () {
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                                function(event, listObj, loadedRowCount){
                            if (loadedRowCount === 0) {
                                resolve(true); // No user returned
                            } else {
                                context.prevTestCaseMessage = 'Users exist so the case failed.';
                                resolve(false);
                            }
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                            resolve(false);
                        }
                        // Status changed to Archived
                        form.setInputValue('user_enabled', '-1');
                        // Click Submit button
                        form.element.find('button[type=submit]').trigger('click');
                    });
                    // Click the first row for editing
                    $('#mzdk-user-list li:first-of-type a.edit').trigger('click');
                });
            }
        }, {
            name: "N - Display of archived users",
            description: "Display of the archived users by clicking the Status filter.",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                return await new Promise((resolve) => {
                    const userInfos = context.custom_z4musers.userInfos,
                        expirationISODate = context.custom_z4musers.expirationISODate,
                        existingProfiles = context.custom_z4musers.existingProfiles,
                        labelFullMenuAccess = context.custom_z4musers.labelFullMenuAccess,
                        getIsoDate = context.custom_z4musers.getIsoDate;
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                            function(){
                        // After data list refresh
                        const rowEl = $(this).find('li');
                        if (rowEl.length !== 1) {
                            context.prevTestCaseMessage = 'Number of rows is not equal to 1.';
                            resolve(false);
                        }
                        const rowCells = rowEl.find('.w3-col');
                        resolve(context.areValuesEqual(
                            rowCells.first().find('strong').text().trim(), userInfos.user_name,
                            rowCells.first().find('span').first().text().trim(), userInfos.login_name,
                            rowCells.first().find('span').eq(1).text().trim(), userInfos.notes,
                            rowCells.eq(1).find('a').first().text().trim(), userInfos.user_email,
                            rowCells.eq(1).find('a').eq(1).text().trim(), userInfos.user_phone,
                            rowCells.eq(2).find('.w3-tag').hasClass('user-enabled--1'), true,
                            getIsoDate(rowCells.eq(2).find('.expiration-date span').text()), expirationISODate,
                            rowCells.eq(3).find('span').first().text(), labelFullMenuAccess[context.lang],
                            rowCells.eq(3).find('span.user-profiles').text(), 'Z4M Test Profile #1, Z4M Test Profile #2'
                        ));
                    });
                    // Click the Archived status filter
                    $('#mzdk-user-list-filter-status-archived').trigger('click');
                });
            }
        }, {
            name: "N - Update user's status to Disabled",
            description: "Update the user's status to Disabled from the built-in form",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                await context.pause(500); // Waiting for 500 ms because multiple clicks are prevented
                // Edit user
                return await new Promise((resolve) => {
                    $('#mzdk-user-modal').one('aftershow', function () {
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                                function(event, listObj, loadedRowCount){
                            if (loadedRowCount === 0) {
                                resolve(true); // No user returned
                            } else {
                                context.prevTestCaseMessage = 'Users exist so the case failed.';
                                resolve(false);
                            }
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                            resolve(false);
                        }
                        // Status changed to Archived
                        form.setInputValue('user_enabled', '0');
                        // Click Submit button
                        form.element.find('button[type=submit]').trigger('click');
                    });
                    // Click the first row for editing
                    $('#mzdk-user-list li:first-of-type a.edit').trigger('click');
                });
            }
        }, {
            name: "N - Display of disabled users",
            description: "Display of the disabled users by clicking the Status filter.",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                return await new Promise((resolve) => {
                    const userInfos = context.custom_z4musers.userInfos,
                        expirationISODate = context.custom_z4musers.expirationISODate,
                        existingProfiles = context.custom_z4musers.existingProfiles,
                        labelFullMenuAccess = context.custom_z4musers.labelFullMenuAccess,
                        getIsoDate = context.custom_z4musers.getIsoDate;
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                            function(){
                        // After data list refresh
                        const rowEl = $(this).find('li');
                        if (rowEl.length !== 1) {
                            context.prevTestCaseMessage = 'Number of rows is not equal to 1.';
                            resolve(false);
                        }
                        const rowCells = rowEl.find('.w3-col');
                        resolve(context.areValuesEqual(
                            rowCells.first().find('strong').text().trim(), userInfos.user_name,
                            rowCells.first().find('span').first().text().trim(), userInfos.login_name,
                            rowCells.first().find('span').eq(1).text().trim(), userInfos.notes,
                            rowCells.eq(1).find('a').first().text().trim(), userInfos.user_email,
                            rowCells.eq(1).find('a').eq(1).text().trim(), userInfos.user_phone,
                            rowCells.eq(2).find('.w3-tag').hasClass('user-enabled-0'), true,
                            getIsoDate(rowCells.eq(2).find('.expiration-date span').text()), expirationISODate,
                            rowCells.eq(3).find('span').first().text(), labelFullMenuAccess[context.lang],
                            rowCells.eq(3).find('span.user-profiles').text(), 'Z4M Test Profile #1, Z4M Test Profile #2'
                        ));
                    });
                    // Click the Archived status filter
                    $('#mzdk-user-list-filter-status-disabled').trigger('click');
                });
            }
        }, {
            name: "N - Update user's status to Enabled",
            description: "Update the user's status to Enabled from the built-in form",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                await context.pause(500); // Waiting for 500 ms because multiple clicks are prevented
                // Edit user
                return await new Promise((resolve) => {
                    $('#mzdk-user-modal').one('aftershow', function () {
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                                function(event, listObj, loadedRowCount){
                            if (loadedRowCount === 0) {
                                resolve(true); // No user returned
                            } else {
                                context.prevTestCaseMessage = 'Users exist so the case failed.';
                                resolve(false);
                            }
                        });
                        const form = z4m.form.make($(this).find('form'));
                        if (form === null) {
                            context.prevTestCaseMessage = 'Unable to instantiate the user form.';
                            resolve(false);
                        }
                        // Status changed to Archived
                        form.setInputValue('user_enabled', '1');
                        // Click Submit button
                        form.element.find('button[type=submit]').trigger('click');
                    });
                    // Click the first row for editing
                    $('#mzdk-user-list li:first-of-type a.edit').trigger('click');
                });
            }
        }, {
            name: "N - Display of enabled users",
            description: "Display of the enabled users by clicking the Status filter.",
            testFn: async function(context) {
                if (context.prevTestCaseState !== true) {
                    context.prevTestCaseMessage = 'Previous test case failed.';
                    return -1;
                }
                return await new Promise((resolve) => {
                    const userInfos = context.custom_z4musers.userInfos,
                        expirationISODate = context.custom_z4musers.expirationISODate,
                        existingProfiles = context.custom_z4musers.existingProfiles,
                        labelFullMenuAccess = context.custom_z4musers.labelFullMenuAccess,
                        getIsoDate = context.custom_z4musers.getIsoDate;
                    $('body').off('afterpageloaded.z4mtsts');
                    $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                            function(){
                        // After data list refresh
                        const rowEl = $(this).find('li');
                        if (rowEl.length !== 1) {
                            context.prevTestCaseMessage = 'Number of rows is not equal to 1.';
                            resolve(false);
                        }
                        const rowCells = rowEl.find('.w3-col');
                        resolve(context.areValuesEqual(
                            rowCells.first().find('strong').text().trim(), userInfos.user_name,
                            rowCells.first().find('span').first().text().trim(), userInfos.login_name,
                            rowCells.first().find('span').eq(1).text().trim(), userInfos.notes,
                            rowCells.eq(1).find('a').first().text().trim(), userInfos.user_email,
                            rowCells.eq(1).find('a').eq(1).text().trim(), userInfos.user_phone,
                            rowCells.eq(2).find('.w3-tag').hasClass('user-enabled-1'), true,
                            getIsoDate(rowCells.eq(2).find('.expiration-date span').text()), expirationISODate,
                            rowCells.eq(3).find('span').first().text(), labelFullMenuAccess[context.lang],
                            rowCells.eq(3).find('span.user-profiles').text(), 'Z4M Test Profile #1, Z4M Test Profile #2'
                        ));
                    });
                    // Click the Archived status filter
                    $('#mzdk-user-list-filter-status-enabled').trigger('click');
                });
            }
        }, {
            name: 'E - Add User, email already exists',
            description: 'Error displayed on adding user if email already exists',
            testFn: async function(context) {
                context.prevTestCaseMessage = 'TODO';
                return -1;
            }
        }, {
            name: 'E - Add User, login already exists',
            description: 'Error displayed on adding user if login already exists',
            testFn: async function(context) {
                context.prevTestCaseMessage = 'TODO';
                return -1;
            }
        }, {
            name: 'E - Add User, password and confirmation mismatch',
            description: 'Error displayed on adding user if password and confirmation are not the same',
            testFn: async function(context) {
                context.prevTestCaseMessage = 'TODO';
                return -1;
            }
        }, {
            name: 'E - Add User, password not enough strong',
            description: 'Error displayed on adding user if the expected characters in the password are missing',
            testFn: async function(context) {
                context.prevTestCaseMessage = 'TODO';
                return -1;
            }
        }, {
            name: 'A - Remove User',
            description: 'Remove the user added previously.',
            testFn: async function(context) {

                if (context.prevTestCaseState === false) {
                    context.prevTestCaseMessage = 'Not run for previous test case failed';
                    return -1; // Previous test case failed,
                }
                await context.pause(300);
                return await new Promise((resolve) => {
                    // 2) User modal dialog is displayed
                    $('#mzdk-user-modal').one('aftershow', function () {
                        // 6) Data list refreshed
                        $('body').off('afterpageloaded.z4mtsts');
                        $('body').one('afterpageloaded.z4mtsts', '#mzdk-user-list',
                                function(){
                            // After data list refresh
                            const rowEl = $(this).find('li');
                            // Remove the two profiles added for the previous test cases
                            z4m.ajax.request({
                                controller: 'z4mtsts_testdata_ctl',
                                action: 'remove2profiles',
                                callback: function() {
                                    // End of the test case
                                    resolve(context.areValuesEqual(
                                        rowEl.length, 1,
                                        rowEl.text().trim(), context.noRowMsg[context.lang]
                                    ));
                                }
                            });
                        });
                        // 3) Click Remove button
                        $(this).find('button.remove').trigger('click');
                        // 4) Waiting for confirmation modal dialog display
                        context.pause(300).then(function(){
                            // 5) Click yes button for confirmation
                            $('#zdk-confirmation-modal').find('button.yes').trigger('click');
                        });
                    });
                    // 1) Click the first row for editing
                    $('#mzdk-user-list li:first-of-type a.edit').trigger('click');
                });
            }
        }
    ]
};
