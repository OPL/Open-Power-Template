<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:extend file="test_inherit_3.tpl">
	<!-- Test for the infinite recursion detection -->
	<opt:snippet name="header">
		<p>Recursion start</p>
		<opt:insert snippet="header"/>
		<p>Recursion end</p>
	</opt:snippet>
</opt:extend>