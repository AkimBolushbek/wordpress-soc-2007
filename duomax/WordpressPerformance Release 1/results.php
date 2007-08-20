<?php
 
 /**
  * Contains testbanks, runs them and saves the results to XML
  */
 class Results
 {
 private $suite;
 	
 	public function Results()
 	{	
 	}
 
 	public function runTests($runs)
 	{	
 		//include framework
 		include "PerformanceTest.php"; 
 		//suite to hold all tests
 		$suite = new testSuite();
 
 		//include test banks to be run
		//include "TestBank.php";		
 		include "FormatingPerformance.php";
 		include "PostPageAndAttachementFunctions.php";
 		include "UserAndAuthorFunctions.php";
 		include "CategoryFunctions.php";
 		include "Miscellaneous.php";
 		$this ->suite = $suite;
 	}
 
 	public function writeXML($file)
 	{
  		//write the results to XML
  		$doc = new DOMDocument();
  		$doc->formatOutput = true;
  
  		$tests = $doc->createElement("tests");
  		$doc->appendChild($tests);
  
 	 	//get the array of results
 		$resultsArray = $this->suite->getTestCases();
 		foreach($resultsArray as $testCase)
 		{
	 		//create the test element 
	 		$test = $doc->createElement("test");
 	
	 		//attach test's elements
	 		$result = $testCase->getResult(); 
	 		$resultElement = $doc ->createElement("result");
	 		$resultElement ->appendChild($doc->createTextNode($result));
 		
 			$category = $testCase->getCategory(); 
 			$categoryElement = $doc ->createElement("category");
 			$categoryElement ->appendChild($doc->createTextNode($category));
 	
 			$name = $testCase->getName(); 
 			$nameElement = $doc ->createElement("name");
 			$nameElement ->appendChild($doc->createTextNode($name));
 	
 			$description = $testCase->getDescription(); 
 			$descriptionElement = $doc ->createElement("description");
 			$descriptionElement ->appendChild($doc->createTextNode($description));
 	
 			//attach elements to their parent's
 			$test ->appendChild($resultElement);
 			$test ->appendChild($categoryElement);
 			$test ->appendChild($nameElement);
 			$test ->appendChild($descriptionElement);
 			$tests ->appendChild($test); 
 			$doc  ->appendChild($tests);
 		}
 
		 //writes the string to disk
 		$xmlString = $doc->saveXML();
 		$openFile = fopen($file, 'w');
 		if(!$openFile) 
 			throw new Exception("Could not open XML file");
 
 		else
 		{
 			fputs($openFile, $xmlString);
 	  	 fclose($openFile);
	 	}
	 }
 }
?>