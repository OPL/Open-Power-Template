<?xml version="1.0" ?>
<opt:extend file="layout.tpl" xmlns:opt="http://xml.invenzzia.org/opt">
<opt:snippet name="content">
	<ul class="buttons">
		<li><a href="index.php?action=add">Add your entry</a></li>
	</ul>

	{$count is count($entries) + 1}
	<opt:section name="entries">
	<opt:body>
		<div class="entry" id="parse:'e'~$entries.id">
			<p class="header"><a href="parse:'#e'~$entries.id" class="number">#{--$count}</a>
			Written by <a href="parse:$entries.website" opt:omit-tag="not $entries.website"><strong>{$entries.author}</strong></a>
			on {$entries.date}</p>

			<p>{u:nl2br($entries.body)}</p>
		</div>
	</opt:body>
	<opt:else>
		<p class="message">There are no entries in the database now. Add one!</p>
	</opt:else>
	</opt:section>

	<ul class="buttons" opt:if="count($entries) gt 0">
		<li><a href="index.php?action=add">Add your entry</a></li>
	</ul>
</opt:snippet>
</opt:extend>
