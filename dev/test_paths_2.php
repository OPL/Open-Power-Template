<?php
	require('./init.php');
	
	class Opt_Test_Stream
	{
		private $read = 0;
		private $text = "<p>This is an internal template</p>";
	
		public function stream_open($path, $mode, $options, $opened_path)
		{		
			return true;
		} // end stream_open();
	
		public function stream_close()
		{		
		
		} // end stream_close();
		
		public function stream_read($count)
		{		
			$return = substr($this->text, $this->read, $count);
			$this->read += $count;
			return $return;
		} // end stream_read();
		
		public function stream_write($data)
		{		
		
		} // end stream_write();
		
		public function stream_eof()
		{		
			return (sizeof($this->text) < $this->read);
		} // end stream_eof();
		
		public function stream_tell()
		{		
		
		} // end stream_tell();
		
		public function stream_seek($offset, $whence)
		{		
		
		} // end stream_seek();
		
		public function stream_stat()
		{		
			return array('foo' => 'bar');
		} // end stream_stat();
		
		public function url_stat($path, $flags)
		{		
			return array('foo' => 'bar');
		} // end url_stat();
	} // end Opt_Test_Stream;
	
	stream_register_wrapper('opttest', 'Opt_Test_Stream');

	try
	{
		$tpl = new Opt_Class;
		$tpl->sourceDir = array(
			'file' => './templates/',
			'opttest' => 'opttest://'
		);
		$tpl->compileDir = './templates_c';
		$tpl->stripWhitespaces = false;
		$tpl->htmlAttributes = true;
		$tpl->prologRequired = false;
		$tpl->compileMode = Opt_Class::CM_REBUILD;
		$tpl->errorReporting = E_ALL | E_NOTICE;
		$tpl->setup();
		
		$out = new Opt_Output_Http;
		$out->render(new Opt_View('opttest:template'));
	}
	catch(Opt_Exception $e)
	{
		Opt_Error_Handler($e);
	}
?>