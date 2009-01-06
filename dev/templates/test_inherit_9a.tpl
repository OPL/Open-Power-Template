<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:extend file="test_inherit_9b.tpl" escaping="no">
	<opt:snippet name="foo">
		<p>This should not be escaped: {$htmlContent}</p>
		<opt:parent/>
	</opt:snippet>
</opt:extend>