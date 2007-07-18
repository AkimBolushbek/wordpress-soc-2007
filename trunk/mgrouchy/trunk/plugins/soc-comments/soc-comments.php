<?php
/*
Plugin Name: SoC Comment Panel Upgrades
Plugin URI: http://wordpress-soc-2007.googlecode.com/svn/trunk/mgrouchy/soc-comments
Description: A number of upgrades to WordPress's commenting system
Version: 0.1
Author: Mike Grouchy
Author URI: http://www.mikegrouchy.com

*/

//get our plugin dir
$plugindir = get_option('homeurl'). '/wp-content/plugins/soc-comments/' ;

//create soc_comments class
if ( !class_exists( "soc_comments" ) ){
	class soc_comments {
	
		//constructor
		function soc_comments() {
		
		}
		
		
		//replace wp-admin/edit-comments.php with my version
		function replace_edit_comment( $arg = '' ){
			global $wpdb,$menu,$submenu,$comment,$soc_com;
			include('soc-edit-comments.php');
			exit;
		}
		
		//get list of comments
		function get_comment_list( $start, $num , $s = false, $sfield = false , $sort = "" , $filter = 'all'  ){
			global $wpdb;
			
				
			$ss_params = array(
            	"c_author" => "comment_author",
            	"c_aurl" => "comment_author_url",
            	"c_aemail" => "comment_author_email",
            	"c_aip" => "comment_author_ip",
            	"c_content" => "comment_content",
            	"c_date"  => "comment_date");
			
			if ( strcmp( 'all', $filter ) == 0 ) $filter = '';
			else $filter = "AND comment_type = '$filter'";
			
			$start = (int) $start;
			$num = (int) $num;
			$sort = $wpdb->escape($sort);     
			$order = "DESC";

			//set up our vars for sorting
			if ( isset( $ss_params[$sort] ) ){
				$sort = $ss_params[$sort];
				if ( $sort != "comment_date" ) $order = "ASC";
			}
			else {
				$sort = "comment_date";
			}
       		 
			 //if we have a search string
    	    if ( $s ) {
				$s = $wpdb->escape($s);
  	        	$sfield = $wpdb->escape($sfield);

				if ( ( $sfield == 'all' ) || ( !$sfield ) ) {
					$comments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE
                        (comment_author LIKE ('%$s%') OR
                        comment_author_email LIKE ('%$s%') OR
                        comment_author_url LIKE ('%$s%') OR
                        comment_author_IP LIKE ('%$s%') OR
                        comment_content LIKE ('%$s%') ) 
						$filter AND
                        comment_approved != 'spam'
                        ORDER BY $sort $order LIMIT $start, $num");
				}
				else {
					//set up the beginning of our SQL query
					$sq = "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE";
					//check is $sfield is set
					if ( isset( $ss_params[$sfield] ) )
						$sq2 = $ss_params[$sfield] . " LIKE ('%$s%')";

					//put our SQL query back together
					if ( isset( $sq2 ) )
                        $sq = "$sq ( $sq2 ) $filter AND comment_approved != 'spam' ORDER BY $sort $order LIMIT $start, $num";
					else
                        $sq = "$sq comment_approved != 'spam' $filter ORDER BY $sort $order LIMIT $start, $num";
                        $comments = $wpdb->get_results($sq);
				}
			}
			else {

			    $comments = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE ( comment_approved = '0' OR comment_approved = '1') $filter ORDER BY $sort $order LIMIT $start, $num" ); 
    	
	    }
	
			$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );
	
			return array($comments, $total);
	}
	
	
		//to replace _wp_comment_list_item
		function get_comment_list_item( $id, $alt = 0, $reply = false ){
			global $authordata, $comment, $wpdb, $user_identity, $user_email, $user_url; 
            get_currentuserinfo();	
			$id = (int) $id;
			$comment =& get_comment( $id );
			$class = '';
			$post = get_post($comment->comment_post_ID);
			$authordata = get_userdata($post->post_author);
			$comment_status = wp_get_comment_status($comment->comment_ID);
			if ( isset( $_GET['replyid'] ) )
				 $query = remove_query_arg('replyid');
			else 
				$query = add_query_arg('replyid' , $comment->comment_ID);
				
			if ( 'unapproved' == $comment_status )
				$class .= ' unapproved';
			if ( $alt % 2 )
				$class .= ' alternate';
			echo "<li id='comment-$comment->comment_ID' class='$class'>";
			?>
			<p><strong><?php comment_author(); ?></strong> <?php if ($comment->comment_author_email) { ?>| <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?>| <?php _e('IP:') ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>

			<?php comment_text() ?>

			<p><?php comment_date(__('M j, g:i A'));  ?> &#8212; [
			<?php
			if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
				echo " <a href='comment.php?action=editcomment&amp;c=".$comment->comment_ID."'>" .  __('Edit') . '</a>';
				echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . js_escape(sprintf(__("You are about to delete this comment by '%s'.\n'Cancel' to stop, 'OK' to delete."), $comment->comment_author)) . "', theCommentList );\">" . __('Delete') . '</a> ';
				if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
					echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Unapprove') . '</a> </span>';
					echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Approve') . '</a> </span>';
				}
				echo " | <a href=\"" . wp_nonce_url("comment.php?action=deletecomment&amp;dt=spam&amp;p=" . $comment->comment_post_ID . "&amp;c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . "\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to mark as spam this comment by '%s'.\n'Cancel' to stop, 'OK' to mark as spam."), $comment->comment_author))  . "', theCommentList );\">" . __('Spam') . "</a> ";
    			echo " | <a href='" . $query . "' onclick=' return addReplyForm(\"" . get_option('siteurl') . "/wp-comments-post.php\"," .  $id . "," . $comment->comment_post_ID . ",\"" . $user_identity . "\",\"" . $user_email . "\",\"" . $user_url . "\",\"" .  wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) . "\",\""  . add_query_arg('ajax','1') ."\")' >" . __('Reply') . " </a>";
			}
			$post = get_post($comment->comment_post_ID);
			$post_title = wp_specialchars( $post->post_title, 'double' );
			$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
			?>
 			] &#8212; <a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><?php echo $post_title; ?></a></p>
			
			<div id="com-<?php echo $comment->comment_ID; ?>" >
			<?php			
			if ( true == $reply ) : ?>	
			<?php if ('open' == $post->comment_status) : ?>

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="POST" id="comment-reply-form">
			
			<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

			<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
			<input type="hidden" name="comment_post_ID" value="<?php echo $comment->comment_post_ID; ?>" />
			<input type="hidden" id="author" name="author" value="<?php echo $user_identity; ?>" />
			<input type="hidden" id="email" name="email" value="<?php echo $user_email; ?>" />
			<input type="hidden" id="url" name="url" value="<?php echo $user_url; ?>" />
			<?php $qs = remove_query_arg('replyid'); ?>
			<input type="hidden" id="redirect_to"name="redirect_to" value="<?php echo $qs; ?>" />
			</p>
			<?php do_action('comment_form', $comment->comment_post_ID); ?>
			</form>
			<?php else : ?>
				<p> Sorry. Comments for this post are closed</p>
			</div>
			<?php endif; 
				endif;
				?>
				</li>
				<?php
		}
	}
			
}
	
if (class_exists("soc_comments")) {
	$soc_com = new soc_comments();
	global $soc_com;
}


if (isset($soc_com)) {
	add_action( 'load-edit-comments.php', array( &$soc_com,'replace_edit_comment' ), 9 );
	wp_register_script('soc-comments-js',  $plugindir . '/js/soc-comments.js', array('jquery', 'jquery-form'), '0.1');
}


?>
