<?php
/**
 * The file generates the parser for the expression engine. It uses the
 * PHP Parser Generator present in this directory. Furthermore, it installs
 * the newly generated files in the project directory tree.
 *
 * @author Tomasz Jędrzejewski
 */
// Create Parser
passthru('php53 ./ParserGenerator/cli.php expression_parser.y');

echo "\n\nPARSER COMPLETED\n\n";

// Create Lexer
require_once './LexerGenerator.php';
$lex = new PHP_LexerGenerator('./expression_lexer.plex');

echo "\n\nLEXER COMPLETED\n\n";

$comment = <<<'EOF'
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 */

/**
 * The expression parser for OPT. Note: do not edit this file
 * manually. It was generated by PHP Parser Generator by Gregory Beaver.
 * Instead, use the file /tools/lexer/expression_parser.y and parse
 * it with /tools/lexer/generateExpression.php.
 */
EOF;

file_put_contents('../../lib/Opt/Expression/Standard/Parser.php', str_replace('<'.'?php', '<'."?php\r\n".$comment, file_get_contents('./expression_parser.php')));



file_put_contents('../../lib/Opt/Expression/Standard/Lexer.php', preg_replace('/throw new Exception\(\'Unexpected input at line\' \. (\$this\-\>_line) \.
                    \'\: \' \. (\$this\-\>_data\[\$this\-\>_counter\])\)\;/', 'throw new Opt_Expression_Exception(\'Unexpected input at line \'.\$this->_line.\': \'.\$this->_data[\$this->_counter]);', file_get_contents('expression_lexer.php')));
echo "\n\nSUCCESS\n\n";