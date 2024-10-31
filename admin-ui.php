<?php

class PLGAdminUi {
	var $file_path;

	public function __construct( $path){
		$this->file_path = $path;
		$this->setUi();
	}

	public function setUi(){
		register_setting(PLG::OPTIONS, PLG::OPTIONS, array( &$this, 'validate' ));
		add_settings_section('plg_main_section', '表示設定', array(&$this,'section_text_fn'), $this->file_path);
		add_settings_field('plg_content_type', '表示するコンテンツ', array(&$this,'setting_content_type'), $this->file_path, 'plg_main_section');
		add_settings_field('plg_category_name', 'カテゴリーのスラッグ', array(&$this,'setting_category_name'), $this->file_path, 'plg_main_section');
		add_settings_field('plg_orderby', '表示順序', array(&$this,'setting_orderby'), $this->file_path, 'plg_main_section');
		add_settings_field('plg_number', '表示件数', array(&$this,'setting_number'), $this->file_path, 'plg_main_section');
		add_settings_field('plg_dateformat', '日付のフォーマット', array(&$this,'setting_dateformat'), $this->file_path, 'plg_main_section');
		add_settings_field('plg_window', '記事を別ウィンドウで開く', array(&$this,'setting_window_open'), $this->file_path, 'plg_main_section');
	}

	public function show_admin_page() {
		$file = $this->file_path;
		$option_name = PLG::OPTIONS;
		include_once('admin-view.php');
	}

	function validate($input) {
		$input['category_name'] = trim( esc_html( $input['category_name'] ));
		return $input;
	}

	function  section_text_fn() {
	}

	function  setting_number() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$items = array("15", "30", "50");
		echo "<select id='posts_number' name='{$option_name}[numberposts]'>";
		foreach($items as $item) {
			$selected = ($options['numberposts'] == $item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}

	function setting_content_type() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$items = array("投稿", "固定ページ", "投稿＋固定ページ");
		foreach($items as $item) {
			$checked = ($options['content_type']==$item) ? ' checked="checked" ' : '';
			echo "<label><input {$checked} value='$item' name='{$option_name}[content_type]' type='radio' /> $item</label><br />";
		}
	}

	function setting_category_name() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$value = $options["category_name"];
		echo "<input id='plg_category_name' name='{$option_name}[category_name]' size='40' type='text' value='{$value}' />";
	}

	function setting_orderby() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$items = array("公開日順", "更新日順", "タイトル順");
		foreach($items as $item) {
			$checked = ($options['orderby']==$item) ? ' checked="checked" ' : '';
			echo "<label><input {$checked} value='$item' name='{$option_name}[orderby]' type='radio' /> $item</label><br />";
		}
	}
	function setting_dateformat() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$items = array("Y年n月j日", "Y-m-d", "Y/m/d", "j/n/Y", "n/j/Y");
		echo "<select id='plg_dateformat' name='{$option_name}[plg_dateformat]'>";
		foreach($items as $item) {
			$selected = ($options['plg_dateformat']==$item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}
	function setting_window_open() {
		$options = PLG::get_option();
		$option_name = PLG::OPTIONS;
		$checked = (isset($options['window_open']) && $options['window_open']) ? $checked = ' checked="checked" ': "";
		echo "<input id='plg_window_open' name='{$option_name}[window_open]' type='checkbox' {$checked} />";
	}
}
