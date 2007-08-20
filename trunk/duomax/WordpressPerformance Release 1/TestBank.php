<?php

  
 class Demo extends PerformanceTest{
	
 
 	protected function testOne()
 	{
 		$time = 0.5; 
 		$this ->enterResult($time); 
 	}
 	
 	protected function testTwo()
 	{
 		$time = 1.5; 
 		$this ->enterResult($time);
 	}
 	
 	public function run()
 	{
 		$this->name = "demo";
 		$this->testOne(); 
 		$this->testTwo();
 	}
 }
  
 $test = new Demo($suite);
 
 $test->run();
 echo $test->getResult();
 echo $test->getName();
?>
