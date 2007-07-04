function addReplyForm( commentid, userident, useremail, userurl){
	var divname = 'com-' + commentid;
	var divid = '#' + divname;
	// build our form and put it the comments div
	jQuery('#' + divname).css({display: 'none'});
	jQuery("<form action=\"/wp-comments-post.php\" method=\"POST\" id=\"comment-reply-form\">" + 
	      "<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>" +
	      "<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />" +
	      "<input type=\"hidden\" name=\"comment_post_ID\" value=\"" + commentid + "\" />" +
	      "<input type=\"hidden\" id=\"author\" name=\"author\" value=\"" + userident + "\" />" +
	      "<input type=\"hidden\" id=\"email\" name=\"email\" value=\"" + useremail + "\" />" +
	      "<input type=\"hidden\" id=\"url\" name=\"url\" value=\"" + userurl + "\" />" +
	      "</p></form>").appendTo(divid);
	
	bindForm(divname,'comment-reply-form');
	jQuery(divid).show("slow");
	
	return false;	
}

function bindForm(divname,formname) {
	jQuery(document).ready(function() {
	    var options = {
	        target: '#' + divname,
			beforeSubmit: addFormData ,
			//success: showResponse,
	    };
	    // bind form and provide a simple callback function
	    jQuery('#' + formname).ajaxForm(options);
	});
}

function addFormData(formData, jqForm, options ) {
	formData.push({ name: 'ajax', value: '1'});	
	
	//for testing purposes 
	//var qs = jQuery.param(formData);
	//alert(qs);
	return true;
}

//for testing
function showResponse(responseText, statusText) {
		alert('responseText: \n' + responseText );
}
