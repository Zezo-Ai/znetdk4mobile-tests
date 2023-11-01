<h3>Run tests of a specific domain (from console)</h3>
<ol>
    <li>Open the browser console,</li>
    <li>Set the UI container required for tests by entering the command: <code class="w3-codespan">z4mTestRunner.setUIContainerId('z4mtsts-ui-container');</code></li>
    <li>Enter the command: <code class="w3-codespan">z4mTestRunner.run('domain');</code><br>
        <i class="w3-small">Expected domains are: 'action' 'ajax', 'autocomplete', 'form', 'list', 'modal', 'serverSideCore', 'z4musers' and 'z4mprofiles'.</i>
    </li>
    <li>See failed test cases via the command: <code class="w3-codespan">z4mTestRunner.getFailed();</code></li>
    <li>See test cases in error via the command: <code class="w3-codespan">z4mTestRunner.getErrors();</code></li>
</ol>
<h3>Run only one test (from console)</h3>
<p>When a test case be run independently...</p>
<ol>
    <li>Open the browser console,</li>
    <li>Set the UI container required for tests by entering the command: <code class="w3-codespan">z4mTestRunner.setUIContainerId('z4mtsts-ui-container');</code></li>
    <li>Enter the command: <code class="w3-codespan">z4mTestRunner.run('domain', 9);</code><br>
        <i class="w3-small">'domain' is to replace by 'action' 'ajax', 'autocomplete', 'form', 'list', 'modal', 'serverSideCore', 'z4musers' and 'z4mprofiles'.<br>
        9 is to replace by the 0 based index of the test case in the domain.</i>
    </li>
    <li>See failed test case via the command: <code class="w3-codespan">z4mTestRunner.getFailed();</code></li>
    <li>See test case in error via the command: <code class="w3-codespan">z4mTestRunner.getErrors();</code></li>
</ol>