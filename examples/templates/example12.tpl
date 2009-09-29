<html> 
<head> 
  <title>Example 12</title> 
</head> 
<body>
<h1>Example 12</h1>
<h3>Component rotation</h3>
<p>Here we use different components to create a dynamic HTML form without conditional instruction. All is done by
PHP-defined components feature demonstrated in example 8.</p>
<hr/>
<form method="post" action="example12.php">
<b>Your name:</b>
{component id="$name"}
	{onmessage message="msg" position="down"}
		<br/><font color="blue">{@msg}</font>
	{/onmessage}
{/component}
<br/>
{formActionsComponent datasource="$actions"}{/formActionsComponent}
</body>
</html>
