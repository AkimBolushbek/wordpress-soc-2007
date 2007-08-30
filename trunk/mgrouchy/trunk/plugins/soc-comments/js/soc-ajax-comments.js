
jQuery(document).ready(function(){
	//get our commentform
	var commentform = jQuery('#commentform');
    //when we dubmit our form
	commentform.submit(function() { 
						 
    jQuery(this).ajaxSubmit({
	   
		beforeSubmit: prepare ,
		url: formurl,
        success: onSuccess,	
	   }); 
		
        return false; 
	}); 
}); 

function prepare(formData, jqForm, options ) { 
   //add extra data to our form 
	formData.push({ name: 'ajax', value: '1'}, {name: 'postcomments', value: '1' });

	//now lets validate the form
	if(!jqForm.find('a[@title="Log out of this account"]')[0]) {

		var authorval = jQuery('#author').fieldValue(); 
		var emailval =  jQuery('#email').fieldValue();
		var commentdata = jQuery('#comment').fieldValue();
		
		if ( ( authorval == '' ) || ( emailval == 0 ) ) {
			alert("Error: please fill the required fields (name, email).");
			return false;
		}	
		
		var emailfilt  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if ( !emailfilt.test(emailval) ) {
			alert("Error: please enter a valid email address");
			return false;
		}
			
		if ( commentdata = '' ) {
			alert("You must enter a comment");
			return false;
		}
	}
	return true;
}

function onSuccess(responseText) {
	 if ((jQuery("*").index( jQuery('.commentlist')[0] )) != -1) {
		jQuery(responseText).appendTo('.commentlist');
	}
	else if ((jQuery("*").index( jQuery('#commentlist')[0] )) != -1){
		jQuery(responseText).appendTo('#commentlist');
	}
	else {
		jQuery('<ol class="commentlist" id="commentlist"></ol>').insertBefore('#commentform');
		jQuery(responseText).appendTo('#commentlist').show('slow'); 
	}
	jQuery('#commentform').clearForm();
}					
