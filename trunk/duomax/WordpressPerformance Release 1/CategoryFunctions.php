<?php
/*
 * Created on Aug 20, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 class GetCategories extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			get_categories();
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "get_categories";
 		$this->category = "Category Functions";
 		$this->description = "Tests get_categories";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of GetCategories
    
  //run WpInsertPost
 $test = new GetCategories($suite, $runs);
 $test->run();
 
 class GetCatID extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			get_cat_id("Uncategorized");
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "get_cat_ID";
 		$this->category = "Category Functions";
 		$this->description = "Tests get_cat_ID";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of GetCategories
    
  //run WpInsertPost
 $test = new GetCatID($suite, $runs);
 $test->run();
 
 class GetCatName extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			get_cat_name(1);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	 	
 	public function run()
 	{
 		$this->name = "get_cat_name";
 		$this->category = "Category Functions";
 		$this->description = "Tests get_cat_name";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of GetCategories
    
  //run WpInsertPost
 $test = new GetCatName($suite, $runs);
 $test->run();
?>
