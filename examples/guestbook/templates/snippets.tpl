<?xml version="1.0" ?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
	<opt:snippet name="fieldLayout">
	<div opt:component-attributes="default">
		<opt:on-event name="error">
		<p class="error">{$errorMessage}</p>
		</opt:on-event>
		
		<label for="parse:'form_'~$system.component.name~'_id'">
			{$system.component.title}
		<opt:on-event name="isRequired">
			<strong>*</strong>
		</opt:on-event>
		</label>
		

		<opt:display />
		<p opt:if="$system.component.description" class="desc">{$system.component.description}</p>
	</div>
	</opt:snippet>
</opt:root>
