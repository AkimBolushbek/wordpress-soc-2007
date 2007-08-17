
function addReplyForm(action, commentid, comment_post_id, userident, useremail, userurl, noncefield, redirect){
	var divname = 'com-' + commentid;
	var divid = '#' + divname;
	
	//check to see if the form already exists, if it does, remove it
	if ((jQuery("*").index( jQuery('#comment-reply-form')[0] )) != -1){
		return clearInner(divid);
	}
	
	// build our form and put it the comments div
	jQuery('#' + divname).css({display: 'none'});
	jQuery("<form action=\"" + action + "\" method=\"POST\" id=\"comment-reply-form\">" + 
	      "<p><textarea name=\"comment\" id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"></textarea></p>" +
	      "<p><input name=\"submit\" type=\"submit\" id=\"submit\" tabindex=\"5\" value=\"Submit Comment\" />" +
	      "<input name=\"submit\" type=\"reset\" id=\"cancel\" tabindex=\"6\" value=\"Cancel\" onclick=\"return clearInner('" + divid + "')\" />" +
		  "<input type=\"hidden\" name=\"comment_post_ID\" value=\"" + comment_post_id + "\" />" +
	      "<input type=\"hidden\" id=\"author\" name=\"author\" value=\"" + userident + "\" />" +
	      "<input type=\"hidden\" id=\"email\" name=\"email\" value=\"" + useremail + "\" />" +
	      "<input type=\"hidden\" id=\"redirect_to\" name=\"redirect_to\" value=\"" + redirect + "\"/>" +
	      "<input type=\"hidden\" id=\"url\" name=\"url\" value=\"" + userurl + "\" />" +
	      "<input type=\"hidden\" id=\"_wp_unfiltered_html_comment\" name=\"_wp_unfiltered_html_comment\" value=\"" + noncefield + "\" /></p> </form>").appendTo(divid);
	
	//bind our form, then show it
	bindReplyForm('comment-reply-form');
	jQuery(divid).slideDown("slow");
	return false;	
}


//open a link with ajax and then post with div
function openLink(url,  method, target) {
	//append some data to the url we are trying to open
	url = url + "&ajax=1";
	jQuery.ajax({
		type: method,
		url: url,
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


//bind reply form using form plugin
function bindReplyForm(formname ) {
	jQuery(document).ready(function() {
		var options = {
			beforeSubmit: addFormData ,
			success: showReplyResponse,
		};

	//bind form providing callback
	jQuery('#' + formname).ajaxForm(options);
	});
}

//bind a form using form plugin
function bindForm(divname,formname) {
	jQuery(document).ready(function() {
	    var options = {
	        target: '#' + divname,
			beforeSubmit: addFormData ,
	    };
	    // bind form and provide a simple callback function
	    jQuery('#' + formname).ajaxForm(options);
	});
}

//push data onto the form submit call
function addFormData(formData, jqForm, options ) {
	formData.push({ name: 'ajax', value: '1'});	
	return true;
}

//show the response from the submission of the reply form
function showReplyResponse(responseText, statusText) {

	jQuery(document).ready(function() {
		//remove the reply form
		jQuery('#comment-reply-form').remove();
		//append our comment
		comList = jQuery('#the-comment-list');
		comList.prepend(responseText).slideDown("slow");
	});
}
