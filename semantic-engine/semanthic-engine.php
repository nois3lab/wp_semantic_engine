<?php
/*
Plugin Name: Semantic Engine
Plugin URI: http://wordpress.org/
Description: Semantic engine is the most powerful engine Plugin for wordpress. Enables custom type, custom fields and microdata to be automatically supported; everything can be managed trough the admin UI; is not necessary to write your own code but if you are a brave dev, we have a full documented set of APIs for you.
Author: Marco Antonutti @ Nois3lab.it
Author URI: http://nois3lab.it/
Version: 0.1alfa
*/
/** execute main query only once and then retrieve n times with global $semantic_CPT **/
$semantic_CPT = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."semantic_engine_CPT");
/**add action area**/
add_action('admin_menu', 'semantic_engine_admin_menu');
add_action('admin_init', 'semantic_engine_admin_init');
add_action('save_post', 'semantic_engine_save_postdata');
add_action('init', 'semantic_engine_init');
add_action('pre_get_posts', 'semantic_pre_get_posts');
/** path define **/
define('SEMANTIC_ENGINE_PATH', plugin_dir_path(__FILE__));
define('SEMANTIC_ENGINE_URL',  plugin_dir_url(__FILE__));
/** END of setup zone **/

function semantic_engine_admin_menu() {
	add_menu_page('Semantic Engine Dashboard', 'Semantic Engine', 'manage_options', 'semantic-engine-dashboard', 'semantic_engine_admin_dashboard_build', SEMANTIC_ENGINE_URL.'/icons/icon_menu.png', 61);
	add_submenu_page('semantic-engine-dashboard', 'Add new content type', 'Add Content Type', 'manage_options', 'semantic-engine-new-content-type', 'semantic_engine_add_content_type');
	add_submenu_page('semantic-engine-dashboard', 'Add new view mode', 'Add View Mode', 'manage_options', 'semantic-engine-new-view-mode', 'semantic_engine_add_view_mode');

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

function semantic_engine_admin_init() {
	//post submit new CPT
	if(isset($_POST['semantic_post_submit'])) {
		switch ($_POST['semantic_post_submit']) {
			case 'new_custom_type':
				semantic_engine_insert_new_CPT();
				break;
			case 'new_custom_field':
				semantic_engine_insert_new_CF();
				break;
			default:
				# code...
				break;
		}
		
	}
}

function semantic_engine_sql_install() {

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$sql = "CREATE TABLE `".$wpdb->prefix."semantic_engine_CPT` (
			  ID int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(20) NOT NULL,
			  title_sing varchar(20) NOT NULL,
			  slug varchar(20) NOT NULL,
			  front_base tinyint(1) NOT NULL,
			  exclude_from_search tinyint(1) NOT NULL,
			  position varchar(20) NOT NULL,
			  show_in_toolbar tinyint(1) NOT NULL,
			  active tinyint(1) NOT NULL,
			  PRIMARY KEY  (ID)
			) AUTO_INCREMENT=1 ;";
	$sql.= "CREATE TABLE ".$wpdb->prefix."semantic_engine_CF (
		  ID int(11) NOT NULL AUTO_INCREMENT,
		  id_cpt int(11) NOT NULL,
		  title varchar(40) NOT NULL,
		  widget tinyint NOT NULL,
		  csv_values text NOT NULL,
		  multiple_entries tinyint(1) NOT NULL,
		  PRIMARY KEY  (ID),
		  KEY id_cpt (id_cpt)
		) AUTO_INCREMENT=1 ;";

	dbDelta($sql);
	update_option("semantic_engine_db_version", "0.1");

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

function semantic_engine_insert_new_CF() {
	$title = $_POST['title_field'];
	$widget = $_POST['widget'];
	$values = $_POST['values'];
	$multiple_entries = $_POST['multiple_entries'];
	$id_cpt = $_POST['id_cpt'];

	global $wpdb;
	$table_name = $wpdb->prefix . "semantic_engine_CF";
	$rows_affected = $wpdb->insert( $table_name, array( 'id' => null, 'title' => $title, 'widget' => $widget, 'csv_values' => $values, 'multiple_entries' => $multiple_entries, 'id_cpt' => $id_cpt) );

	wp_redirect('admin.php?page=semantic-engine-dashboard&cpt='.$id_cpt.'&action=custom_fields');
}

function semantic_engine_init() {

	//sql db install and updates
	$semantic_engine_db_version = "0.1";
	if(get_option('semantic_engine_db_version') != $semantic_engine_db_version)
		semantic_engine_sql_install();

	semantic_engine_CPT_setup();

}
function semantic_engine_meta_box_cb($post_obj) {
	add_meta_box('id'.$post_obj->post_type, translate('Custom fields'), 'semantic_engine_build_metabox', $post_obj->post_type, 'normal', 'high'); 
}
function semantic_engine_build_metabox($post) {
	global $wpdb;
	$cpt_id = str_replace('semantic_', '', $post->post_type);
	$table_name = $wpdb->prefix . "semantic_engine_CF";
	$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE id_cpt = '$cpt_id'");
	foreach($semantic_CF as $cf_row):
		echo '<div style="margin-top: 8px;">';
		semantic_admin_html_widget($cf_row->title, $cf_row->csv_values, $cf_row->widget, $post->ID, $cf_row->ID,$cf_row->multiple_entries);
		echo'<input type="hidden" name="semantic_engine_noncename" id="semantic_engine_noncename_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		echo '</div>';
	endforeach;
}
function semantic_admin_html_widget($label, $values, $widget, $post, $cf_id, $allow_multiple) {
	switch($widget) {
		case 1:
		#input text
			if(!$allow_multiple) {
				$val = get_post_meta($post, strtolower($label).$cf_id, true);
				echo '<label for="id_'.$label.'"> '.$label.'</label> <input type="text" name="'.strtolower($label).'" id="id_'.$label.'" value="'.$val.'" />';
			} else {
				$val = get_post_meta($post, strtolower($label).$cf_id, true);
				echo '<label for="id_'.$label.'"> '.$label.'</label> <input type="text" name="'.strtolower($label).'[]" id="id_'.$label.'" value="'.$val.'" />';
				echo '<br/><a style="cursor:pointer;" onClick="addNewEntry(\''.strtolower($label).'\');">Add new entry for '.$label.'</a>';
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
function semantic_engine_save_postdata($post_id) {
	global $post;
	if(!isset($_POST['semantic_engine_noncename']))
		return;

	/**security check **/
	if ( !wp_verify_nonce($_POST['semantic_engine_noncename'], plugin_basename(__FILE__) ))
		return $post_id;
	elseif (!current_user_can( 'edit_page', $post_id))
		return $post_id;
	elseif (!current_user_can( 'edit_post', $post_id))
		return $post_id;
	/**end security check**/
	global $wpdb;
	$cpt_id = str_replace('semantic_', '', $_POST['post_type']);
	$table_name = $wpdb->prefix . "semantic_engine_CF";
	$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE id_cpt = '$cpt_id'");
	foreach($semantic_CF as $cf_row):

		$data = $_POST[strtolower($cf_row->title)];
		if($cf_row->widget == 2 || $cf_row->widget == 5)
			$data = implode(',',$data);

		$field_name = strtolower($cf_row->title).$cf_row->ID;
		if(get_post_meta($post_id, $field_name) == "")
			add_post_meta($post_id, $field_name, $data, true);
		elseif($data != get_post_meta($post_id, $field_name, true))
			update_post_meta($post_id, $field_name, $data);
		elseif($data == "")
			delete_post_meta($post_id, $field_name, get_post_meta($post_id, $field_name, true));

	endforeach;

}

function semantic_pre_get_posts( $query ) {
    if ( $query->is_main_query() && ! $query->get( 'post_type' ) )
        $query->set( 'post_type', array('post', 'page', 'semantic_1') );
}

function semantic_engine_CPT_setup() {
	global $semantic_CPT;
	foreach($semantic_CPT as $semantic_row_CPT) {
		register_post_type( 'semantic_'.$semantic_row_CPT->ID,
		array(
			'labels' => array(
				'name' => $semantic_row_CPT->title,
				'singular_name' => $semantic_row_CPT->title_sing
			),
			'public' => true,
			'has_archive' => true,
			'exclude_from_search' => $semantic_row_CPT->exclude_from_search,
			'show_ui' => true, 
    		'show_in_menu' => true, 
    		'show_in_admin_bar' => $semantic_row_CPT->show_in_toolbar,
    		'menu_position' => $semantic_row_CPT->position,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
			'rewrite' => array('slug' => $semantic_row_CPT->slug),
			'register_meta_box_cb' => 'semantic_engine_meta_box_cb',
			'taxonomies' => array('category', 'post_tag')
		)
	);
	}
}