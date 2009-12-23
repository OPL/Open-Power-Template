<?php
require('./expression_parser.php');
require('./expression_lexer.php');

$in = fopen('php://stdin', 'r');

do
{
	$read = trim(fread($in, 80));

	if($read != 'q')
	{
		try
		{
			$lexer = new Opt_Expression_Standard_Lexer($read);
			$parser = new Opt_Expression_Standard_Parser();
			while ($lexer->yylex())
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
			echo 'Exception: '.$e->getMessage()."\n";
		}
	}
}
while($read != 'q');