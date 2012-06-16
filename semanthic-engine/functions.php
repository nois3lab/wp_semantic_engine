<?php

/**add action area**/
add_action('admin_menu', 'semanthic_engine_admin_menu');
add_action('admin_init', 'semanthic_engine_admin_init');
add_action('init', 'semanthic_engine_init');


function semanthic_engine_admin_menu() {
	add_menu_page('Semanthic Engine Dashboard', 'Semanthic Engine', 'manage_options', 'semanthich-engine-dashboard', 'semanthich_engine_admin_dashboard_build',  get_bloginfo('template_directory').'/icons/icon_menu.png', 61.2323232);
	add_submenu_page('semanthich-engine-dashboard', 'Add new content type', 'Add Content Type', 'manage_options', 'semanthic-engine-new-content-type', 'semanthic_engine_add_content_type');
	add_submenu_page('semanthich-engine-dashboard', 'Add new view mode', 'Add View Mode', 'manage_options', 'semanthic-engine-new-view-mode', 'semanthic_engine_add_view_mode');

}

function semanthich_engine_admin_dashboard_build() {
	global $wpdb;
	$table_name = $wpdb->prefix . "semanthic_engine_CPT";
	$semanthic_CPT = $wpdb->get_results( "SELECT ID, title, active FROM $table_name" );
	include_once(get_template_directory().'/admin/admin_dashboard.php');
}
function semanthic_engine_add_content_type() {
	//new custom post type
	include_once(get_template_directory().'/admin/admin_new_cpt.php');
}

function semanthic_engine_admin_init() {
	//sql db install and updates
	$semanthic_engine_db_version = "1.0";
	if(get_option('semanthic_engine_db_version') != $semanthic_engine_db_version)
		semanthic_engine_sql_install();

	//post submit new CPT
	if(isset($_POST['semanthic_new_custom_type']))
		semanthic_engine_insert_new_CPT();
}

function semanthic_engine_sql_install() {

	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."semanthic_engine_CPT` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(20) NOT NULL,
			  `title_sing` varchar(20) NOT NULL,
			  `slug` varchar(20) NOT NULL,
			  `front_base` tinyint(1) NOT NULL,
			  `exclude_from_search` tinyint(1) NOT NULL,
			  `position` varchar(20) NOT NULL,
			  `show_in_toolbar` tinyint(1) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  PRIMARY KEY (`ID`)
			) AUTO_INCREMENT=1 ;";


	dbDelta($sql);
	add_option("semanthic_engine_db_version", "1.0");

}

function semanthic_engine_insert_new_CPT() {
	$title = $_POST['title_field'];
	$title_sing = $_POST['title_field_sing'];
	$slug = $_POST['slug'];
	$front_base = $_POST['with_frontbase'];
	$exclude_from_search = $_POST['exclude_from_search'];
	$admin_position = $_POST['admin_position'];
	$show_in_toolbar = $_POST['show_in_toolbar'];
	$active = 0;

	global $wpdb;
	$table_name = $wpdb->prefix . "semanthic_engine_CPT";
	$rows_affected = $wpdb->insert( $table_name, array( 'id' => null, 'title' => $title, 'title_sing' => $title_sing, 'slug' => $slug, 'front_base' => $front_base, 'exclude_from_search' => $exclude_from_search, 'position' => $admin_position, 'show_in_toolbar' => $show_in_toolbar, 'active' => $active) );

	wp_redirect('admin.php?page=semanthich-engine-dashboard');
}

function semanthic_engine_init() {
		global $wpdb;
	$table_name = $wpdb->prefix . "semanthic_engine_CPT";
	$semanthic_CPTi = $wpdb->get_results("SELECT * FROM $table_name")or die(semanthic_engine_sql_install());
	foreach($semanthic_CPTi as $semanthic_row_CPT) {
		register_post_type( 'semanthic_'.$semanthic_row_CPT->ID,
		array(
			'labels' => array(
				'name' => $semanthic_row_CPT->title,
				'singular_name' => $semanthic_row_CPT->title_sing
			),
			'public' => true,
			'has_archive' => true,
			'exclude_from_search' => $semanthic_row_CPT->exclude_from_search,
			'show_ui' => true, 
    		'show_in_menu' => true, 
    		'show_in_admin_bar' => $semanthic_row_CPT->show_in_toolbar,
    		'menu_position' => $semanthic_row_CPT->position,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
			'rewrite' => array('slug' => $semanthic_row_CPT->slug)
		)
	);
	}

}
