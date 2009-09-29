<html> 
<head> 
  <title>Example 11</title> 
</head> 
<body>
<h1>Example 11</h1>
<h3>XML-Syntax mode</h3>
<p>This example demonstrates working with XML-Syntax mode. In this mode OPT is allowed to parse also XML-like tags, so it may
be used in XML document without any problems. Notice OPT does not use any XML parser; it's only an emulation. This means it will parse
also invalid XML content and doesn't generate an error.</p>
<p>In XML-Syntax mode, OPT supports some entities in the instruction parameters. Two last ones are also supported outside the OPT tags:</p>
<ul>
  <li>&amp;amp; - &amp;</li>
  <li>&amp;quot; - &quot;</li>
  <li>&amp;apos; - '</li>
  <li>&amp;lt; - &lt;</li>
  <li>&amp;gt; - &gt;</li>
  <li>&amp;lb; - &lb;</li>
  <li>&amp;rb; - &rb;</li>
</ul>
<hr/>

<table width="60%" opt:put="$border">
 <tr>
  <td width="30"><b>#</b></td>
  <td width="20%"><b>Name</b></td>
  <td width="*"><b>Description</b></td> 
 </tr>
 <opt:section name="products">
 <tr>
  <td width="30">{$products.id}</td>
  <td width="20%">{$products.name}</td>
  <td width="*">{$products.description}</td>
 </tr>
 </opt:section>
</table>
</body>
</html>
