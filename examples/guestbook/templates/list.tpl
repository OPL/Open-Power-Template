<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
	<opt:snippet name="content">

		<h2>The entries</h2>
		<p><a href="index.php?action=add">Add your entry!</a></p>

		<opt:section name="entries">
		<div class="entry" parse:id="'e '~$entries.id">
			<p>Written by <a parse:href="$entries.website" opt:on="$entries.website">{$entries.author}</a> on {$entries.date}</p>

			<p>{nl2br($entries.body)}</p>
		</div>
		<opt:sectionelse>
			<p>There are no entries in the database now. Add one!</p>
		</opt:sectionelse>
		</opt:section>

		<p><a href="index.php?action=add">Add your entry!</a></p>
	</opt:snippet>
</opt:extend>