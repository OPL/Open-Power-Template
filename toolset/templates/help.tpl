<h3>Configurator</h3>
<p>OPT configurator is a simple script, which allows you to build your own versions of Open Power Template
parser. This gives you lots of benefits: you may remove the features from the source code you are not using
or are unncessary. When you upload your application into the web, you will probably also remove all
the debug options. OPT will work faster then.</p>
<p>You must specify three things: 1/ the directory, where the original OPT code is placed; 2/ the output
directory, where will be placed the "new" OPT; 3/ the features you want to keep. Here are their descriptions:
</p>
{show=directives}
<ul>
{section}
<li><strong>{$directives.title}</strong> - {$directives.description}</li>
{/section}
</ul>
{showelse}
<p>No directives found.</p>
{/show}

<h3>Compiler</h3>
<p>When the "performance" directive is set in the OPT configuration, the library does not check, whether
the templates are modified. This means the programmer must recompile them on his own. This tool is intended
to those ones, who have already run their websites and do not want to change the settings just to make
tiny changes. They can compile the template here and send the precompiled version to the server.</p>
<p>Above the list of templates, you see some configuration options:</p>
<ol>
 <li>Source template directory - a path to the source versions of the templates.</li>
 <li>Destination template directory - where the compiled templates must be placed to.</li>
 <li>Plugin directory - a directory with external OPT plugins to load.</li>
 <li>Master template - OPT Toolset allows to pre-load one master template file. If you use master templates, write down here the filename with the full path.</li>
 <li>XML Syntax Mode - whether to compile the templates in XML Syntax Mode.</li>
</ol>

<p>In the template list, you can:</p>
<ol>
 <li>Recompile all the templates at once (for a big number of templates it may take several seconds).</li>
 <li>Recompile the selected templates</li>
 <li>Remove the compiled version</li>
</ol>

<h3>Other issues</h3>
<p>The script remembers the last used settings, so that you do not have to remember, what you set lately.
Remember that PHP must have a write access to the script directory, where the settings are stored.</p>
