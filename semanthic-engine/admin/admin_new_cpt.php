
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/admin/js/formee.js"></script>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/formee-structure.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/formee-style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/table.css" type="text/css" media="screen" />


<!-- formee-->
<form class="formee" method="post" action="">
    <fieldset>
        <legend>New Custom Post Type</legend>
        <div class="grid-4-12 ">
	    	<label for="title_field"><?php _e('Custom post type title'); ?> <em class="formee-req">*</em> (eg: products)</label> 
	  		<input id="title_field" name="title_field" type="text">
        </div>	
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label for="title_field_sing"><?php _e('Custom post type title singular'); ?> <em class="formee-req">*</em> (eg: product)</label> 
	  		<input id="title_field_sing" name="title_field_sing" type="text">
        </div>	
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label for="slug"><?php _e('Custom post type slug'); ?> (eg: yourdomain/<b>products</b>/*  default: plural title)</label> 
	  		<input id="slug" name="slug" type="text">
        </div>	
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label><?php _e('Use url front base'); ?> (eg: yourdomain/<em>blog</em>/<b>products</b>/ where <em>blog</em> apply to whole site)</label> 
	  		<ul class="formee-list">
	  			<li><input id="with_frontbase" name="with_frontbase" type="radio" value="1"><label for="with_frontbase">Yes<label></li>
	  			<li><input id="without_frontbase" name="with_frontbase" type="radio" value="0"><label for="without_frontbase">No<label></li>
	  		</ul>
        </div>	
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label><?php _e('Exclude from search'); ?> (won't appear in public search. default: no)</label> 
	  		<ul class="formee-list">
	  			<li><input id="exclude_from_search" name="exclude_from_search" type="radio" value="1"><label for="exclude_from_search">Yes<label></li>
	  			<li><input id="no_exclude_from_search" name="exclude_from_search" type="radio" value="0"><label for="no_exclude_from_search">No<label></li>
	  		</ul>
        </div>
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label for="admin_position"><?php _e('Position in admin menu'); ?> (default: below Comments)</label> 
	  		<select name="admin_position" id="admin_position">
	  			<option value="6.<?php echo time(); ?>">Below Posts</option>
	  			<option value="11.<?php echo time(); ?>">Below Media</option>
	  			<option value="16.<?php echo time(); ?>">Below Links</option>
	  			<option value="22.<?php echo time(); ?>">Below Pages</option>
	  			<option value="28.<?php echo time(); ?>">Below Comments</option>
	  		</select>
        </div>
        <div class="clear"></div>
        <div class="grid-4-12 ">
	    	<label><?php _e('Show in admin toolbar'); ?> (default: yes)</label> 
	  		<ul class="formee-list">
	  			<li><input id="show_in_toolbar" name="show_in_toolbar" type="radio" value="1"><label for="show_in_toolbar">Yes<label></li>
	  			<li><input id="no_show_in_toolbar" name="show_in_toolbar" type="radio" value="0"><label for="no_show_in_toolbar">No<label></li>
	  		</ul>
        </div>
        <div class="clear"></div>
		<input type="submit" value="Save Custom Type" class="left" style="margin-left:25px;" />
		<input type="hidden" name="semanthic_new_custom_type" value="true" />       
    </fieldset>
 </form>