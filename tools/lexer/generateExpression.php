<?php
// Create Parser
passthru('php ./ParserGenerator/cli.php expression_parser.y');

echo "\n\nPARSER COMPLETED\n\n";

// Create Lexer
require_once './LexerGenerator.php';
$lex = new PHP_LexerGenerator('./expression_lexer.plex');