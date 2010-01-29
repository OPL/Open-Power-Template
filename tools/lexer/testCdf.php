<?php
require('./cdf_parser.php');
require('./cdf_lexer.php');

if(sizeof($argv) != 2)
{
	die('Please specify the test file name.');
}
try
{
	$lexer = new Opt_Cdf_Lexer($argv[1]);
	$parser = new Opt_Cdf_Parser();
	while($lexer->yylex())
	{
		if($lexer->token != 'w')
		{
			$parser->doParse($lexer->token, $lexer->value);
		}
	}
	$parser->doParse(0, 0);
	echo "OK\n";
}
catch(Exception $e)
{
	die('Exception: '.$e->getMessage()."\n");
}