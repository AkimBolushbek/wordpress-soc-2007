Notes: 
If you get a maxium execution error, then you need to extended that allowed execution time in php.ini

php 5.2 seems to have some issues with the current build of wordpress(2.2.2)
you may need to modify the wordpress source code to get php 5.2 to work. There is a ticket for this item

Setup: 
You must fill out password and database information in PostPageAndAttachementFunctions.php

Fill out 

//fill out these
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

//and these too 
$dbuser = "";
$dbpassword = ""; 
$dbname = ""; 
$dbhost = "";

You also need to add the wordpress source code (for the build you wish to test) to a wordpress folder. For instance, if your php deploy directory is htdocs then you'd make a htdocs/wordpress and copy the wordpress code to there