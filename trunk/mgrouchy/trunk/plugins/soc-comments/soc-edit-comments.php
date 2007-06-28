<?php

if (!isset($_GET['ajax'])) $ajax = 0;
else $ajax = (int) $_GET['ajax'];

if ( !$ajax ) :

require_once('admin.php');

$title = __('Edit Comments');
$parent_file = 'edit-comments.php';
wp_enqueue_script( 'admin-comments' );
wp_enqueue_script( 'soc-comments-js');

require_once('admin-header.php');

endif;

if (empty($_GET['mode'])) $mode = 'view';
else $mode = attribute_escape($_GET['mode']);

if (!isset($_GET['replyid'])) $replyid = -1;
else $replyid = (int) $_GET['replyid']; 
if ( !$ajax ) :
?>

<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}

//-->
</script>
<div class="wrap">
<h2><?php _e('Comments'); ?></h2>

 <form name="searchform" action="" method="get" id="editcomments">  
 <fieldset>
 <legend><?php _e('Show Comments That Contain...') ?></legend>
 <div><?php _e('Search by:') ?>
 <select name="sfield">
 	<option value="c_author"><?php _e('Author') ?></option>
	<option value="c_aurl"><?php _e('Author URL') ?></option>
	<option value="c_aemail"><?php _e('Author Email') ?></option>
	<option value="c_aip"><?php _e('Author IP') ?></option>
	<option value="c_content"><?php _e('Content') ?></option>
 	<option value="all"><?php _e('All Fields') ?></option>
 </select>
 <input type="text" name="s" value="<?php if (isset($_GET['s'])) echo attribute_escape($_GET['s']); ?>" size="17" /> 
 <input type="submit" name="submit" value="<?php _e('Search') ?>" />  
 <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
</div>
 </fieldset> 
</form>
<p><a href="?mode=view"><?php _e('View Mode') ?></a> | <a href="?mode=edit"><?php _e('Mass Edit Mode') ?></a></p>

<?php
if ( !empty( $_POST['delete_comments'] ) ) :
	check_admin_referer('bulk-comments');

	$i = 0;
	foreach ($_POST['delete_comments'] as $comment) : // Check the permissions on each
		$comment = (int) $comment;
		$post_id = (int) $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = $comment");
		// $authordata = get_userdata( $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $post_id") );
		if ( current_user_can('edit_post', $post_id) ) {
			if ( !empty( $_POST['spam_button'] ) )
				wp_set_comment_status($comment, 'spam');
			else
				wp_set_comment_status($comment, 'delete');
			++$i;
		}
	endforeach;
	echo '<div style="background-color: rgb(207, 235, 247);" id="message" class="updated fade"><p>';
	if ( !empty( $_POST['spam_button'] ) ) {
		printf(__ngettext('%s comment marked as spam', '%s comments marked as spam.', $i), $i);
	} else {
		printf(__ngettext('%s comment deleted.', '%s comments deleted.', $i), $i);
	}
	echo '</p></div>';
endif;
echo '<div id="replydiv">';
endif;

if ( isset( $_GET['apage'] ) )
	$page = (int) $_GET['apage'];
else
	$page = 1;

$start = $offset = ( $page - 1 ) * 20;

list($_comments, $total) = $soc_com->get_comment_list( $start, 25, isset($_GET['s']) ? $_GET['s'] : false, isset($_GET['sfield']) ? $_GET['sfield'] :false); // Grab a few extra

$comments = array_slice($_comments, 0, 20);
$extra_comments = array_slice($_comments, 20);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'apage', '%#%' ), 
	'format' => '',
	'total' => ceil($total / 20),
	'current' => $page
));

if ( $page_links )
	echo "<p class='pagenav'>$page_links</p>";

if ('view' == $mode) {
	if ($comments) {
		$offset = $offset + 1;
		$start = " start='$offset'";

		echo "<ol id='the-comment-list' class='commentlist' $start>\n";
		$i = 0;
		foreach ( $comments as $comment ) {
			get_comment( $comment ); // Cache it
			$commentid = $comment->comment_ID;
			$soc_com->get_comment_list_item( $commentid, ++$i, ($commentid == $replyid) );
		}
		echo "</ol>\n\n";

if ( $extra_comments ) : ?>
<div id="extra-comments" style="display:none">
<ul id="the-extra-comment-list" class="commentlist">
<?php
	foreach ( $extra_comments as $comment ) {
		get_comment( $comment ); // Cache it
		$soc_com->get_comment_list_item( $comment->comment_ID, ++$i );
	}
?>
</ul>
</div>
<?php endif; // $extra_comments ?>

<div id="ajax-response"></div>

<?php
	} else { //no comments to show

		?>
		<p>
			<strong><?php _e('No comments found.') ?></strong></p>

		<?php
	} // end if ($comments)
} elseif ('edit' == $mode) {

	if ($comments) {
		echo '<form name="deletecomments" id="deletecomments" action="" method="post"> ';
		wp_nonce_field('bulk-comments');
		echo '<table class="widefat">
<thead>
  <tr>
    <th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById(\'deletecomments\'));" /></th>
    <th scope="col">' .  __('Name') . '</th>
    <th scope="col">' .  __('E-mail') . '</th>
    <th scope="col">' . __('IP') . '</th>
    <th scope="col">' . __('Comment Excerpt') . '</th>
	<th scope="col" colspan="3" style="text-align: center">' .  __('Actions') . '</th>
  </tr>
</thead>';
		foreach ($comments as $comment) {
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $comment->comment_post_ID"));
		$comment_status = wp_get_comment_status($comment->comment_ID);
		$class = ('alternate' == $class) ? '' : 'alternate';
		$class .= ('unapproved' == $comment_status) ? ' unapproved' : '';
?>
  <tr id="comment-<?php echo $comment->comment_ID; ?>" class='<?php echo $class; ?>'>
    <td style="text-align: center"><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
    <td><?php comment_author_link() ?></td>
    <td><?php comment_author_email_link() ?></td>
    <td><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></td>
    <td><?php comment_excerpt(); ?></td>
    <td>
    	<?php if ('unapproved' == $comment_status) { ?>
    		(Unapproved)
    	<?php } else { ?>
    		<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php comment_ID() ?>" class="edit"><?php _e('View') ?></a>
    	<?php } ?>
    </td>
    <td><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo "<a href='comment.php?action=editcomment&amp;c=$comment->comment_ID' class='edit'>" .  __('Edit') . "</a>"; } ?></td>
    <td><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		echo "<a href=\"comment.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;c=".$comment->comment_ID."\" onclick=\"return deleteSomething( 'comment', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to delete this comment by '%s'. \n  'Cancel' to stop, 'OK' to delete."), $comment->comment_author ))  . "', theCommentList );\" class='delete'>" . __('Delete') . "</a> ";
		} ?></td>
  </tr>
		<?php 
		} // end foreach
	?></table>
<p class="submit"><input type="submit" name="delete_button" class="delete" value="<?php _e('Delete Checked Comments &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deletecomments')); if(numchecked < 1) { alert('<?php echo js_escape(__("Please select some comments to delete")); ?>'); return false } return confirm('<?php echo sprintf(js_escape(__("You are about to delete %s comments permanently \n  'Cancel' to stop, 'OK' to delete.")), "' + numchecked + '"); ?>')" />
			<input type="submit" name="spam_button" value="<?php _e('Mark Checked Comments as Spam &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deletecomments')); if(numchecked < 1) { alert('<?php echo js_escape(__("Please select some comments to mark as spam")); ?>'); return false } return confirm('<?php echo sprintf(js_escape(__("You are about to mark %s comments as spam \n  'Cancel' to stop, 'OK' to mark as spam.")), "' + numchecked + '"); ?>')" /></p>
  </form>
<div id="ajax-response"></div>
<?php
	} else {
?>
<p>
<strong><?php _e('No results found.') ?></strong>
</p>
<?php
	} // end if ($comments)
}
echo '</div>';
if ( $page_links )
	echo "<p class='pagenav'>$page_links</p>";

?>

</div>

<?php 
if ( !$ajax ) 
	include('admin-footer.php'); ?>
