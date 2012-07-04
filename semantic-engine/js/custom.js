jQuery(function(jQuery) {  
  
    jQuery('.custom_upload_image_button').click(function() {  
        formfield = jQuery(this).siblings('.custom_upload_image');  
        preview = jQuery(this).siblings('.custom_preview_image');  
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');  
        window.send_to_editor = function(html) {  
            imgurl = jQuery('img',html).attr('src');  
            classes = jQuery('img', html).attr('class');  
            id = classes.replace(/(.*?)wp-image-/, '');  
            formfield.val(id);  
            preview.attr('src', imgurl);  
            tb_remove();  
        }  
        return false;  
    });  
  
    jQuery('.custom_clear_image_button').click(function() {  
        var defaultImage = jQuery(this).parent().siblings('.custom_default_image').text();  
        jQuery(this).parent().siblings('.custom_upload_image').val('');  
        jQuery(this).parent().siblings('.custom_preview_image').attr('src', defaultImage);  
        return false;  
    });  
  
});  