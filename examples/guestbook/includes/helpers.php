<?php

	class helpers
	{

		static public function route($paramLine)
		{
			$ctl = FrontController::getInstance();

			$data = explode('/', $paramLine);
			foreach($data as &$item)
			{
				$item = strtr('/', '_', $item);
			}


			if($ctl->config->get('website', 'niceUrls'))
			{
				return '/'.implode('/', $data);
			}

			return '/?'.http_build_query($data);
		} // end route();

	} // end helpers;