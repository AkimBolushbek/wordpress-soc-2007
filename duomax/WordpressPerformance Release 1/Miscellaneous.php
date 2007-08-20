<?php
/*
 * Created on Aug 20, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class CurrentTime extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			current_time("mysql");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "current_time";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests current_time";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of currentTime
    
  //run WpInsertPost
 $test = new CurrentTime($suite, $runs);
 $test->run();
 
 class GetOption extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			get_option("siteurl");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "get_option";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests get_option";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of GetOption
    
  //run WpInsertPost
 $test = new GetOption($suite, $runs);
 $test->run();
 
  class FormOption extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			form_option("");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "form_option";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests form_option";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of FormOption
    
  //run WpInsertPost
 $test = new FormOption($suite, $runs);
 $test->run();
 
   class UpdateOption extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			update_option("long option name", "the value of the option", "the description of the option", "yes");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "update_option";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests update_option";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of UpdateOption
    
  //run WpInsertPost
 $test = new UpdateOption($suite, $runs);
 $test->run();
 
 class AddOption extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			add_option("option added", "the value of the option", "the description of the option", "yes");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "add_option";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests add_option";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of AddOption
    
  //run WpInsertPost
 $test = new AddOption($suite, $runs);
 $test->run();
 
 class DeleteOption extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			delete_option("option added");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "delete_option";
 		$this->category = "Miscellaneous";
 		$this->description = "Tests delete_option";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of currentTime
    
  //run WpInsertPost
 $test = new DeleteOption($suite, $runs);
 $test->run();
?>
