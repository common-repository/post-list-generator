<?php
/*
 Plugin Name: Post List Generator
Plugin URI: http://residentbird.main.jp/bizplugin/
Description: 記事（投稿・固定ページの一覧を作成するプラグインです
Version: 1.3.0
Author:WordPress Biz Plugin
Author URI: http://residentbird.main.jp/bizplugin/
*/

include_once( dirname(__FILE__)."/admin-ui.php" );
new PostListGeneratorPlugin();

class PLG{
	const VERSION = "1.3.0";
	const SHORTCODE = "showpostlist";
	const OPTIONS = "post_list_options";

	public static function get_option(){
		return get_option(self::OPTIONS);
	}

	public static function update_option( $options ){
		if ( empty($options)){
			return;
		}
		update_option(self::OPTIONS, $options);
	}

	public static function enqueue_css_js(){
		wp_enqueue_style('post-list-style', plugins_url('post-list-generator.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_script('post-list-js', plugins_url('next-page.js', __FILE__ ), array('jquery'), self::VERSION );
	}

	public static function localize_js(){
		wp_localize_script( 'post-list-js', 'PLG_Setting', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'action' => 'get_post_ajax',
				"plg_dateformat" => "Y年n月j日",
				'next_page' => '1',
		));
	}
}

/**
 * プラグイン本体
 */
class PostListGeneratorPlugin{

	var $adminUi;

	public function __construct(){
		register_activation_hook(__FILE__, array(&$this,'on_activation'));
		add_action( 'admin_init', array(&$this,'on_admin_init') );
		add_action( 'admin_menu', array(&$this, 'on_admin_menu'));
		add_action( 'wp_enqueue_scripts', array(&$this,'on_enqueue_sctipts') );
		add_action( 'wp_ajax_get_post_ajax', array(&$this,'get_post_ajax') );
		add_action( 'wp_ajax_nopriv_get_post_ajax', array(&$this,'get_post_ajax') );
		add_shortcode( PLG::SHORTCODE, array(&$this,'show_shortcode'));
	}

	function on_activation() {
		$option = PLG::get_option();
		if( $option ) {
			return;
		}
		$arr = array(
				"content_type" => "投稿",
				"orderby" => "公開日順",
				"category_name" => "",
				"numberposts" => "30",
				'window_open' => 'false',
		);
		PLG::update_option( $arr );
	}

	function on_enqueue_sctipts() {
		if ( is_admin() ) {
			return;
		}
		PLG::enqueue_css_js();
		PLG::localize_js();
	}

	function on_admin_init() {
		$this->adminUi = new PLGAdminUi(__FILE__);
	}

	public function on_admin_menu() {
		add_options_page("Post List設定", "Post List設定", 'administrator', __FILE__, array(&$this->adminUi, 'show_admin_page'));
	}

	/**
	 * shortcode
	 */
	function show_shortcode(){
		$info = new PostListInfo();
		ob_start();
		include( dirname(__FILE__).'/post-list-view.php');
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	/**
	 * Ajax
	 */
	function get_post_ajax(){
		$page = absint( $_REQUEST['page'] );
		if ( $page == 0){
			die();
		}
		$info = new PostListInfo( $page );
		$info->next_page = $info->has_next ? $page + 1: null;
		$json = json_encode( $info );
		$charset = get_bloginfo( 'charset' );
		nocache_headers();
		header( "Content-Type: application/json; charset=$charset" );
		echo $json;
		die();
	}
}


class PostListInfo{
	var $items = array();
	var $has_next = false;
	var $window_open = false;

	public function __construct( $page = 0){
		$options = PLG::get_option();
		$this->window_open = isset( $options['window_open'] ) ? $options['window_open'] : false;

		$condition = array();
		if ( $options['content_type'] == '投稿'){
			$condition['post_type'] = 'post';
		}else if ( $options['content_type'] == '固定ページ' ){
			$condition['post_type'] = 'page';
		}else{
			$condition['post_type'] = array('page', 'post');
		}
		if ( $options['orderby'] == '公開日順'){
			$condition['orderby'] = 'post_date';
			$condition['order'] = 'desc';
		}else if ( $options['orderby'] == '更新日順'){
			$condition['orderby'] = 'modified';
			$condition['order'] = 'desc';
		}else{
			$condition['orderby'] = 'title';
			$condition['order'] = 'asc';
		}
		$condition['numberposts'] = $options['numberposts'] + 1;
		$condition['offset'] = $page * $options['numberposts'];
		$condition['category_name'] = $options['category_name'];

		$posts = get_posts( $condition );

		if ( !is_array($posts) ){
			return;
		}

		if ( count($posts) > $options['numberposts']){
			$this->has_next = true;
			array_pop ( $posts );
		}

		foreach($posts as $post){
			$this->items[] = new PostListItem($post, $options);
		}
	}
}

class PostListItem{
	var $date;
	var $title;
	var $url;

	public function __construct( $post, $options ){
		$raw_date = $options['orderby'] == '公開日順' ? $post->post_date : $post->post_modified;
		$dateformat = empty($options['plg_dateformat']) ? "Y年n月j日" : $options['plg_dateformat'];
		$this->date = date($dateformat, strtotime($raw_date));
		$this->title = esc_html( $post->post_title );
		$this->url = get_permalink($post->ID);
	}
}

?>