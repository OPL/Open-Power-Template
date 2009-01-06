<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Functions 1</title>
 </head>
 <body>
  <h1>Test: Functions 1</h1>
  <p>This file tests whether various functions work.</p>
  
  <h3>replace()</h3>
  
  <p>{replace($fooText, 'foo', 'bar')}</p>
  
  <h3>contains()</h3>
  
  <p opt:if="contains($array, 'foo')">The array contains foo</p>
  <p opt:if="contains($array, 'joe')">The array contains joe</p>
  
  <h3>range()</h3>
  
  <p>Copyright &copy; {range(2005)} Foo Corporation</p>
  
  <h3>isUrl()</h3>
  
  <p><opt:if test="isUrl($url1)"><em>{$url1}</em> is a valid URL<opt:else><em>{$url1}</em> is not a valid URL</opt:else></opt:if></p>
  <p><opt:if test="isUrl($url2)"><em>{$url2}</em> is a valid URL<opt:else><em>{$url2}</em> is not a valid URL</opt:else></opt:if></p>

  <h3>isImage()</h3>
  <p><opt:if test="isImage($img1)"><em>{$img1}</em> is a valid image link<opt:else><em>{$img1}</em> is not a valid image link</opt:else></opt:if></p>
  <p><opt:if test="isImage($img2)"><em>{$img2}</em> is a valid image link<opt:else><em>{$img2}</em> is not a valid image link</opt:else></opt:if></p>

  <h3>Aggregate functions: average(), sum(), stddev()</h3>
  
  <p>The numbers:</p>
  <ul>
  <opt:section name="numbers">
   <li>{$numbers}</li>
  </opt:section>
  </ul>
  
  <p>Sum: {sum($numbers)}</p>
  <p>Average: {average($numbers)}</p>
  <p>Standard deviation: {stddev($numbers)}</p>

  <h3>Aggregate money()</h3>
  {@asMoney is money($numbers, '%i')}
  
  <ul>
  <opt:section name="currency" datasource="@asMoney">
   <li>{$currency}</li>
  </opt:section>
  </ul>
  
  <h3>Aggregate spacify()</h3>
  <p>Also with format changes. The original is objective.</p>
  {@spacified is spacify($texts, '-')}
  
  <p>{@spacified.foo}</p>
  <p>{@spacified.bar}</p>
  <p>{@spacified.joe}</p>
 </body>
</html>
