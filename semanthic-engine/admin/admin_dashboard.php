
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/admin/js/formee.js"></script>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/formee-structure.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/formee-style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/admin/css/table.css" type="text/css" media="screen" />

<!-- formee-->
<form class="formee">
    <fieldset>
        <legend>Dashboard</legend>
        <table id="hor-minimalist-b">
			<thead>
				<tr>
						<th scope="col">#</th>
						<th scope="col"><?php _e('Content type name'); ?></th>
						<th scope="col"><?php _e('Active'); ?></th>
						<th scope="col"><?php _e('Actions'); ?></th>
				</tr>
				
			</thead>
			<tbody>
				<?php foreach($semanthic_CPT as $semanthic_row) { ?>		
					<tr>
						<td><?php echo $semanthic_row->ID; ?></td>
						<td><?php echo $semanthic_row->title; ?></td>
						<td><?php if($semanthic_row->active == 1) echo 'Yes'; else echo 'No';?></td>
						<td><a href="#"><?php _e('Edit'); ?></a> &nbsp; <a href="#"><?php _e('Delete'); ?></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

               <a href="admin.php?page=semanthic-engine-new-content-type"><input class="left" title="<?php _e('Add new'); ?>" value="<?php _e('Add new'); ?>" type="button" style="margin-left:25px;"></a>
        
    </fieldset>
 </form>