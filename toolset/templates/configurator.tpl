<h3>Configurator</h3>
<p><strong>Step 1</strong>: Select the source and destination directory. They have to be different.</p>
<form method="post" action="configurator.php">
<p>Source directory: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="source" value="{$srcValue}"/></p>
<p>Destination directory: <input type="text" name="destination" value="{$destValue}"/></p>
<p><strong>Step 2</strong>: Select the new OPT feature configuration. Tick the features you want to keep and untick those ones to remove.</p>

{section=features}
<input type="checkbox" name="f[{$features.id}]" {$features.checked} value="1"/> {$features.title}<br/>
{/section}
<p><input type="submit" value="Generate OPT"/></p>
</form>
