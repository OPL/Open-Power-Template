<html> 
<head> 
  <title>Example 3</title> 
</head> 
<body>
<h1>Example 3</h1>
<h3>Advanced lists</h3>
<p>Two-level lists are more complicated, when it comes to the PHP code. Inside the template, the code is clear, as usual.</p>
<hr/>

<ul>
{section=categories}
<li><i>{$categories.name}</i><br/>
<table width="60%" border="1">
 <tr>
  <td width="30"><b>#</b></td>
  <td width="20%"><b>Name</b></td>
  <td width="*"><b>Description</b></td> 
 </tr>
 {section=products}
 <tr>
  <td width="30">{$products.id}</td>
  <td width="20%">{$products.name}</td>
  <td width="*">{$products.description}</td> 
 </tr>
 {/section}
</table>
</li>
{/section}
</ul>
</body> 
</html>
