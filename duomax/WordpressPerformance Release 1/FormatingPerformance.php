<?php
/*
 * Created on Jun 17, 2007
 * Author: Andrew Nelson
 * Holds the formatting tests
 */
 
 include "wordpress/wp-includes/formatting.php"; 
 
 class wptexturize extends PerformanceTest{
	
 
 	protected function randomTest()
 	{
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wptexturize($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	protected function constantTest()
 	{
 		$string = "twere twas bout fishing on the lake nuff cause to be around.";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wptexturize($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "wptexturize";
 		$this->category = "formatting";
 		$this->description = "Tests wptexturize";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of wptexturize
 
    
  //run wptexturize
 $test = new wptexturize($suite, $runs);
 $test->run();
 
 //tests the clean_pre function
 class cleanPre extends PerformanceTest{	
 
 	protected function randomTest()
 	{
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			clean_pre($string);
 		}  
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	protected function constantTest()
 	{
 		$string = "Hello how are you? <br> what is up <p>" . 
 		"What's going on with you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow what's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			clean_pre($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "clean_pre";
 		$this->category = "formatting";
 		$this->description = "Tests clean_pre";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of clean_pre 
 
 //run clean_pre
 $test = new cleanPre($suite, $runs);
 $test->run();
 
 class wpautop extends PerformanceTest{	
 
 	protected function randomTest()
 	{
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wpautop($string);
 		}  
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	protected function constantTest()
 	{
 		$string = "Hello how are you? <br> what is up <p>" . 
 		"What's going on with you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow what's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wpautop($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "wpauto";
 		$this->category = "formatting";
 		$this->description = "Tests wpauto";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of wpautop

//run wpautop
 $test = new wpautop($suite, $runs);
 $test->run();
 
class seemsUtf8 extends PerformanceTest
{
	
	protected function randomTest()
 	{
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			seems_utf8($string);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello how are you? <br> what is up <p>" . 
 		"What's going on with you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow what's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			seems_utf8($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "seems_utf8";
 		$this->category = "formatting";
 		$this->description = "Tests seems_utf8";
 		$this->randomTest(); 
 		$this->constantTest();
 	}	
}//end of seems_utf8

//run wpautop
 $test = new seemsUtf8($suite, $runs);
 $test->run();

class wpSpecialchars extends PerformanceTest
{
   
   protected function randomTest()
 	{
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wp_specialchars($string);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello how are you? <br> what is up <p>" . 
 		"What's going on with you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow what's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wp_specialchars($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_specialchars";
 		$this->category = "formatting";
 		$this->description = "Tests wp_specialchars";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
	
}

//run wpSpecialchars
 $test = new wpSpecialchars($suite, $runs);
 $test->run();
 
 class removeAccents extends PerformanceTest
 {
	protected function randomTest()
	{			
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			remove_accents($string);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello' how are you? <br> what is up <p>" . 
 		"What's going on with' you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow wha't's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!''''";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			remove_accents($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "remove_accents";
 		$this->category = "formatting";
 		$this->description = "Tests remove_accents";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }
//run wpSpecialchars
 $test = new removeAccents($suite, $runs);
 $test->run();
 
 class sanitizeUser extends PerformanceTest
 {
 	protected function randomTest()
	{			
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{	
 			sanitize_user($string, false);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello' how are you? <br> what is up <p>" . 
 		"What's going on with' you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow wha't's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!''''";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			sanitize_user($string, false);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "sanitize_user";
 		$this->category = "formatting";
 		$this->description = "Tests sanitize_user";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }
 //run sanitizeUser
 //$test = new sanitizeUser($suite, $runs);
 //$test->run();
 
 
 class sanitizeTitle extends PerformanceTest
 {
 	protected function randomTest()
	{			
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{	
 			sanitize_title($string);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello' how are you? <br> what is up <p>" . 
 		"What's going on with' you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow wha't's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!''''";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			sanitize_title($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "sanitize_user";
 		$this->category = "formatting";
 		$this->description = "Tests sanitize_user";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }
 //run sanitizeTitle
 //$test = new sanitizeTitle($suite, $runs);
 //$test->run();
 
class sanitizeTitleWithDashes extends PerformanceTest
 {
 	protected function randomTest()
	{			
 		$stringGenerator = new randomString(); 
 		$string = $stringGenerator->generateString(1000); 
 	
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{	
 			sanitize_title_with_dashes($string);
 		}  
 	
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}		
 	
 	protected function constantTest()
 	{
 		$string = "Hello' how are you? <br> what is up <p>" . 
 		"What's going on with' you<br><br><br><br><br><p><p><p><p>". 
 		"howdy!!!!!<br><br><br><br><p>blahblahblah blah blah blah blahblahb".
 		"wow wha't's up!!!!!!! What's going on!!!!!!!!<br><br><br><p>".
 		"<p><p><br><br>howdy!!!!!!!!!!! More text. More text!''''";
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			sanitize_title_with_dashes($string);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "sanitize_user";
 		$this->category = "formatting";
 		$this->description = "Tests sanitize_user";
 		$this->randomTest(); 
 		$this->constantTest();
 	}
 }
 //run sanitizeTitle
 $test = new sanitizeTitleWithDashes($suite, $runs);
 $test->run();

?>
