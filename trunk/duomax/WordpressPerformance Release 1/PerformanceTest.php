<?php
/**
 * Utility classes
 */

/**
 * class used by performance tests
 */
abstract class PerformanceTest{
	public $resultsSum; 
	public $count;
	public $category;
	public $name; 
	public $description; 
	public $testSuite;
	public $runNumber;
	
	//pass in the testing suite
	public function PerformanceTest($tests, $runs)
	{
		$this -> testSuite = $tests; 
		$this -> testSuite -> addTest($this);
		$this -> resultsSum = 0; 
		$this -> count = 0; 
		$this -> runNumber = $runs;
	}
 

	//get the name of the case
	public function getName()
	{
		return $this -> name;
	}

	//get the description of the case
	public function getDescription()
	{
		return $this -> description; 
	}
	
	//get the result of the tests 
	public function getResult()
	{
		return ($this -> resultsSum/ $this -> count); 
	}
	
	public function getCategory()
	{
		return $this -> category;
	}
	
	//get the results
	protected function enterResult($timingResult)
	{
		$this -> resultsSum += $timingResult; 
		$this -> count++;
	}
	
	public function run(){}
	 
}// end of performance test

/**
 * holds a group of testClasses
 */
class testSuite
{
	private $testCaseArray; 
	private $count = 0;
	
	//returns ojb
	public function getTestCases()
	{
		return $this -> testCaseArray; 
	}
	
	//adds test to array
	public function addTest($testClass)
	{
		$this -> testCaseArray[$this -> count] = $testClass;
		$this -> count++;  
	}
	
	//returns the testClasses as an array
	public function getTests()
	{
		return $this -> testCaseArray; 
	}
}//end of testSuite

//generates a random alphanumeric string of the inputed length
class randomString
{
	public function generateString($length)
	{
		$randomString = ""; 
		
		for($i = 0; $i < $length; $i++)
		{
			$random = mt_rand(32, 126); 
			$char = chr($random);
			$randomString .= strval($char); 		
		}
		
		return $randomString; 
	}
}

?>
