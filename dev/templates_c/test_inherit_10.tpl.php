<?php if(!$this->_massPreprocess($this->_template, $compileTime, array('test_inherited_d.tpl',))){   ?><<?php echo '?'; ?>xml version="1.0" standalone="yes" ?>

<p>Hi</p>

		<p>Foo</p>
	<?php  }else{ $compileTime = $this->_compile($this->_template, $mode); require(__FILE__); }   ?>