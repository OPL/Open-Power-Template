<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
	<opt:snippet name="content">
		<h2>Message</h2>
		<p>{$message}</p>

		<p><a parse:href="$redirect">OK</a></p>
	</opt:snippet>
</opt:extend>