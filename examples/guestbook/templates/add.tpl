<?xml version="1.0" ?>
<opt:extend file="layout.tpl" xmlns:opt="http://xml.invenzzia.org/opt">
<opt:snippet name="content">
	<form action="parse:$form.action" method="post">
	<fieldset>
		<legend>New entry</legend>

		<opt:input name="author" template="fieldLayout">
			<opt:set name="str:title" value="str:Your name" />
		</opt:input>

		<opt:input name="email" template="fieldLayout">
			<opt:set name="str:title" value="str:Your e-mail" />
			<opt:set name="str:description" value="str:Will not be published" />
		</opt:input>

		<opt:input name="website" template="fieldLayout">
			<opt:set name="str:title" value="str:Website" />
			<opt:set name="str:description" value="str:Begin with http://" />
		</opt:input>

		<opt:textarea name="body" template="fieldLayout">
			<opt:set name="str:title" value="str:Message" />
		</opt:textarea>

		<div><input type="submit" class="inputSubmit" value="Send!" /></div>
	</fieldset>
	</form>

	<ul class="buttons">
		<li><a href="index.php?action=list">Back</a></li>
	</ul>
</opt:snippet>
</opt:extend>
