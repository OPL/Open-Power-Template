<html> 
<head> 
  <title>Example 10</title> 
</head> 
<body>
<h1>Example 10</h1>
<h3>Advanced output caching</h3>
<p>This example demonstrates, how to use output caching with databases.</p>
<hr/>

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
</body> 
</html>
