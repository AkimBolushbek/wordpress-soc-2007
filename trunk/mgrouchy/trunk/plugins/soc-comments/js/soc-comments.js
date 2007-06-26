function addReplyForm(commentid){
	var divname = 'com-' + commentid;
	var divid = '#' + divname;
	jQuery('#' + divname).css({display: 'none'});
	jQuery("<?php if ('open' == $post->comment_status) : ?>").appendTo(divid);
	jQuery("<form action=\"<?php echo get_option('siteurl'); ?>/wp-comments-post.php\" method=\"POST\" id=\"comment-reply-form\">").appendTo(divid);
	jQuery("<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>").appendTo(divid);
	jQuery("<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" name=\"comment_post_ID\" value=\"<?php echo $id; ?>\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"author\" name=\"author\" value=\"<?php echo $user_identity; ?>\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"email\" name=\"email\" value=\"<?php echo $user_email; ?>\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"url\" name=\"url\" value=\"<?php echo $user_url; ?>\" />").appendTo(divid);
	jQuery("</p>").appendTo(divid);
	jQuery("<?php do_action('comment_form', $post->ID); ?>").appendTo(divid);
	jQuery("</form>").appendTo(divid);
	jQuery("<?php else : ?>").appendTo(divid);
	jQuery("<p> Sorry. Comments are closed for this post").appendTo(divid);
	jQuery("<?php endif; ?>").appendTo(divid);
	
	bindForm(divname,'comment-reply-form');
	
}

function bindForm(divname,formname) {
	jQuery(document).ready(function() {
	    var options = {
	        target: '#' + divname,
	        beforeSubmit: showRequest,
	        success: showResponse,
	    };
	// bind 'myForm' and provide a simple callback function
	    jQuery('#' + formname).ajaxForm(options);
	});
}

function showRequest(formData, jqForm, options) {

    var qs = jQuery.param(formData);
    alert(qs);
    return true;
}


function showResponse(responseText, statusText) {
	if (statusText == "success" ){
		alert('responseText: \n' + responseText );

	}
}




