jQuery(document).ready(function($){
	language_tabs($);
	
	//post_edit($);
	//publish_validation($);
   cp_metaboxes_init($);
   cp_templates_metaboxes($);
   cp_sortable($);
});

function language_tabs($) {
    $('.cp-langs > span').unbind('click');

	$('.cp-langs > span').click(function(){
		$(this).parent().children('span').removeClass('active');
		$(this).parent().find('div').removeClass('active');
		$(this).addClass('active');
		
		var id = $(this).attr('id');
		$('#div'+id).addClass('active');
	});
}

function post_edit($) {
	if ($('#main-post-title').length > 0) {
		$('#titlewrap label').hide();
		$('#titlewrap input').clone().attr('type','hidden').insertAfter('#titlewrap input').prev().remove();
		$('#main-post-title').keyup(function(){
			$('#title').val($(this).val());
		});
	}
}

function publish_validation($) {
	$('#publish').click(function(){
		alert('asd');
	});
}

function media_upload_single(filetype, field_id, field_name, title, button) {
	media_upload(filetype, field_id, field_name, title, button, false);
}

function media_upload_multiple(filetype, field_id, field_name, title, button) {
	media_upload(filetype, field_id, field_name, title, button, true);
}

function media_upload(filetype, field_id, field_name, title, button, multiple) {
	
	if (filetype == 'file')
		filetype = '';
	
	var link_status = jQuery('#button_'+field_id+'').attr('disabled');

	// ignore clicks when button is disabled
	if (link_status == 'disabled')
		return null;
	
	var tgm_media_frame;
    
    // Bind to our click event in order to open up the new media experience.
    //$(document.body).on('click.tgmOpenMediaManager', '.tgm-open-media', function(e){
        // Prevent the default action from occuring.
      //  e.preventDefault();

        // If the frame already exists, re-open it.
        if ( tgm_media_frame ) {
            tgm_media_frame.open();
            return;
        }

        /**
         * The media frame doesn't exist let, so let's create it with some options.
         *
         * This options list is not exhaustive, so I encourage you to view the
         * wp-includes/js/media-views.js file to see some of the other default
         * options that can be utilized when creating your own custom media workflow.
         */
        tgm_media_frame = wp.media.frames.tgm_media_frame = wp.media({
            /**
             * We can pass in a custom class name to our frame, so we do
             * it here to provide some extra context for styling our
             * media workflow. This helps us to prevent overwriting styles
             * for other media workflows.
             */
            className: 'media-frame tgm-media-frame',

            /**
             * When creating a new media workflow, we are given two types
             * of frame workflows to chose from: 'select' or 'post'.
             *
             * The 'select' workflow is the default workflow, mainly beneficial
             * for uses outside of a post or post type experience where a post ID
             * is crucial.
             *
             * The 'post' workflow is tailored to screens where utilizing the
             * current post ID is critical.
             *
             * Since we only want to upload an image, let's go with the 'select'
             * frame option.
             */
            frame: 'select',

            /**
             * We can determine whether or not we want to allow users to be able
             * to upload multiple files at one time by setting this parameter to
             * true or false. It defaults to true, but we only want the user to
             * upload one file, so let's set it to false.
             */
            multiple: multiple,

            /**
             * We can set a custom title for our media workflow. I've localized
             * the script with the object 'tgm_nmp_media' that holds our
             * localized stuff and such. Let's populate the title with our custom
             * text.
             */
            title: title,

            /**
             * We can force what type of media to show when the user views his/her
             * library. Since we are uploading an image, let's limit the view to
             * images only.
             */
            library: {
                type: filetype
            },

            /**
             * Let's customize the button text. It defaults to 'Select', but we
             * can customize it here to give us better context.
             *
             * We can also determine whether or not the modal requires a selection
             * before the button is enabled. It requires a selection by default,
             * and since this is the experience desired, let's keep it that way.
             *
             * By default, the toolbar generated by this frame fires a generic
             * 'select' event when the button is clicked. We could declare our
             * own events here, but the default event will work just fine.
             */
            button: {
                text:  button
            }
        });

        /**
         * ========================================================================
         * EVENT BINDING
         *
         * This section before opening the modal window should be used to bind to
         * any events where we want to customize the view. This includes binding
         * to any custom events that may have been generated by us creating
         * custom controller states and views.
         *
         * The events used below are not exhaustive, so I encourage you to again
         * study the wp-includes/js/media-views.js file for a better feel of all
         * the potential events you can attach to.
         * ========================================================================
         */

        /**
         * We are now attaching to the default 'select' event and grabbing our
         * selection data. Since the button requires a selection, we know that a
         * selection will be available when the event is fired.
         *
         * All we are doing is grabbing the current state of the frame (which will
         * be 'library' since that's the only area where we can make a selection),
         * getting the selection, calling the 'first' method to pluck the first
         * object from the string and then forcing a faux JSON representation of
         * the model.
         *
         * When all is said and done, we are given absolutely everything we need to
         * insert the data into our custom input field. Specifically, our
         * media_attachment object will hold a key titled 'url' that we want to use.
         */
        tgm_media_frame.on('select', function(){
            // Grab our attachment selection and construct a JSON representation of the model.
            var media_attachment = tgm_media_frame.state().get('selection').toJSON();
			jQuery.each(media_attachment, function( key, value ){
				var file_field = 
					'<div id="file-'+value.id+'">';
				
				if (value.mime == "image/jpeg") {
					var file_field = file_field +
						'<img src="'+value.url+'" width="100">';
				}
				else {
					var file_field = file_field +
						'<img src="'+value.icon+'">' +
						'<span>'+value.filename+'</span>';
				}
				
				
				var file_field = file_field +
					'<input type="hidden" name="'+field_name+'[]" value="'+value.id+'">' +
					'<a href="javascript:remove_image(\''+value.id+'\');" class="cp-remove button">Remove</a>' +
					'</div>';
				
				jQuery('#container_'+field_id+'').append(file_field);
		
				// if multiple images are not allowed
				// disable after adding one
				if (!multiple)
					jQuery('#button_'+field_id+'').attr("disabled", "disabled");;
			});
            // Send the attachment URL to our custom input field via jQuery.
           // jQuery('#tgm-new-media-image').val(media_attachment.url);
        });

        // Now that everything has been set, let's open up the frame.
        tgm_media_frame.open();
   // });
}

function remove_image(image_id) {
	jQuery('#file-'+image_id).animate({
		opacity:0,
		height: 0
	},500,function(){
		jQuery(this).parent('.cp-files').find('.cp-open-media').removeAttr("disabled");  
		jQuery(this).remove();
	})
}

function cp_metaboxes_init($) {
   cp_metaboxes_remove($);

    $('.cp-mb-add-group').click(function(ev){
        ev.preventDefault();
        var href = $(this).attr('href');
        
        var key = 0;
        $(this).prev('.cp-mb-group-wrapper').children('fieldset').each(function(index, el) {    
            var thisKey = $(el).attr('data-key');
            if (thisKey > key) {
                key = thisKey;
            }
        });
        
        var groupId = $(this).attr('id').replace('group-', '');
        console.log(key);
        var newKey = (key*1+1*1);
        console.log(newKey);
        var button = $(this);

        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=cp_mb_add_group',
            data: 'action=cp_mb_add_group&group='+groupId+'&key='+newKey,

            success: function(response) {
                if (response.type == 'success') {
                    button.prev('.cp-mb-group-wrapper').append(response.group);
                    cp_metaboxes_remove($);
                    language_tabs($);
                    cp_sortable($);
                }
            }
        });
    });
}

function cp_metaboxes_remove($) {
    $('.cp-mb-remove-group').unbind();

     $('.cp-mb-remove-group').click(function(ev){
        ev.preventDefault();
        $(this).parent().remove();
    });
}

function cp_templates_metaboxes($) {
    $('._cp_template_').each(function(index, el) {
        $(this).parents(".postbox").hide();    
    });
   
    cp_templates_metaboxes_show($);

    $('#_cp_template').change(function(event) {
        cp_templates_metaboxes_show($);
    });
}

function cp_templates_metaboxes_show($) {
    var template = $('#_cp_template').val();
    $('._cp_template_').parents(".postbox").hide();
    if (template) {
        if ($('#_cp_template').length) {
            $('._cp_template_'+template).parents(".postbox").show();
        }
    }
}

function cp_sortable($) {
    $('.cp-mb-group-wrapper').sortable();
}
