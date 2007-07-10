<?php
define('PATH_TO_FILES','../temp/');
$InputTextName="The_Testing_Text.txt";
$PathToInputText =PATH_TO_FILES.$InputTextName;

define('INPUT_FILE',"../temp/The_Testing_Text.txt");
define('OUTPUT_TEXT',"The_Output_Text.txt");

if (! defined('SIMPLE_TEST')) {
	define('SIMPLE_TEST', '../simpletest/');
    }
    require_once(SIMPLE_TEST . 'unit_tester.php');
    require_once(SIMPLE_TEST . 'reporter.php');
    include('../classes/PhpCodeGenerator.php');
    
    class TestOfExistingFile extends UnitTestCase {
        function TestOfImputFileExist() {
		$this->assertTrue(file_exists("../temp/The_Testing_Text.txt"), 'File exist');
        }
	
	function TestOfImputFileHaveData() {
		$ImputFileSize=filesize(INPUT_FILE); 
		$this->assertNotEqual($ImputFileSize,0,'No data in input file');
	}
	
	function TestOfOutputFileExist() {
		@unlink("../temp/1_The_Output_Text.txt");
		Write_Output_File("1","The_Output_Text.txt"," ");
		$OutputFileSize=filesize("../temp/1_The_Output_Text.txt"); 
		$this->assertNotEqual($OutputFileSize,0,'No data in output file');
		@unlink("../temp/1_The_Output_Text.txt");
		}
	function TestTakeAllTheTextInAFile() {
		$FileContent= TakeAllDatasInAFile(INPUT_FILE);
		$this->assertEqual($FileContent,"Hello World I'm Luc
I live In Paris, Je suis francais j'écris donc avec des accents and some spécials chars like % ^$ ' '' ");
	}
	
	function TestTakeInputAndWriteInOutput() {
		@unlink("../temp/1_The_Output_Text.txt");
		$InputFileContent= TakeAllDatasInAFile(INPUT_FILE);
		Write_Output_File("1","The_Output_Text.txt",TakeAllDatasInAFile(INPUT_FILE));
		$OutputFileContent= TakeAllDatasInAFile("../temp/1_The_Output_Text.txt");
		$this->assertEqual($InputFileContent,$OutputFileContent);
		@unlink("../temp/1_The_Output_Text.txt");
	}
	
	function TestCountNumberOfCharsInAText(){ # Without balises
		$InputFileContent = TakeAllDatasInAFile(INPUT_FILE);
		$NumberOfChar = CountNumberOfCharsInAText($InputFileContent);
		$this->assertEqual($NumberOfChar,"124");
	}
	
	function TestLocateAPartOfInputTextOnTheFirstLine(){
		$SelectedPartOfTheString= TakeOnePartOfAString(TakeAllDatasInAFile(INPUT_FILE),3,8);
		$this->assertEqual($SelectedPartOfTheString,"lo World");

		$SelectedOtherOfTheString= TakeOnePartOfAString(TakeAllDatasInAFile(INPUT_FILE),0,36);
		$this->assertEqual($SelectedOtherOfTheString,"Hello World I'm Luc
I live In Paris");
	}

	function TestGenerationOfAnJsUnitTest(){ 
		$test = GenerateAJsUnitTest("1","0","11","Bold","Hello World","<strong>Hello World</strong>");
		$this->assertEqual($test,"function 1_TestScenariosGenerate(){ 
		SetContentToEditor('Hello World'); 
		SetFocus(22,3); 
		ExecuteCommand(Bold); 
		this->assertEqual('<strong>Hello World</strong>',GetContentFromEditor()) ;
	}
	");
	}
	
	function TestConvertAnActionInATag(){
		$this->assertEqual('strong',ConvertAnActionInATag('Bold'));
		$this->assertEqual('em',ConvertAnActionInATag('Italic'));

	}
	
	function TestGenerateResult(){
		$this->assertEqual('strong',ConvertAnActionInATag('Bold'));
		$this->assertEqual('em',ConvertAnActionInATag('Italic'));

	}
	
	
	
}
    $test = &new TestOfExistingFile();
    $test->run(new HtmlReporter());
    print CountNumberOfCharsInAText('Hello World');
?>

	
