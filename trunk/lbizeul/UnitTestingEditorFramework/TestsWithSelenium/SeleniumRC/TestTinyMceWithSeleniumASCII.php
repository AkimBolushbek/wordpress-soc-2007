<?php

require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Example extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->verificationErrors = array();
        $this->selenium = new Testing_Selenium("*firefox C:\Program Files\firefox1.5\firefox.exe", "http://lbizeul.free.fr/gsoc/TinyMceInstance.html");
        $result = $this->selenium->start();
    }

    function tearDown()
    {
        $this->selenium->stop();
    }

    function testMyTestCase()
    {
    $this->selenium->open("http://lbizeul.free.fr/gsoc/TinyMceInstance.html");
    $this->selenium->waitForCondition("selenium.browserbot.getCurrentWindow().tinyMCE.setContent(\"Hello World\"); true", "100");
    $this->selenium->waitForCondition("selenium.browserbot.getCurrentWindow().tinyMCE.execCommand('mceFocus', false, 'TinyMceInstance');true", "100");
    $this->selenium->shiftKeyDown();
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->keyPress("dom=document.getElementById('mce_editor_0').contentDocument.body", "\\20");
    $this->selenium->waitForCondition("selenium.browserbot.getCurrentWindow().tinyMCE.execCommand('Bold');true", "100");

    }
}
?>