
<script type="text/javascript" src="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/js/formee.js"></script>
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/formee-structure.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/formee-style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/table.css" type="text/css" media="screen" />


<!-- formee-->
<form class="formee" method="post" action="">
    <fieldset>
        <legend>Manage Fields for <?php echo $semantic_CPT->title; ?></legend>
        <div class="grid-2-12 "><label>Title</label></div><div class="grid-2-12 "><label>Widget Type</label></div><div class="grid-2-12 "><label>Predefined Values (<b>CSV</b>)</label></div><div class="grid-2-12 "><label>Actions</label></div>
        <hr class="clear"/>
        <?php foreach($semantic_CF as $row_CF) { ?>
		<div class="grid-2-12 "><label><?php echo $row_CF->title;?></label></div><div class="grid-2-12 "><label><?php echo $row_CF->widget;?></label></div><div class="grid-2-12 "><label><?php echo $row_CF->values;?></label></div><div class="grid-2-12 "><label><a href="#"><?php _e('Edit'); ?></a> &nbsp; <a href="#"><?php _e('Delete'); ?></a></label></div>
        <div class="clear"></div>
        <?php } ?>
        <div class="grid-2-12 ">
	    	
	  		<input id="title_field" name="title_field" type="text">
        </div>	
       <div class="grid-2-12 ">
	    	
	  		<select name="widget" id="widget">
	  			<option value="1">Input Text (no values support)</option>
	  			<option value="2">Checkbox (multiple values)</option>
	  			<option value="3">Radio (one value)</option>
	  			<option value="4">Select (one value)</option>
	  			<option value="28">Select (multiple values)</option>
	  		</select>
        </div>	
       
        
        <div class="grid-4-12 ">
        	
	  		<input id="values" class="formee-medium" name="values" type="text">
	    	
		<input type="submit" value="Add Custom Field" class="left" style="margin-left:25px;" />
		<input type="hidden" name="id_cpt" value="<?php echo $_GET['cpt']; ?>" />  
		<input type="hidden" name="semantic_post_submit" value="new_custom_field" />  
		</div>
    </fieldset>
 </form>