<html> 
<head> 
  <title>Example 5</title> 
</head> 
<body>
<h1>Example 5</h1>
<h3>Object i18n support</h3>
<p>This is an object-oriented layer for the custom i18n system. It provides additional method: setObjectI18n() and
ioptI18n interface.</p>
<hr/>
{* put current date inside the global@date language block *}
{apply($global@date, $current_date)}
<p>{$global@text1}</p>
<p>{$global@text2}</p>
<p>{$global@text3}</p>
<p>{$global@date}</p>
</body> 
</html>
