<?php

	require('./common.php');

	try
	{
		toolsetInit();

		$view -> setTitle('Configurator');
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// Validate the settings
			if(!toolsetValidPath($_POST['source']))
			{
				$view -> message('The source directory is unreachable.');
			}
			if(!toolsetValidPath($_POST['destination']))
			{
				$view -> message('The destination directory is unreachable or you do not have write permissions there.');
			}
			if($_POST['source'] == $_POST['destination'])
			{
				$view -> message('The specified paths must not be the same.');
			}

			// Save the settings
			$config -> sourceDir = $_POST['source'];
			$config -> destDir = $_POST['destination'];
			foreach($availableDirectives as $id => $values)
			{
				if(isset($_POST['f'][$id]))
				{
					$config -> enable($id, 1);
				}
				else
				{
					$config -> enable($id, 0);
				}
			}

			// Run the preprocessor
			$projectFiles = array(0 =>
				'opt.class.php',
				'opt.compiler.php',
				'opt.core.php',
				'opt.instructions.php',
				'opt.functions.php',
				'opt.error.php',
				'opt.api.php'
			);

			$tpl -> assign('results', toolsetPreprocessor($projectFiles, $config->sourceDir, $config->destDir, $_POST['f']));
			$view -> setTemplate('configurator_results.tpl');
		}
		else
		{
			$view -> setTemplate('configurator.tpl');
			$result = array();
			foreach($availableDirectives as $name => $values)
			{
				$result[] = array(
					'id' => $name,
					'title' => $values['title'],
					'checked' => (is_null($config->enabled($name)) ? 'checked="checked"' :
						($config->enabled($name) ? 'checked="checked"' : ''))					
					);	
			}
			$tpl -> assign('srcValue', $config -> sourceDir);
			$tpl -> assign('destValue', $config -> destDir);
			$tpl -> assign('features', $result);
		}	
		$view -> display();
	}
	catch(optException $exception)
	{
		toolsetException($exception);
	}
?>
