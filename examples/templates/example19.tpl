<html> 
<head> 
  <title>Example 19</title> 
</head> 
<body>
<h1>Example 19</h1>
<h3>Pagesystem support</h3>
<p>OPT has a built-in pagination support. You just have to implement the <em>ioptPagesystem</em>
interface and pass the object to the parser. A suitable template instruction will do the
rest:</p>
<hr/>

{show=list}
<ol>
{section}
<li>{$list.item}</li>
{/section}
</ol>
{/show}

<p>Page {$ps->active()} of {$ps->count()}</p>

{pagesystem=ps}
{page}[ <a href="{@url}">{@title}</a> ]{/page}
{active}&lt; <strong><a href="{@url}">{@title}</a></strong> &gt;{/active}
{separator}...{/separator}
{prev}<a href="{@url}">prev</a> :: {/prev}
{next} :: <a href="{@url}">next</a>{/next}
{first}<a href="{@url}">first</a> :: {/first}
{last} :: <a href="{@url}">last</a>{/last}
{/pagesystem}

</body>
</html>