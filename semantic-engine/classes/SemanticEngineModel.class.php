<?php
/** sql insert, select, update and tables schemas **/
class SemanticEngineModel extends SemanticEngine {
	public function install() {
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
		update_option("semantic_engine_db_version", "0.01");

	}
	public function getAllCpt() {
		global $wpdb;
		$query = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix ."semantic_engine_CPT ORDER BY ID");
		return $query;
	}
}