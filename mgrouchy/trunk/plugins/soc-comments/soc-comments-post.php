<?php
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
      header('Allow: POST');
      header('HTTP/1.1 405 Method Not Allowed');
      header('Content-Type: text/plain');
      exit;
}

require(  '../../../wp-config.php' );

if (!isset($_POST['ajax'])) $ajax = 0;
else $ajax = (int) $_POST['ajax'];

if (!isset($_POST['postcomments'])) $postcomments = 0;
else $postcomments = (int) $_POST['postcomments'];

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($status->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	exit;
} elseif ( 'closed' ==  $status->comment_status ) {
	do_action('comment_closed', $comment_post_ID);
	wp_die( __('Sorry, comments are closed for this item.') );
} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
	do_action('comment_on_draft', $comment_post_ID);
	exit;
}

$comment_author       = trim(strip_tags($_POST['author']));
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim($_POST['comment']);

// If the user is logged in
$user = wp_get_current_user();
if ( $user->ID ) {
	$comment_author       = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url   = $wpdb->escape($user->user_url);
	if ( current_user_can('unfiltered_html') ) {
		if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
} else {
	if ( get_option('comment_registration') )
		wp_die( __('Sorry, you must be logged in to post a comment.') );
}

$comment_type = '';

if ( get_option('require_name_email') && !$user->ID ) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
		wp_die( __('Error: please fill the required fields (name, email).') );
	elseif ( !is_email($comment_author_email))
		wp_die( __('Error: please enter a valid email address.') );
	}

if ( '' == $comment_content ) 
	wp_die( __('Error: please type a comment.') );

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

$comment_id = wp_new_comment( $commentdata );

$comment = get_comment($comment_id);
if ( !$user->ID ) {
	setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_url_' . COOKIEHASH, clean_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
}
if ( $ajax ) {
	
	if ( $postcomments ) {	
		//inspiration for this code derived from another ajax commenting plugin
		$comment = $wpdb->get_row("SELECT * FROM {$wpdb->comments} WHERE comment_ID = $comment_id LIMIT 1;");
		$commentcount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_post_ID = '".$wpdb->escape($comment_post_ID)."' LIMIT 1;");
		$post->comment_status = $wpdb->get_var("SELECT comment_status FROM {$wpdb->posts} WHERE ID = '".$wpdb->escape($comment_post_ID)."' LIMIT 1;");

		ob_start();
		$comments = array($comment); 
		include(TEMPLATEPATH.'/comments.php');
	   	$commentout = ob_get_clean();
   	    preg_match('#<li(.*?)>(.*)</li>#ims', $commentout, $matches); // Regular Expression cuts out the LI element's HTML
 
   echo '<li ' .$matches[1].' >'.$matches[2].'</li>';
   exit;
		
	
	}
	$soc_com->get_comment_list_item($comment_id);
	exit;

}

$location = ( empty($_POST['redirect_to']) ? get_permalink($comment_post_ID) : $_POST['redirect_to'] ) . '#comment-' . $comment_id;
$location = apply_filters('comment_post_redirect', $location, $comment);
wp_redirect($location);

?>
