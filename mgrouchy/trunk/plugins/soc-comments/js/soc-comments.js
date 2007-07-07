function addReplyForm( commentid, userident, useremail, userurl){
	var divname = 'com-' + commentid;
	var divid = '#' + divname;
	
	//check to see if the form already exists, if it does, remove it
	if ((jQuery("*").index( jQuery('#comment-reply-form')[0] )) != -1){
		return clearInner(divid);
	}

	// build our form and put it the comments div
	jQuery('#' + divname).css({display: 'none'});
	jQuery("<form action=\"/wp-comments-post.php\" method=\"POST\" id=\"comment-reply-form\">" + 
	      "<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>" +
	      "<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />" +
	      "<input name=\"submit\" type=\"submit\" id=\"cancel\" tabindex=\"6\" value=\"Cancel\" onclick=\"return clearInner('" + divid + "')\" />" +
		  "<input type=\"hidden\" name=\"comment_post_ID\" value=\"" + commentid + "\" />" +
	      "<input type=\"hidden\" id=\"author\" name=\"author\" value=\"" + userident + "\" />" +
	      "<input type=\"hidden\" id=\"email\" name=\"email\" value=\"" + useremail + "\" />" +
	      "<input type=\"hidden\" id=\"url\" name=\"url\" value=\"" + userurl + "\" />" +
	      "</p></form>").appendTo(divid);
	
	//bind our form, then show it
	bindForm(divname,'comment-reply-form');
	jQuery(divid).show("slow");
	
	return false;	
}


//open a link with ajax and then post with div
function openLink(url,  method, target) {
	//append some data to the url we are trying to open
	url = url + "&ajax=1";
	jQuery.ajax({
		type: method,
		url: url,
		//data: querystring,
		dataType : "html",
		success : function(html) {
			jQuery(target).attr("innerHTML", html);
		},
	});
	
	return false;

}

//function to empty an element gracefully
function clearInner(elemid) {
	jQuery(elemid).hide('slow');
	jQuery(elemid).empty();
	return false;
}


//bind a form using form plugin
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
