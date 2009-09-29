<html> 
<head> 
  <title>Example 4</title> 
</head> 
<body>
<h1>Example 4</h1>
<h3>Default i18n support</h3>
<p>This example shows, how to use the default i18n system. In the template, we do not write any text. All of them are defined
in the PHP code and dynamically loaded from decicated array by OPT. The places where they should be located are marked by 
<i>language blocks</i>.</p>
<hr/>
{* put current date inside the global@date language block *}
{apply($global@date, $current_date)}
<p>{$global@text1}</p>
<p>{$global@text2}</p>
<p>{$global@text3}</p>
<p>{$global@date}</p>
</body> 
</html>
