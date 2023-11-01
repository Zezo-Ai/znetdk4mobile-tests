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
 * ZnetDK Javascript library for mobile page layout
 *
 * File version: 1.0
 * Last update: 03/24/2023
 */
/* global Promise, z4mTestCases */
z4mTestRunner = (function() {
    const _userCancelCheckDelay = 500;
    var _runState = {
        success: 0,
        failed: 0,
        notTested: 0,
        error: 0,
        run: 0,
        total: 0,
        progress: 0,
        lastTest: null
    }, _failed = [], _errors = [], _notTested = [], _progressCallback = null,
        _testCases = null, _UIContainerId, _stopRequest = false,
        _testCaseExecTimeout = 10000;

    function _reset() {
        _stopRequest = false;
        _runState.success = 0;
        _runState.failed = 0;
        _runState.notTested = 0;
        _runState.error = 0;
        _runState.run = 0;
        _runState.total = 0;
        _runState.progress = 0;
        _errors = [];
        _failed = [];
        _notTested = [];
    }
    function _clearUIContainer() {
        if (typeof _UIContainerId === 'string') {
            $('#' + _UIContainerId).empty();
        }
    }
    function _toggleUIContainer(isDisplayed) {
        if (isDisplayed === false) {
            $('#' + _UIContainerId).addClass('w3-hide');
        } else {
            $('#' + _UIContainerId).removeClass('w3-hide');
        }
    }
    function _areValuesEqual(...values) {
        if (values.length%2 !== 0) {
            throw new Error('The number of values to compare must be a multiple of two.');
        }
        var valuesNotEqual = [];
        values.forEach(function(value, index){
            if (index > 0 && index%2 !== 0 && value !== values[index-1]) {
                let obtainedValue = typeof values[index-1] === 'string'
                    ? "'" + values[index-1] + "'"
                    : (values[index-1]).toString();
                let expectedValue = typeof value === 'string'
                    ? "'" + value + "'" : value.toString();
                valuesNotEqual.push(obtainedValue + ' !== ' + expectedValue);
            }
        });
        return {
            status: valuesNotEqual.length === 0,
            failed: valuesNotEqual.length > 0
                ? ' [' + valuesNotEqual.join(', ') + ']' : ''
        };
    }
    function _pause(delayInMs) {
        return new Promise((resolve) => setTimeout(resolve, delayInMs));
    }
    function _onlyOneTestToRun(testCaseDomain, testCaseIndex) {
        return typeof testCaseDomain === 'string'
                && Number.isInteger(testCaseIndex);
    }
    function _onlyOneDomainToRun(testCaseDomain) {
        return typeof testCaseDomain === 'string';
    }
    function _setTestCases(testCases) {
            if (typeof testCases === 'object' && testCases !== null) {
                _testCases = testCases;
            } else {
                throw new Error('The specified variable for initializing test cases is not an object.');
            }
    }
    function _getTests(testCaseDomain, testCaseIndex) {
        if (_testCases === null) {
            _setTestCases(z4mTestCases);
        }
        var tests = [];
        for (const domain in _testCases) {
            _testCases[domain].forEach(function(testCase, index){
                if (!_onlyOneTestToRun(testCaseDomain, testCaseIndex)
                        && !_onlyOneDomainToRun(testCaseDomain) ||
                        (testCaseDomain === domain && (testCaseIndex === index
                        || typeof testCaseIndex === 'undefined'))) {
                    testCase.index = index;
                    testCase.domain = domain;
                    tests.push(testCase);
                }
            });
        }
        return tests;
    }
    function _getProgressPercent() {
        return Math.floor(_runState.run/_runState.total*100);
    }
    function _returnProgress() {
        if (typeof _progressCallback === 'function') {
            const progressPercent = _getProgressPercent();
            if (progressPercent > _runState.progress) {
                _runState.progress = progressPercent;
                _progressCallback(_runState);
            }
        }
    }
    function _addFailed(failedCase) {
        _failed.push(failedCase);
    }
    function _addError(error) {
        _errors.push(error);
    }
    function _addNotTested(testCase) {
        _notTested.push(testCase);
    }
    return {
        setTestCases: _setTestCases,
        setProgressCallback: function(callbackFunction) {
            if (typeof callbackFunction === 'function') {
                _progressCallback = callbackFunction;
            } else {
                throw new Error('The parameter value is not a function.');
            }
        },
        setUIContainerId: function(containerId) {
            if (typeof containerId === 'string'
                    && $('#' + containerId).length === 1) {
                _UIContainerId = containerId;
            } else {
                throw new Error('The parameter value is not a valid container ID.');
            }
        },
        setTestCaseExecutionTimeout(delayInMs) {
            _testCaseExecTimeout = delayInMs;
        },
        run: async function(testCaseDomain, testCaseIndex) {
            _reset();
            const tests = _getTests(testCaseDomain, testCaseIndex);
            if (tests.length === 0) {
                console.error('No test case found!', testCaseDomain, testCaseIndex);
            }
            var context = {
                UIContainerId: _UIContainerId,
                lang: $('html').attr('lang'),
                isUIContainerEmptiedBeforeNextTest: true,
                prevTestCaseState: false,
                prevTestCaseMessage: null,
                noRowMsg:  {
                    fr: 'Aucun résultat trouvé.',
                    en: 'No results found.',
                    es: 'No se han encontrado resultados.'
                },
                toggleUIContainer: _toggleUIContainer,
                pause: _pause,
                areValuesEqual: _areValuesEqual
            };
            _toggleUIContainer(false);
            _runState.total = tests.length;
            const userCancel = new Promise((resolve) => {
                function timeout() {
                    if (_stopRequest) {
                        resolve('cancel');
                    } else if (_getProgressPercent() === 100) {
                        resolve(1);
                    } else {
                        setTimeout(timeout, _userCancelCheckDelay);
                    }
                }
                setTimeout(timeout, _userCancelCheckDelay);
            });
            for (const test of tests) {
                if (context.isUIContainerEmptiedBeforeNextTest === true) {
                    _clearUIContainer();
                }
                let isOk = false, failedMessage = null;
                const timeout = new Promise((resolve) => setTimeout(resolve,
                        _testCaseExecTimeout, 'timeout'));
                try {
                    isOk = await Promise.race([timeout, userCancel, test.testFn(context)]);
                } catch (error) {
                    isOk = 'error';
                    console.log('Catched error: ', error);
                }
                _runState.lastTest = test;
                if (typeof isOk === 'object' && isOk !== null
                        && typeof isOk.status === 'boolean') {
                    failedMessage = isOk.failed;
                    isOk = isOk.status;
                }
                if (isOk === -1) { // Not tested
                    test.error = context.prevTestCaseMessage === null
                        ? 'Not tested.' : context.prevTestCaseMessage;
                    _addNotTested(test);
                    _runState.notTested++;
                } else if (isOk === true) { // Success
                    _runState.success++;
                } else if (isOk === false) { // Failed
                    test.error = context.prevTestCaseMessage === null
                        ? (failedMessage === null ? 'Assertion failed.' : failedMessage)
                        : context.prevTestCaseMessage;
                    _addFailed(test);
                    _runState.failed++;
                } else if (isOk === 'cancel') { // Cancelling by user
                    _addError('Tests canceled by user.');
                    break;
                } else if (isOk === 'timeout') { // Timeout
                    test.error = 'Execution time exceeded.';
                    _addError(test);
                    _runState.error++;
                } else if (isOk === 'error') { // JS Error
                    test.error = 'Unexpected JS error, see console for details.';
                    _addError(test);
                    _runState.error++;
                } else {
                    test.error = "Unexpected status '" + isOk + "' returned by the test case.";
                    _addError(test);
                    _runState.error++;
                }
                context.prevTestCaseState = isOk;
                context.prevTestCaseMessage = null;
                _runState.run++;
                _returnProgress();
            };
            if (!_onlyOneTestToRun(testCaseDomain, testCaseIndex)) {
                _clearUIContainer();
            }
            return _runState;
        },
        stop: function() {
            _stopRequest = true;
        },
        getFailed: function() {
            return _failed;
        },
        getErrors: function() {
            return _errors;
        },
        getNotTested: function() {
            return _notTested;
        }
    };
})();