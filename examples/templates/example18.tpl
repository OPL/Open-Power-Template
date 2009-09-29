<html> 
<head> 
  <title>Example 18</title> 
</head> 
<body>
<h1>Example 18</h1>
<h3>Tree rendering</h3>
<p>Since OPT 1.1.0, it is easy to generate a tree data, such as below:</p>
<hr/>

<ol>
{tree=mytree}
	{leaf}
	<li>{$mytree.title}</li>
	{/leaf}
	{opening}
	<li>{$mytree.title}<ol>
	{/opening}
	{closing}
	</ol></li>
	{/closing}
{treeelse}
	<li>No tree provided.</li>
{/tree}
</ol>

</body>
</html>