<?xml version="1.0" ?>
<opt:root>
	<opt:snippet name="fieldLayout">
	<com:div class="field">
		<label parse:for="$system.component.name~'_id'">{$system.component.title}</label>
		<p opt:if="$system.component.description" class="desc">{$system.component.description}</p>

		<opt:onEvent name="isRequired">
		<p class="desc">Required</p>
		</opt:onEvent>

		<opt:display />

		<opt:onEvent name="error">
		<p class="error">{$errorMessage}</p>
		</opt:onEvent>
	</com:div>
	</opt:snippet>
</opt:root>