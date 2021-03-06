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
include_once('classes/SemanticEngine.class.php');
$SemanticEngine = new SemanticEngine;
/**add action area**/
add_action('admin_init', 'semantic_engine_admin_init');
add_action('init', array(&$SemanticEngine, 'init'));
add_action('pre_get_posts', 'semantic_pre_get_posts');
add_action('save_post', 'semantic_engine_save_postdata');
add_action('admin_menu', 'semantic_engine_admin_menu');
/** add filter area **/
add_filter('the_content', 'semantic_engine_view_content', 1);
/** path define **/
define('SEMANTIC_ENGINE_PATH', plugin_dir_path(__FILE__));
define('SEMANTIC_ENGINE_URL',  plugin_dir_url(__FILE__));
/** END of setup zone **/
function semantic_engine_init() {
	//sql db install and updates
	$semantic_engine_db_version = "0.1";
	if(get_option('semantic_engine_db_version') != $semantic_engine_db_version)
		semantic_engine_sql_install();

	semantic_engine_CPT_setup();

}

function semantic_engine_admin_init() {
		global $wpdb, $semantic_CPT;
	include(SEMANTIC_ENGINE_PATH.'admin-function.php');
	//post submit new CPT
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-button');
	wp_enqueue_script('jquery-ui-datepicker');
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
	wp_enqueue_script('semantic-image', SEMANTIC_ENGINE_URL.'js/custom.js');
}

function semantic_engine_admin_menu() {
	add_menu_page('Semantic Engine Dashboard', 'Semantic Engine', 'manage_options', 'semantic-engine-dashboard', 'semantic_engine_admin_dashboard_build', SEMANTIC_ENGINE_URL.'/icons/icon_menu.png', 61);
	add_submenu_page('semantic-engine-dashboard', 'Add new content type', 'Add Content Type', 'manage_options', 'semantic-engine-new-content-type', 'semantic_engine_add_content_type');
	add_submenu_page('semantic-engine-dashboard', 'Add new view mode', 'Add View Mode', 'manage_options', 'semantic-engine-new-view-mode', 'semantic_engine_add_view_mode');

}


function semantic_engine_meta_box_cb($post_obj) {
	add_meta_box('id'.$post_obj->post_type, translate('Custom fields'), 'semantic_engine_build_metabox', $post_obj->post_type, 'normal', 'high'); 
}

function semantic_engine_build_metabox($post) {
	global $wpdb;
	$cpt_id      = str_replace('semantic_', '', $post->post_type);
	$table_name  = $wpdb->prefix . "semantic_engine_CF";
	$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE id_cpt = '$cpt_id'");
	foreach($semantic_CF as $cf_row):
		/**JavaScript print for multiple entries support*/ ?>
		<script type="text/javascript">
		function addNewEntry(labelInput) {
			var cosa = jQuery("input#id_"+labelInput).filter(':last').after('<br/><label for="id_'+labelInput+'"> '+labelInput+'</label> <input type="text" name="'+labelInput.toLowerCase()+'[]" id="id_'+labelInput+'" value="" />');
		}
		</script>
	<?php
		echo '<div style="margin-top: 8px;">';
		semantic_admin_html_widget($cf_row->title, $cf_row->csv_values, $cf_row->widget, $post->ID, $cf_row->ID,$cf_row->multiple_entries);
		echo'<input type="hidden" name="semantic_engine_noncename" id="semantic_engine_noncename_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		echo '</div>';
	endforeach;
}

function semantic_pre_get_posts( $query ) {
    if ( $query->is_main_query() && ! $query->get( 'post_type' ) )
        $query->set( 'post_type', array('post', 'semantic_1', 'semantic_2', 'semantic_3') );
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
	$cpt_id      = str_replace('semantic_', '', $_POST['post_type']);
	$table_name  = $wpdb->prefix . "semantic_engine_CF";
	$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE id_cpt = '$cpt_id'");
	foreach($semantic_CF as $cf_row):

		$data = $_POST[sanitize_title($cf_row->title)];
		if($cf_row->widget == 2 || $cf_row->widget == 5)
			$data = implode(',',$data);

		if($cf_row->multiple_entries == 1)
			$data = serialize($data);

		$field_name = sanitize_title($cf_row->title).$cf_row->ID;
		if(get_post_meta($post_id, $field_name) == "")
			add_post_meta($post_id, $field_name, $data, true);
		elseif($data != get_post_meta($post_id, $field_name, true))
			update_post_meta($post_id, $field_name, $data);
		elseif($data == "")
			delete_post_meta($post_id, $field_name, get_post_meta($post_id, $field_name, true));

	endforeach;

}

function semantic_engine_view_content($content) {
	global $post, $wpdb;
	if(strpos("semantic_", $post->post_type) == null)
		return $content;

	$cpt_id      = str_replace('semantic_', '', $post->post_type);
		$table_name  = $wpdb->prefix . "semantic_engine_CF";
		$semantic_CF = $wpdb->get_results( "SELECT * FROM $table_name WHERE slug = '$post->post_type'");
		foreach($semantic_CF as $cf_row):
			$field_name = sanitize_title($cf_row->title).$cf_row->ID;
			$val        = get_post_meta($post->ID, $field_name, true);
			if(!empty($val)):
				$vl = semantic_engine_get_meta_value_by_type($val, $cf_row->widget, $cf_row->multiple_entries);
			endif;
		endforeach;
	return $content;
}

function semantic_engine_get_meta_value_by_type($meta, $type, $allow_multiple = null) {
	switch ($type) {
		case 1:
			if(!$allow_multiple)
				return $meta;
			else
				return unserialize($meta);
			break;
		case 2:
				return explode(",", $meta);
			break;
		case 3:
				return $meta;
			break;
		case 4:
				return $meta;
			break;
		case 5:
				return explode(",", $meta);
			break;
			case 6:
				return $meta;
			break;
		default:
			return $meta;
			break;
	}
}


