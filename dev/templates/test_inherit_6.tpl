<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:extend file="test_inherited_a.tpl" secondary="test_inherited_b.tpl">
	<opt:snippet name="header">
		<h1>Webmaster Of Puppets</h1>
		<p>Branch testing.</p>
	</opt:snippet>

	<opt:snippet name="content">
		<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus ut tellus id nulla adipiscing eleifend. Sed dictum accumsan ante. Nullam at nisl vitae elit aliquet fringilla. Praesent egestas eros eget tellus. Praesent id odio a sapien rhoncus vehicula. Nunc fringilla, diam eget euismod tempor, tortor metus tincidunt sapien, eu cursus magna tellus at risus. Praesent non tellus eget magna facilisis pulvinar. Praesent libero mi, adipiscing a, pharetra eget, condimentum sodales, mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Donec ac elit. Duis iaculis tortor a metus. Aliquam id purus et eros faucibus fringilla. Praesent quis quam. In lectus urna, fringilla sit amet, iaculis eget, aliquet ac, quam. Donec vulputate dui sit amet lectus. Aenean tempor, orci at pretium ornare, tortor tortor venenatis ligula, eget blandit nisi risus eget dolor. Duis nunc neque, sodales porta, viverra non, tristique eu, sem. Curabitur magna neque, blandit ullamcorper, congue quis, tristique ut, felis.</p>
		<opt:insert snippet="subcontent"/>
		
		<p>You are working with the following branch: {$branch}</p>
	</opt:snippet>

	<opt:snippet name="footer">
		<p>Bye!!!</p>
	</opt:snippet>
</opt:extend>