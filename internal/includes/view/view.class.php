<?php

define( 'SMARTY_DIR', realpath($config->internal_dir.'/smarty/') . '/' );
require_once(SMARTY_DIR . 'Smarty.class.php');

class View {
	
	protected $engine;
	protected $template;
	
	public function __construct($template = null)
	{
		global $config;
		
		$this->engine = new Smarty();
		
		$this->engine->template_dir = $config->internal_dir . '/smarty_templates/';
		$this->engine->compile_dir  = $config->internal_dir . '/smarty_templates_c/';
		$this->engine->config_dir   = $config->internal_dir . '/smarty_configs/';
		$this->engine->cache_dir    = $config->internal_dir . '/smarty_cache/';
		$this->engine->plugins_dir = array(
			SMARTY_DIR . '/plugins',                   // SMARTY_DIR 直下（デフォルト）
			$config->internal_dir . '/smarty_plugins/' // 独自プラグイン用
		);
		
		// デバッグ用設定
		if ($config->user['env'] != 'production') {
			$this->engine->error_reporting = E_ALL;
			$this->engine->force_compile = true;
		}
		
		// デリミタ変更
		$this->engine->left_delimiter = '{{';
		$this->engine->right_delimiter = '}}';
		
		// フィルタ登録
		$this->engine->registerFilter( "pre", array('View', '_uniform_charcode') );
		
		// confファイル登録
		$this->engine->configLoad('riina-k.tv.conf');
		
		if ($template) {
			$this->template = $template;
		}
	} // function __construct()
	
	static function _uniform_charcode($source, $smarty)
	{
		// UTF-8 BOM clear
		$source = str_replace("\xef\xbb\xbf", '', $source);
		
		// uniform EOL
		$source = str_replace( "\r\n", "\n", $source );
		$source = str_replace( "\r", "\n", $source );
		
		return $source;
	} // function _uniform_charcode()
	
	public function set_template($template)
	{
		$this->template = $template;
	} // function set_template()
	
	public function assign($name, $param)
	{
		$this->engine->assign($name, $param);
	} // function assign()
	
	public function assignByRef($name, $param)
	{
		$this->engine->assignByRef($name, $param);
	} // function assignByRef()
	
	public function render()
	{
		if ($this->template) {
			$this->engine->display($this->template);
		} else {
			throw new Exception('template is not assigned.');
		}
	} // function render()
	
	public function fetch()
	{
		if ($this->template) {
			return $this->engine->fetch($this->template);
		} else {
			throw new Exception('template is not assigned.');
		}
	} // function fetch()
	
} // class View
