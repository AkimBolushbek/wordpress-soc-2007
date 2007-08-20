<?php
 
 class XMLLoader
 { 	
 	private $fileName;
 	private $xmlDocument;
 	 
 	public function XMLLoader($fileName)
 	{
 		$this -> fileName;
 		 		
 		//load the XML file into DOM here
 		$doc = new DOMDocument();
  		$doc->load($fileName);  		
  		$this -> xmlDocument = $doc; 
 	} 	
 	
 	//returns the xml comparisions as an array
 	public function compareXML($xmlFile, &$xmlResults)
	{
		$tests = $this ->xmlDocument ->getElementsByTagName("test");
		$docTwoTests = $xmlFile ->getElementsByTagName("test");
		$xmlArrayOne = Array();
		$xmlArrayTwo = Array();
		$xmlResults = Array();
		
		//loop to turn domXML documents into arrays
		foreach($tests as $test)
		{
			$xmlArrayOne[] = $test; 
		}
		
		foreach($docTwoTests as $test)
		{
			$name = $test -> getElementsByTagName("name");
			$xmlArrayTwo[$name->item(0) -> nodeValue] = $test; 
		}
		
		//compare XML results
		foreach($xmlArrayOne as $test)
		{
			$name = $test ->getElementsByTagName("name");
			$testTwo = $xmlArrayTwo[$name->item(0) -> nodeValue]; 
			
			//calculate the result difference as a percentage
			$resultDifference = 0;
			if($testTwo != null)
			{
				$resultOne = $test ->getElementsByTagName("result");
				$resultTwo = $testTwo ->getElementsByTagName("result");
				$resultDifference = $resultOne->item(0) -> nodeValue - $resultTwo ->item(0) -> nodeValue;
				$resultDifference = $resultDifference/$resultOne->item(0) -> nodeValue;
				
			}
			else
			{
				$resultDifference = "no comparision";
			}
			
			$comparision = new XMLComparision();
			
			$category = $test ->getElementsByTagName("category");
			$description = $test ->getElementsByTagName("description"); 
			
			//put data into comparision object
			$comparision -> name = $name ->item(0) -> nodeValue;
			$comparision -> category = $category ->item(0) -> nodeValue;
			$comparision -> description = $description ->item(0) -> nodeValue;
			$comparision -> result = $resultDifference;
			$xmlResults[] = $comparision;  			
		}	
	}
 	
 	public function getXML()
 	{
 		return $this -> xmlDocument; 
 	}
 }//end of XMLLoader
 
 class XMLComparision
 {
 	public $result; 
 	public $name;
 	public $category; 
 	public $description; 	
 }

 
?>
