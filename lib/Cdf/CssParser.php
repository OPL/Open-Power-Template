<?php
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
 * $Id: Class.php 269 2009-11-27 10:59:46Z zyxist $
 */

/**
 * The generic CSS-style syntax lexer and parser. Actually, it
 * is hardly dependant on other OPT elements, so can be quite
 * easily re-used in other scripts.
 */
class Opt_Cdf_CssParser
{
	/**
	 * Registers a value type that may be accepted by the parser.
	 *
	 * @param string $id The value type unique identifier.
	 * @param string $regexp The regular expression that matches the value token.
	 */
	protected function _registerValueType($id, $regexp)
	{

	} // end _registerValueType();
	
	/**
	 * Performs the lexical analysis of the specified input code,
	 * parsing it as a syntactic CSS document. The analysis results
	 * are stored in the private class fields that can be accessed
	 * via public methods.
	 *
	 * @param string $code The code to parse 
	 */
	protected function _parse($code)
	{
		$lexer = new Opt_Cdf_GeneralCss_Lexer($code);
		$parser = new Opt_Cdf_GeneralCss_Parser();
		while ($lexer->yylex())
		{
			if($lexer->token != 'w')
			{
				$parser->doParse($lexer->token, $lexer->value);
			}
		}
		$parser->doParse(0, 0);
	} // end _parse();
} // end Opt_Cdf_CssParser;