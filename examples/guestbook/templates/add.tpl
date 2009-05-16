<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
	<opt:snippet name="content">

		<h2>Add new entry</h2>
		<p><a href="index.php?action=list">Back</a></p>

		<form parse:action="$form.action" method="post">

		<opt:input name="author" template="fieldLayout">
			<opt:set str:name="title" str:value="Your name" />
		</opt:input>

		<opt:input name="email" template="fieldLayout">
			<opt:set str:name="title" str:value="Your mail" />
			<opt:set str:name="description" str:value="Will not be published" />
		</opt:input>

		<opt:input name="website" template="fieldLayout">
			<opt:set str:name="title" str:value="Website" />
		</opt:input>

		<opt:textarea name="body" template="fieldLayout">
			<opt:set str:name="title" str:value="Message" />
		</opt:textarea>

		<div><input type="submit" value="Send!" /></div>
		</form>

		<p><a href="index.php?action=list">Back</a></p>
	</opt:snippet>
</opt:extend>