<?php
    // OPL Initialization
	$config = parse_ini_file('../paths.ini', true);
    require($config['libraries']['Opl'].'Base.php');
    Opl_Loader::loadPaths($config);
	Opl_Loader::register();
    Opl_Registry::setState('opl_debug_console', true);
	Opl_Registry::setState('opl_extended_errors', true);

	function profile($dump = false)
	{
		$trace = debug_backtrace();
		array_pop($trace);
		$k = reset($trace);
		Opl_Debug::writeErr($k['function'].','.$k['file'].','.$k['line']);
	} // end profile();