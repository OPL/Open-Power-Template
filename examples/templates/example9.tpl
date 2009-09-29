<html> 
<head> 
  <title>Example 9</title> 
</head> 
<body>
<h1>Example 9</h1>
<h3>Output caching</h3>
<p>Output caching is the perfect solution to improve server's performance. It stores the generated HTML output 
(not only a precompiled template) for a specified peroid of time, so that we don't have to load the data from
the database every time. This occurs only if the cache file is too old and has to be regenerated.</p>
<p>Some parts of the document may be still dynamic. In this example the first date changes every page reload
(because it is dynamic) and the second one changes every 30 seconds. Why? Because it is cached.</p>
<hr/>
{dynamic}
<p>Dynamic date: {$current_date}</p>
{/dynamic}
<p>Static (cached) date: {$current_date}</p>
</body> 
</html>
