<script type="text/javascript" src="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/js/formee.js"></script>
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/formee-structure.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/formee-style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo SEMANTIC_ENGINE_URL; ?>/admin/css/table.css" type="text/css" media="screen" />

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
				<?php foreach($semantic_CPT as $semantic_row) { ?>		
					<tr>
						<td><?php echo $semantic_row->ID; ?></td>
						<td><?php echo $semantic_row->title; ?></td>
						<td><?php if($semantic_row->active == 1) echo 'Yes'; else echo 'No';?></td>
						<td><a href="admin.php?page=semantic-engine-dashboard&cpt=<?php echo $semantic_row->ID; ?>&action=custom_fields"><?php _e('Manage Custom Fields'); ?></a> &nbsp; <a href="#"><?php _e('Edit'); ?></a> &nbsp; <a href="#"><?php _e('Delete'); ?></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

               <a href="admin.php?page=semantic-engine-new-content-type"><input class="left" title="<?php _e('Add new'); ?>" value="<?php _e('Add new'); ?>" type="button" style="margin-left:25px;"></a>
        
    </fieldset>
 </form>