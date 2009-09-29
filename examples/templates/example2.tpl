<html> 
<head> 
  <title>Example 2</title> 
</head> 
<body>
<h1>Example 2</h1>
<h3>Simple lists</h3>
<p>Here we see, how to generate simple, one-level lists.</p>
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
