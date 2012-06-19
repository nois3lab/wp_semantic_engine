<?php
function semantic_admin_html_widget($label, $values, $widget, $post, $cf_id, $allow_multiple) {
	switch($widget) {
		case 1:
		#input text
			if(!$allow_multiple) {
				$val = get_post_meta($post, strtolower($label).$cf_id, true);
				echo '<label for="id_'.$label.'"> '.$label.'</label> <input type="text" name="'.strtolower($label).'" id="id_'.$label.'" value="'.$val.'" />';
			} else {
				$val = get_post_meta($post, strtolower($label).$cf_id, true);
				$val = unserialize($val);
				foreach($val as $vl)
				echo '<label for="id_'.$label.'"> '.$label.'</label> <input type="text" name="'.strtolower($label).'[]" id="id_'.$label.'" value="'.$vl.'" /><br/>';
				echo '<br/><a style="cursor:pointer;" onClick="addNewEntry(\''.$label.'\');">Add new entry for '.$label.'</a>';
			} 
			break;
		case 2:
		#checkbox
			echo '<label>'.$label.'</label> &nbsp;';
				$values = explode(',', $values);
				foreach($values as $vl) {
					$vl = trim($vl);
					$data = get_post_meta($post, strtolower($label).$cf_id, true);
					$data = explode(",", $data);
					if(in_array($vl, $data))
					echo '<input type="checkbox" id="id_'.$vl.'" name="'.strtolower($label).'[]"  value="'.$vl.'" checked="true">&nbsp;<label for="id_'.$vl.'">'.$vl.'</label>&nbsp; &nbsp;';
					else
					echo '<input type="checkbox" id="id_'.$vl.'" name="'.strtolower($label).'[]"  value="'.$vl.'">&nbsp;<label for="id_'.$vl.'">'.$vl.'</label>&nbsp; &nbsp;';
				}
			break;
		case 3:
			echo '<label>'.$label.'</label> &nbsp;';
				$values = explode(',', $values);
				foreach($values as $vl) {
					$vl = trim($vl);
					if(get_post_meta($post, strtolower($label).$cf_id, true) == $vl)
					echo '<input type="radio" name="'.strtolower($label).'" id="id_'.$vl.'"  value="'.$vl.'" checked="true">&nbsp;<label for="id_'.$vl.'">'.$vl.'</label>&nbsp; &nbsp;';
					else
					echo '<input type="radio" name="'.strtolower($label).'" id="id_'.$vl.'"  value="'.$vl.'" checked="true">&nbsp;<label for="id_'.$vl.'">'.$vl.'</label>&nbsp; &nbsp;';
				}
			break;
		case 4:
		#select one value
			echo '<label for="id_'.$label.'">'.$label.'</label> &nbsp;';
			echo '<select name="'.strtolower($label).'" id="id_'.$label.'">';
				$values = explode(',', $values);
				foreach($values as $vl) {
					$vl = trim($vl);
					if(get_post_meta($post, strtolower($label).$cf_id, true) == $vl)
					echo '<option value="'.$vl.'" selected="true">'.$vl.'</option>';
					else
					echo '<option value="'.$vl.'">'.$vl.'</option>';
				}
			echo '</select>';
			break;
		case 5:
		#select multiple value
			echo '<label for="id_'.$label.'">'.$label.'</label> &nbsp;';
			echo '<select name="'.strtolower($label).'[]" id="id_'.$label.'" multiple="multiple">';
				$values = explode(',', $values);
				foreach($values as $vl) {
					$vl = trim($vl);
					$data = get_post_meta($post, strtolower($label).$cf_id, true);
					$data = explode(",", $data);
					if(in_array($vl, $data))
					echo '<option value="'.$vl.'" selected="true">'.$vl.'</option>';
					else
					echo '<option value="'.$vl.'">'.$vl.'</option>';
				}
			echo '</select>';
			break;
	}
}




function semantic_engine_admin_dashboard_build() {
	global $wpdb, $semantic_CPT;
	if(isset($_GET['cpt']) && !empty($_GET['cpt'])) {
		switch ($_GET['action']) {
			case 'custom_fields':
					$table_name = $wpdb->prefix . "semantic_engine_CPT";
					$semantic_CPT = $wpdb->get_row( "SELECT title, active FROM $table_name WHERE ID = ".$_GET['cpt']);
					$table_name = $wpdb->prefix . "semantic_engine_CF";
					$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE id_cpt = ".$_GET['cpt']);
					include_once(SEMANTIC_ENGINE_PATH.'/admin/admin_new_custom_fields.php');
				break;
			default:
				# code...
				break;
		}
	} else {
	
	include_once(SEMANTIC_ENGINE_PATH.'/admin/admin_dashboard.php');
	}
}
function semantic_engine_add_content_type() {
	//new custom post type
	include_once(SEMANTIC_ENGINE_PATH.'/admin/admin_new_cpt.php');
}

function semantic_engine_insert_new_CF() {
	$title            = $_POST['title_field'];
	$widget           = $_POST['widget'];
	$values           = $_POST['values'];
	$multiple_entries = $_POST['multiple_entries'];
	$id_cpt           = $_POST['id_cpt'];

	global $wpdb;
	$table_name = $wpdb->prefix . "semantic_engine_CF";
	$rows_affected = $wpdb->insert( $table_name, array( 'id' => null, 'title' => $title, 'widget' => $widget, 'csv_values' => $values, 'multiple_entries' => $multiple_entries, 'id_cpt' => $id_cpt) );

	wp_redirect('admin.php?page=semantic-engine-dashboard&cpt='.$id_cpt.'&action=custom_fields');
}



function semantic_engine_insert_new_CPT() {
	$title = $_POST['title_field'];
	$title_sing = $_POST['title_field_sing'];
	$slug = $_POST['slug'];
	$front_base = $_POST['with_frontbase'];
	$exclude_from_search = $_POST['exclude_from_search'];
	$admin_position = $_POST['admin_position'];
	$show_in_toolbar = $_POST['show_in_toolbar'];
	$active = 0;

	global $wpdb;
	$table_name = $wpdb->prefix . "semantic_engine_CPT";
	$rows_affected = $wpdb->insert( $table_name, array( 'id' => null, 'title' => $title, 'title_sing' => $title_sing, 'slug' => $slug, 'front_base' => $front_base, 'exclude_from_search' => $exclude_from_search, 'position' => $admin_position, 'show_in_toolbar' => $show_in_toolbar, 'active' => $active) );

	wp_redirect('admin.php?page=semantic-engine-dashboard');
}