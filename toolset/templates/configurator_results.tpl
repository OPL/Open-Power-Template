<h3>Configurator</h3>
{@fail=0}
<ol>
{section=results}
<li>{$results.file}: <strong>{if test="$results.result"}Success{else}Failed{@fail=1}{/if}</strong></li>
{/section}
</ol>
{if test="@fail"}
<p>OPT Configurator failed to process some of the library files. Ensure that you have copied the OPT files into your srcDir directory!</p>
{/if}