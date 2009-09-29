<?php

	require('./common.php');

	try
	{
		toolsetInit();

		if(!isset($_GET['cmd']))
		{
			$_GET['cmd'] = '';
		}

		$view -> setTitle('Compiler');
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($_GET['cmd'] == 'chdir')
			{
				if(!toolsetValidPath($_POST['spl']))
				{
					$view -> message('The source directory is unreachable.');
				}
				if(!toolsetValidPath($_POST['cpl']))
				{
					$view -> message('The compile directory is unreachable or you do not have write permissions there.');
				}
				if(trim($_POST['plg']) != '')
				{
					if(!toolsetValidPath($_POST['plg']))
					{
						$view -> message('The plugin directory is unreachable or you do not have write permissions there.');
					}
				}
				if(trim($_POST['mas']) != '')
				{
					if(!file_exists($_POST['mas']))
					{
						$view -> message('The specified master template is unreachable or you do not have read permissions there.');
					}
				}
				if($_POST['spl'] == $_POST['cpl'])
				{
					$view -> message('The specified paths must not be the same.');
				}
				if($_POST['xml'] != 1 && $_POST['xml'] != 0)
				{
					$view -> message('Invalid XML Syntax Mode state.');
					
				}								
				$config -> splDir = $_POST['spl'];
				$config -> cplDir = $_POST['cpl'];
				$config -> plgDir = trim($_POST['plg']);
				$config -> masDir = trim($_POST['mas']);
				$config -> xmlDir = $_POST['xml'];
				$view -> message('The directories have been changed.', 'compiler.php');
			}
			else
			{
				if(isset($_POST['rall']))
				{
					$dir = opendir($config -> cplDir);
					$templates = array();
					if(is_resource($dir))
					{
						$i = 0;
						while($file = readdir($dir))
						{
							if(is_file($config->cplDir.$file))
							{
								@unlink($config->cplDir.$file);
							}
						}
						closedir($dir);
					}
					$view -> message('The compiled templates have been removed.', 'compiler.php');
				}
				if(isset($_POST['rsel']))
				{
					if(!is_array($_POST['sel']))
					{
						$view -> message('No data provided.', 'compiler.php');
					}

					foreach($_POST['sel'] as $file)
					{
						$cplFile = optCompileFilename($file);

						if(is_file($config->cplDir.$cplFile))
						{
							unlink($config->cplDir.$cplFile);
						}
					}
					$view -> message('The selected compiled templates have been removed.', 'compiler.php');
				}
				if(isset($_POST['csel']))
				{
					if(!is_array($_POST['sel']))
					{
						$view -> message('No data provided.', 'compiler.php');
					}
					
					$parser = new optClass;
					$parser -> root = $config -> splDir;
					$parser -> compile = $config -> cplDir;
					$parser -> showWarnings = false;
					$parser -> xmlsyntaxMode = (bool)$config->xmlDir;
					if($config->plgDir != '')
					{
						$parser -> plugins = $config -> plgDir;
						$parser -> loadPlugins();
					}
					if($config->masDir != '')
					{
						$parser -> setMasterTemplate($config->masDir);
					}
					require_once(OPT_DIR.'opt.compiler.php');
					$parser -> compiler = new optCompiler($parser);
					$current = '';
					try
					{
						if($config->masDir != '')
						{
							$current = $config->masDir;
							$parser -> compiler -> parse(NULL, file_get_contents($config->masDir));
						}
						foreach($_POST['sel'] as $file)
						{

							if(is_file($config->splDir.$file))
							{
								$current = $file;
								$parser -> compiler -> parse($parser->compile.optCompileFilename($file),
									file_get_contents($parser->root.$file));
	
							}
						}
					}
					catch(optException $exception)
					{
						$view -> setTemplate('template_error.tpl');
						$view -> setTitle('Template compilation error');
						$tpl -> assign('type', $exception -> getType());
						$tpl -> assign('code', $exception -> getCode());
						$tpl -> assign('message', $exception -> getMessage());
						$tpl -> assign('file', $current);
						$view -> display();
						die();						
					}		
					$view -> message('The templates have been successfully compiled.', 'compiler.php');				
				}
			}
		}
		else
		{
			$view -> setTemplate('compiler.tpl');

			$tpl -> assign('splValue', $config -> splDir);
			$tpl -> assign('cplValue', $config -> cplDir);
			$tpl -> assign('plgValue', $config -> plgDir);
			$tpl -> assign('masValue', $config -> masDir);
			$tpl -> assign('sel'.$config->xmlDir, 'checked="checked"');
			
			$dir = opendir($config -> splDir);
			$templates = array();
			if(is_resource($dir))
			{
				$i = 0;
				while($file = readdir($dir))
				{
					if(is_file($config->splDir.$file))
					{
						$templates[$i] = array();
						$templates[$i]['filename'] = $file;
						
						$cplFile = optCompileFilename($file);
						
						$cpl = @filemtime($config->cplDir.$cplFile);
						if($cpl === false)
						{
							$templates[$i]['cdate'] = '<strong>Not compiled</strong>';
							$templates[$i]['cplSize'] = '<em>N/A</em>';						
						}
						else
						{
							$templates[$i]['cdate'] = date('m.d.Y, H:i', $cpl);							
							$templates[$i]['cplSize'] = filesize($config->cplDir.$cplFile).' b';				
						}
						$templates[$i]['srcSize'] = filesize($config->splDir.$file).' b';
						$i++;						
					}					
				}
				closedir($dir);				
			}
			
			$tpl -> assign('templates', $templates);
		}	
		$view -> display();
	}
	catch(optException $exception)
	{
		toolsetException($exception);
	}
?>
