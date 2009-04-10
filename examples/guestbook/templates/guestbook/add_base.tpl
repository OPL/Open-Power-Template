<?xml version="1.0" ?>
<opt:root include="snippets.tpl">
	<form method="post">
		<opt:insert snippet="formHeader">
			<p>Fill in this form in order to add your note.</p>
		</opt:insert>

		<gb:input name="title" template="fieldLayout">
			<opt:set str:name="title" str:value="Title" />
			<opt:set str:name="description" str:value="Name your note!" />
		</gb:input>

		<gb:input name="author" template="fieldLayout">
			<opt:set str:name="title" str:value="Author" />
			<opt:set str:name="description" str:value="Your name" />
		</gb:input>

		<gb:input name="website" template="fieldLayout">
			<opt:set str:name="title" str:value="Website" />
			<opt:set str:name="description" str:value="A link to your website." />
		</gb:input>

		<gb:input name="email" template="fieldLayout">
			<opt:set str:name="title" str:value="E-mail" />
			<opt:set str:name="description" str:value="Will no be displayed." />
		</gb:input>

		<gb:textarea name="content" rows="6" cols="50" template="fieldLayout">
			<opt:set str:name="title" str:value="The content" />
		</gb:textarea>

		<opt:insert snippet="formFooter" />
	</form>
</opt:root>