<html>
 <head>
  <title>Test: Quirks mode 1</title>
 </head>
 <body>
  <h1>Test: Quirks mode 1</h1>
  <p>This file checks the quirks mode.</p>
  
  <h2>Test 1</h2>
  <p>Here, we check the instructions. They should work.</p>
  
  <ol>
  <opt:section name="s1">
  	<li>Name: {$s1.name} {$s1.surname}</li>
  </opt:section>
  </ol>
  
  <h2>Test 2</h2>
  <p>Brackets inside tags should work now.</p>
  
  <p {$style}>Wohoo!</p>
  
  <h3>Test 3</h3>
  
  <p>Invalid XML code</p>
  
  <p><strong>Wohoo!</p>
 </body>
</html>
