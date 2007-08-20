<?php
/*
 *Author: Andrew Nelson
 *Holds the test bank for the User and Author Functions
 */
 
 include "wordpress/wp-includes/user.php";
 include "wordpress/wp-includes/registration.php";
 include "wordpress/wp-includes/capabilities.php";
 include "wordpress/wp-includes/pluggable.php";
 include "wordpress/wp-includes/classes.php";
 
 class GetUsernumposts extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$user_id = 1; 
 			$post_id_1 = get_usernumposts($user_id);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "get_usernumposts";
 		$this->category = "user and author functions";
 		$this->description = "Tests get_usernumposts";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of getPost 
    
 //run GetUsernumposts
 $test = new GetUsernumposts($suite, $runs);
 $test->run();
 
  class GetUsermeta extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$user_id = 1;
 			get_usermeta($user_id);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "get_usermeta";
 		$this->category = "user and author functions";
 		$this->description = "Tests get_usermeta";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of getPost 
    
 //run GetUsermeta
 $test = new GetUsermeta($suite, $runs);
 $test->run();
 
 class UpdateUsermeta extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$user_id = 1;
 			update_usermeta($user_id, 'metakey', 'metavalue');
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "update_usermeta";
 		$this->category = "user and author functions";
 		$this->description = "Tests update_usermeta";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 
}//end of update_usermeta
    
 //run update_usermeta
 $test = new UpdateUsermeta($suite, $runs);
 $test->run();
 
 
 //not sure about this one. May have to be inventive to 
 //be able to test it
 class GetCurrentuserinfo extends PerformanceTest{
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			get_currentuserinfo();
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "get_currentuserinfo";
 		$this->category = "user and author functions";
 		$this->description = "Tests get_currentuserinfo";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 
}//end of get_currentuserinfo
    
 //run get_currentuserinfo
 $test = new GetCurrentuserinfo($suite, $runs);
 $test->run();
 
 class GetUserdata extends PerformanceTest{
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$user_id = 1;
 			$user_info = get_userdata($user_id);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "get_userdata";
 		$this->category = "user and author functions";
 		$this->description = "Tests get_userdata";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 
}//end of get_userdata
    
 //run get_userdata
 $test = new GetUserdata($suite, $runs);
 $test->run();
 
 class WpCreateUser extends PerformanceTest{
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{ 			
 			wp_create_user('username', 'password', 'email');
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_create_user";
 		$this->category = "user and author functions";
 		$this->description = "Tests wp_create_user";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 
}//end of wp_create_user
    
 //run wp_create_user
 $test = new WpCreateUser($suite, $runs);
 $test->run();
 
 
?>
