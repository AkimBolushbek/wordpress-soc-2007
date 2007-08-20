<?php
/*
 *Author: Andrew Nelson
 *Holds tests for post, page and attachement functions
 */

define('DB_USER', "");
define('DB_PASSWORD', "");
define('DB_HOST', "");
define('DB_NAME', "");
 
include "wordpress/wp-includes/post.php";
include "wordpress/wp-includes/cache.php";
include "wordpress/wp-includes/wp-db.php";
include "wordpress/wp-includes/plugin.php";
include "wordpress/wp-includes/functions.php";
include "wordpress/wp-includes/feed.php";
include "wordpress/wp-includes/rss.php";
include "wordpress/wp-includes/link-template.php";
include "wordpress/wp-includes/cron.php";
include "wordpress/wp-includes/category-template.php";
include "wordpress/wp-includes/category.php";

$dbuser = "";
$dbpassword = ""; 
$dbname = ""; 
$dbhost = "";
global $wpdb; 
$wpdb = new wpdb($dbuser, $dbpassword, $dbname, $dbhost);

$table_prefix = "wp_"; 
// $table_prefix is deprecated as of 2.1
$wpdb->prefix = $table_prefix;

if ( preg_match('|[^a-z0-9_]|i', $wpdb->prefix) && !file_exists(ABSPATH . 'wp-content/db.php') )
	die("<strong>ERROR</strong>: <code>$table_prefix</code> in <code>wp-config.php</code> can only contain numbers, letters, and underscores.");

// Table names
$wpdb->posts          = $wpdb->prefix . 'posts';
$wpdb->users          = $wpdb->prefix . 'users';
$wpdb->categories     = $wpdb->prefix . 'categories';
$wpdb->post2cat       = $wpdb->prefix . 'post2cat';
$wpdb->comments       = $wpdb->prefix . 'comments';
$wpdb->link2cat       = $wpdb->prefix . 'link2cat';
$wpdb->links          = $wpdb->prefix . 'links';
$wpdb->options        = $wpdb->prefix . 'options';
$wpdb->postmeta       = $wpdb->prefix . 'postmeta';
$wpdb->usermeta       = $wpdb->prefix . 'usermeta';
$wp_object_cache = new WP_Object_Cache();



 
  class WpInsertPost extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_array = Array(); 
 			$post_array["post_title"] = "Generated Post";
 			$post_array["post_content"] = "Content, content, content, content" . 
			"content, content, content, content, content, content, content, content, content";
 			$post_array["post_status"] = "private";
 			$post_array["post_author"] = 1;
 			wp_insert_post($post_array);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	}
 	
 	protected function randomTest()
 	{
 		$post_id_1 = 0;
 		$stringGenerator = new randomString();
 		$title = $stringGenerator->generateString(20);
 		$content = $string = $stringGenerator->generateString(1000);
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_array = Array(); 
 			$post_array["post_title"] = $title;
 			$post_array["post_content"] = $content;
 			$post_array["post_status"] = "private";
 			$post_array["post_author"] = 1;
 			wp_insert_post($post_array);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);
 	
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_insert_post";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests wp_insert_post";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of WpInsertPost 
    
  //run WpInsertPost
 $test = new WpInsertPost($suite, $runs);
 $test->run();
 
  class WpUpdatePost extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_array = Array(); 
 			$post_array["post_title"] = "Generated Post";
 			$post_array["post_content"] = "Content, content, content, content" . 
			"content, content, content, content, content, content, content, content, content";
			$post_array["post_id"] = $i;
 			wp_update_post($post_array);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_update_post";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests wp_update_post";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of WpUpdatePost 
    
  //run WpUpdatePost
 $test = new WpUpdatePost($suite, $runs);
 $test->run();
 
  class WpPublishPost extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			wp_publish_post($i);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_publish_post";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests wp_publish_post";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of WpPublishPost 
    
  //run WpPublishPost
 $test = new WpPublishPost($suite, $runs);
 $test->run();
 
 class GetPost extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_id = 1; 
 			//$array = Array();
 			//$post_id_1 = get_post($post_id, $array);
 			$post_id_1 = get_post($i);
 		} 
		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time); 
 	}
 	
 	public function run()
 	{
 		$this->name = "get_post";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests get_post";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of getPost 
    
 //run GetPost
 $test = new GetPost($suite, $runs);
 $test->run();

 class GetPostMimeType extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_id = 1; 
 			$post_id_1 = get_post_mime_type($post_id);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "get_post_mime_type";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests get_post_mime_type";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of GetPostMimeType 
    
  //run GetPostMimeType
 $test = new GetPostMimeType($suite, $runs);
 $test->run(); 
 
 //corrdinate delete and insert so one creates the post
 //and the other deletes it each run
 class WpDeletePost extends PerformanceTest{
	 
 	
 	protected function constantTest()
 	{
 		$post_id_1 = 0;
 		$time = microtime(true); 		
 		for($i = 0; $i< $this ->runNumber; $i++)
 		{
 			$post_id = $i; 
 			$post_id_1 = wp_delete_post($post_id);
 		} 
 		
 		$time = microtime(true) - $time;  
 		$this->enterResult($time);  
 	}
 	
 	public function run()
 	{
 		$this->name = "wp_delete_post";
 		$this->category = "post page and attachement functions";
 		$this->description = "Tests wp_delete_post";
 		//$this->randomTest(); 
 		$this->constantTest();
 	}
 }//end of WpDeletePost 
    
  //run WpDeletePost
 $test = new WpDeletePost($suite, $runs);
 $test->run();
?>
