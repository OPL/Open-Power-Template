<?php


	function toolsetInit()
	{
		global $tpl, $view, $config;

		$tpl = new optClass;
		$tpl -> root = DIR_TPL;
		$tpl -> compile = DIR_TPLC;
		$tpl -> charset = 'utf-8';
		$tpl -> xmlsyntaxMode = true;
		$tpl -> httpHeaders(OPT_XHTML);

		$view = new view;
		$config = new config;
	} // end toolsetInit();

	function toolsetValidPath(&$path, $writeable = false)
	{
		$ok = true;
		if(!is_readable($path))
		{
			$ok = false;
		}
		if($writeable && $ok)
		{
			if(!is_writeable($path))
			{
				$ok = false;
			}
		}

		if($path[strlen($path)-1] != '/')
		{
			$path .= '/';
		}

		return $ok;
	} // end toolsetValidPath();

	function toolsetException($exception)
	{
		optErrorHandler($exception);
	} // end toolsetException();

	function toolsetPreprocessor($projectFiles, $srcDir, $destDir, $opts)
	{
		global $availableDirectives;
		$result = array();
		foreach($projectFiles as $file)
		{
			$src = @file($srcDir.$file);
			if($src === false)
			{
				$result[] = array(
					'file' => $file,
					'result' => 0
					);
				continue;
			}
			$cutting = 0;
			$nesting = 0;
			foreach($src as $i => $line)
			{
				if(preg_match('/# (\/?)([A-Z_0-9]+)/', trim($line), $found))
				{
					if(isset($availableDirectives[$found[2]]) && !isset($opts[$found[2]]))
					{
						if($found[1] == '/')
						{
							if($nesting == 1)
							{
								$cutting = 0;
								$nesting--;
							}
							else
							{
								$nesting--;
							}
						}
						else
						{
							$cutting = 1;
							$nesting++;
						}
						unset($src[$i]);
					}
				}
				if($cutting == 1)
				{
					unset($src[$i]);
				}
			}
			file_put_contents($destDir.$file, implode('', $src));
			$result[] = array(
				'file' => $file,
				'result' => 1
				);
		}
		return $result;
	} // end toolsetPreprocessor();

?>
