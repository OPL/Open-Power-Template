<?php
	$availableDirectives = array(
		'PLUGIN_AUTOLOAD' => array(
			'title' => 'Autoloading plugins support',
			'description' => 'Open Power Template may automatically load and install new functions, instructions
			and filters placed in the "/plugins" directory while running the engine. If you are not loading
			new features in this way, you may disable this option.'	
		),
		'CUSTOM_RESOURCES' => array(
			'title' => 'Custom resources',
			'description' => 'Resources allow you to load templates from different sources, such as databases. If
			you are using just the default resource: "file" (templates in files), you may disable this option.'	
		),
		'DEBUG_CONSOLE' => array(
			'title' => 'Debug console',
			'description' => 'When in debug mode, the console shows much useful information about parsed templates,
			configuration etc. However, if you have already developed the script and you want to upload it into a
			webserver, you may remove the console from the code, because now it will be unnecessary.'	
		),
		'GZIP_SUPPORT' => array(
			'title' => 'GZip compression support',
			'description' => 'GZip compression is the way to speed up the transfer of you website. All the modern
				browsers support it. Requires PHP Zlib extension enabled.'
		),
		'OUTPUT_CACHING' => array(
			'title' => 'Output caching',
			'description' => 'Output caching is a big part of OPT source code and allows to cache the output generated
				by the templates.'
		),
		'COMPONENTS' => array(
			'title' => 'Component support',
			'description' => 'Components allow you to build dynamic forms using OPT without touching IF\'s and
			other programming constructs.'
		),
		'PREDEFINED_COMPONENTS' => array(
			'title' => 'Predefined components',
			'description' => 'Predefined components are just a sample components that may work with OPT. Unsually it
			is good to remove them.'
		),
		'REGISTER_FAMILY' => array(
			'title' => 'registerXXX() methods',
			'description' => 'The methods whose names begin with "register" allow to extend OPT with new features
			manually. However, you may use plugins instead.'
		),
		'OBJECT_I18N' => array(
			'title' => 'Objective I18n',
			'description' => 'If you do not use the object-oriented i18n or the i18n at all, you may remove this
			feature. The option causes also the ioptI18n interface.'
		),
		'HTTP_HEADERS' => array(
			'title' => 'httpHeaders() methods',
			'description' => 'httpHeaders() method is used to send headers to the browser about the content type
			and used encoding. If your web application does it on its own, you may remove it.'
		),
		'DYNAMIC_SECTIONS' => array(
			'title' => 'Dynamic sections',
			'description' => 'Dynamic sections are a special kind of section which does not require preloading
			the data before template parsing. The option affects mostly the compilation process.'
		),
		'MASTER_TEMPLATES' => array(
			'title' => 'Master templates',
			'description' => 'Master templates provide extra information and code snippets for the compiler. They
			are loaded only if there is something to compile.'
		),
		'PAGESYSTEM' => array(
			'title' => 'Pagination support',
			'description' => 'OPT supports the pagination engines by providing the instruction {pagesystem} and
			the ioptPagesystem interface. Untick this option to remove them.'
		),
	);
	
	$projectFiles = array(
		'opt.class.php', 'opt.compiler.php', 'opt.instructions.php', 'opt.functions.php', 'opt.error.php',
		'opt.core.php', 'opt.api.php', 'opt.components.php'
	);
?>
