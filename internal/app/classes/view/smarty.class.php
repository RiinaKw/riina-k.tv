<?php

class View_Smarty extends View {

	protected $_engine;

	public function __construct($template = null)
	{
		global $bootstrap;

		$this->_engine = new Smarty();

		$this->_engine->template_dir = $bootstrap->view_dir;
		$this->_engine->config_dir   = $bootstrap->app_config_path('conf');
		$this->_engine->compile_dir  = $bootstrap->internal_path('smarty_templates_c');
		$this->_engine->cache_dir    = $bootstrap->internal_path('smarty_cache');
		$this->_engine->plugins_dir = array(
			SMARTY_DIR . '/plugins',            // just under SMARTY_DIR
			$bootstrap->app_path('smarty_plugins') // my pulgin
		);

		// for debug
		if ($bootstrap->env != 'production') {
			$this->_engine->error_reporting = E_ALL;
			$this->_engine->force_compile = true;
		}

		// change delimiter
		$this->_engine->left_delimiter = '{{';
		$this->_engine->right_delimiter = '}}';

		// register prefilter
		$this->_engine->registerFilter(
			'pre',
			array(__CLASS__, '_uniform_charcode')
		);

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

	protected function _require_template()
	{
		if ( !$this->_template ) {
			throw new Exception('template is not assigned.');
		} else {
			$exists = false;
			foreach ($this->_engine->template_dir as $dir) {
				$path = $dir . $this->_template;
				if ( is_file($path) ) {
					$exists = true;
					break;
				}
			}
			if ( !$exists ) {
				throw new Exception(
					'template "' . $this->_template . '" is not readable file.'
				);
			}
		}
		return true;
	} // function _require_template()

	protected function before()
	{
		global $bootstrap;

		$this->_engine->assign('root', $bootstrap->root_url);
		$this->_engine->assign('path', $_SERVER['REQUEST_URI']);
	} // function before()

	public function render()
	{
		$this->_require_template();
		$this->before();
		$this->_engine->assign($this->_prop);
		$this->_engine->display($this->_template);
	} // function render()

	public function fetch()
	{
		$this->_require_template();
		$this->before();
		$this->_engine->assign($this->_prop);
		return $this->_engine->fetch($this->_template);
	} // function fetch()

} // class View_Smarty
