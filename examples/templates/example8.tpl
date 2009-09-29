<html> 
<head> 
  <title>Example 8</title> 
</head> 
<body>
<h1>Example 8</h1>
<h3>PHP-defined components</h3>
<p>In this example we create the component inside the PHP code. In the template, there is only a place where
we may link it using normal assign() method. This means the type of component we see is defined by the script,
not by template and we may change it without recompiling them.</p>
<p>Let's take a look at the example. There is only a place for the component. The script is programmed to put here
a select field every time. But we could write a component that generates a list and show it here without changing the
template. This is useful while developing advanced HTML forms.</p>
<hr/>
<form method="get" action="example8.php">
Select the category:
{component id="$selector"}
	{onmessage message="msg" position="down"}
		<font color="red">{@msg}</font>
	{/onmessage}
{/component}
<input type="submit" value="OK"/>
</body>
</html>
