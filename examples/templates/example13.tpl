<html> 
<head> 
  <title>Example 13</title> 
</head> 
<body>
<h1>Example 13</h1>
<h3>Lists with show control</h3>
<p>You may combine {literal}{section}{/literal} with {literal}{show}{/literal} instruction to gain control over the checking, whether the section is displayed
or not. This is useful, if you don't want to show a table, if there are no data.</p>
<hr/>

<ul>
{section=categories}
<li><i>{$categories.name}</i>
{show=products}<br/>
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
{showelse}
(No results)
{/show}
</li>
{/section}
</ul>
</body> 
</html>
