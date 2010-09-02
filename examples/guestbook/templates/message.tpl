<?xml version="1.0" ?>
<opt:extend file="layout.tpl" xmlns:opt="http://xml.invenzzia.org/opt">
<opt:snippet name="content">
	<h2>Message</h2>
	
	<p class="message">{$message}</p>

	<ul class="buttons">
		<li><a href="parse:$redirect">Back to the guestbook</a></li>
	</ul>
</opt:snippet>
</opt:extend>
