function addReplyForm(commentid,userident,useremail,userurl){
	var divname = 'com-' + commentid;
	var divid = '#' + divname;
	jQuery('#' + divname).css({display: 'none'});
	jQuery("<form action=\"<?php echo get_option('siteurl'); ?>/wp-comments-post.php\" method=\"POST\" id=\"comment-reply-form\">").appendTo(divid);
	jQuery("<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>").appendTo(divid);
	jQuery("<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" name=\"comment_post_ID\" value=\"" + commentid + "\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"author\" name=\"author\" value=\"" + userident + "\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"email\" name=\"email\" value=\"" + useremail + "\" />").appendTo(divid);
	jQuery("<input type=\"hidden\" id=\"url\" name=\"url\" value=\""+ userurl + "\" />").appendTo(divid);
	jQuery("</p>").appendTo(divid);
	jQuery("</form>").appendTo(divid);
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




