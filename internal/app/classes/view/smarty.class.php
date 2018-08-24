<?php

class View_Smarty extends View {
	
	protected $_engine;
	
	public function __construct($template = null)
	{
		global $config;
		
		$this->_engine = new Smarty();
		
		$this->_engine->template_dir = $config->view_dir;
		$this->_engine->config_dir   = $config->app_path('smarty_configs');
		$this->_engine->compile_dir  = $config->internal_path('smarty_templates_c');
		$this->_engine->cache_dir    = $config->internal_path('smarty_cache');
		$this->_engine->plugins_dir = array(
			SMARTY_DIR . '/plugins',            // just under SMARTY_DIR
			$config->app_path('smarty_plugins') // my pulgin
		);
		
		// for debug
		if ($config->user['env'] != 'production') {
			$this->_engine->error_reporting = E_ALL;
			$this->_engine->force_compile = true;
		}
		
		// change delimiter
		$this->_engine->left_delimiter = '{{';
		$this->_engine->right_delimiter = '}}';
		
		// register prefilter
		$this->_engine->registerFilter( "pre", array('View_Smarty', '_uniform_charcode') );
		
		// add conf
		$this->_engine->configLoad('riina-k.tv.conf');
		
		if ($template) {
			$this->_template = $template;
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
	
	public function assign($param)
	{
		$this->_engine->assign($param);
	} // function assign()
	
	protected function _acceptable_template()
	{
		if ( !$this->_template ) {
			throw new Exception('template is not assigned.');
		} else {
			// var_dump( $this->_engine->template_exists($this->_template) );exit;
			$exists = false;
			foreach ($this->_engine->template_dir as $dir) {
				$path = $dir . $this->_template;
				if ( is_file($path) ) {
					$exists = true;
					break;
				}
			}
			if ( !$exists ) {
				throw new Exception('template "' . $this->_template . '" is not readable file.');
			}
		}
		return true;
	} // function _acceptable_template()
	
	public function render()
	{
		if ( $this->_acceptable_template() ) {
			$this->_engine->assign($this->_prop);
			$this->_engine->display($this->_template);
		}
	} // function render()
	
	public function fetch()
	{
		if ( $this->_acceptable_template() ) {
			$this->_engine->assign($this->_prop);
			return $this->_engine->fetch($this->_template);
		}
	} // function fetch()
	
} // class View
