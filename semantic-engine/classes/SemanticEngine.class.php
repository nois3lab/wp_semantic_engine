<?php
/** autoloader init. Basically, it calls classes stored in 'classes/' folder when there is not an istance yet. **/
function semantic_engine_autoloader($className) {
	//it's weird, but apparently wordpress include class that he does not provide himself. So, add a strpos on SemanticEngine. 
	if(strpos($className, 'SemanticEngine') === false)
		return;

	include $className.'.class.php';
}
spl_autoload_register('semantic_engine_autoloader');

class SemanticEngine {
	const SEMANTIC_DB_VERSION = 0.01;

	public function init() {
		$semantic_model = new SemanticEngineModel;
		if(get_option('semantic_engine_db_version') != self::SEMANTIC_DB_VERSION):
			$semantic_model->install();
		endif;
		$this->cpt_queryall = $semantic_model->getAllCpt();
		$this->cptSetup();

	}

	private function cptSetup() {
		foreach($this->cpt_queryall as $row):
			register_post_type( 'semantic_'.$row->ID,
				array(
					'labels' => array(
						'name'          => $row->title,
						'singular_name' => $row->title_sing
					),
					'public'               => true,
					'has_archive'          => true,
					'exclude_from_search'  => $row->exclude_from_search,
					'show_ui'              => true, 
					'show_in_menu'         => true, 
					'show_in_admin_bar'    => $row->show_in_toolbar,
					'menu_position'        => $row->position,
					'supports'             => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
					'rewrite'              => array('slug' => $row->slug),
					'register_meta_box_cb' => 'semantic_engine_meta_box_cb',
					'taxonomies'           => array('category', 'post_tag')
				)
			);
		endforeach;
	}
}

?>