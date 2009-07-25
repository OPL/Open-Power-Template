<?php
/*
 *  OPEN POWER LIBS <http://www.invenzzia.org>
 *  ==========================================
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 *
 * $Id: Class.php 155 2009-07-18 07:25:11Z zyxist $
 */

	class Opt_Expression_Standard implements Opt_Expression_Interface
	{
		// Opcodes
		const OP_VARIABLE = 1;
		const OP_LANGUAGE_VAR = 2;
		const OP_STRING = 4;
		const OP_NUMBER = 8;
		const OP_ARRAY = 16;
		const OP_OBJECT = 32;
		const OP_IDENTIFIER = 64;
		const OP_OPERATOR = 128;
		const OP_POST_OPERATOR = 256;
		const OP_PRE_OPERATOR = 512;
		const OP_ASSIGN = 1024;
		const OP_NULL = 2048;
		const OP_SQ_BRACKET = 4096;
		const OP_SQ_BRACKET_E = 8192;
		const OP_FUNCTION = 16384;
		const OP_METHOD = 32768;
		const OP_BRACKET = 65536;
		const OP_CLASS = 131072;
		const OP_CALL = 262144;
		const OP_FIELD = 524288;
		const OP_EXPRESSION = 1048576;
		const OP_OBJMAN = 2097152;
		const OP_BRACKET_E = 4194304;
		const OP_TU = 8388608;
		const OP_CURLY_BRACKET = 16777216;

		// Regular expressions
		private $_rBacktickString = '`[^`\\\\]*(?:\\\\.[^`\\\\]*)*`';
		private $_rSingleQuoteString = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
		private $_rHexadecimalNumber = '\-?0[xX][0-9a-fA-F]+';
		private $_rDecimalNumber = '[0-9]+\.?[0-9]*';
		private $_rLanguageVar = '\$[a-zA-Z0-9\_]+@[a-zA-Z0-9\_]+';
		private $_rVariable = '(\$|@)[a-zA-Z0-9\_\.]*';
		private $_rOperators = '\-\>|!==|===|==|!=|\=\>|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\||\%|\+\+|\-\-|\+|\-|\*|\/|\[|\]|\.|\:\:|\{|\}|\'|\"|';
		private $_rIdentifier = '[a-zA-Z\_]{1}[a-zA-Z0-9\_\.]*';
		private $_rLanguageVarExtract = '\$([a-zA-Z0-9\_]+)@([a-zA-Z0-9\_]+)';

		// Help fields
		private $_translationConversion = null;
		private $_tf = null;

		/**
		 * The compiler instance.
		 *
		 * @var Opt_Compiler_Class
		 */
		protected $_compiler;

		/**
		 * The main class instance.
		 *
		 * @var Opt_Class
		 */
		protected $_tpl;

		/**
		 * Sets the compiler instance in the expression parser.
		 *
		 * @param Opt_Compiler_Class $compiler The compiler object
		 */
		public function setCompiler(Opt_Compiler_Class $compiler)
		{
			$this->_compiler = $compiler;
			$this->_tpl = Opl_Registry::get('opt');

			$this->_tf = $this->_tpl->getTranslationInterface();
		} // end setCompiler();

		/**
		 * Parses the source expressions to the PHP code.
		 *
		 * @param String $expression The expression source
		 * @return Array
		 */
		public function parse($expr, $allowAssignment)
		{
			// cat $expr > /dev/oracle > $result > happy programmer :)
			preg_match_all('/(?:'.
		   			$this->_rSingleQuoteString.'|'.
		   			$this->_rBacktickString.'|'.
					$this->_rHexadecimalNumber.'|'.
					$this->_rDecimalNumber.'|'.
					$this->_rLanguageVar.'|'.
					$this->_rVariable.'|'.
					$this->_rOperators.'|'.
					$this->_rIdentifier.')/x', $expr, $match);

			// Skip the whitespaces and create the translation units
			$cnt = sizeof($match[0]);
			$stack = new SplStack;
			$tu = array(0 => array());
			$tuid = 0;
			$maxTuid = 0;
			$prev = '';
			$chr = chr(18);
			$assignments = array();

			/* The translation units allow to avoid recursive compilation of the
			 * expression. Each sub-expression within parentheses and that is a
			 * function call parameter, becomes a separate translation unit. The
			 * loop below scans the array of tokens, looking for translation
			 * unit separators and builds suitable arrays of tokens for each
			 * TU.
			 */
			for($i = 0; $i < $cnt; $i++)
			{
				if(ctype_space($match[0][$i]) || $match[0][$i] == '')
				{
					continue;
				}
				switch($match[0][$i])
				{
					case ',':
						if($prev == '(' || $prev == ',')
						{
							throw new Opt_Expression_Exception('OP_COMMA', $match[0][$i], $expr);
						}
						$tuid = $stack->pop();
						if(in_array($tuid, $assignments))
						{
							$tuid = $stack->pop();
						}
					case '[':
					case '(':
					case 'is':
					case '=':
						if($match[0][$i] == '=' || $match[0][$i] == 'is')
						{
							$assignments[] = $tuid;
						}
						$tu[$tuid][] = $match[0][$i];
						++$maxTuid;
						$tu[$tuid][] = $chr.$maxTuid;	// A fake token that marks the translation unit which goes here.
						$stack->push($tuid);
						$tuid = $maxTuid;
						$tu[$tuid] = array();
						break;
					case ']':
					case ')':
						// If we have a situation like (), we can remove the TU we've just created,
						// because it's empty and will confuse the expression compiler later.
						if($prev == '(')
						{
							unset($tu[$tuid]);
							--$maxTuid;
						}
						if($stack->count() > 0)
						{
							$tuid = $stack->pop();
							if(in_array($tuid, $assignments))
							{
								$tuid = $stack->pop();
							}
						}
						if($prev == '(')
						{
							array_pop($tu[$tuid]);
						}
						if($prev == ',')
						{
							throw new Opt_Expression_Exception('OP_BRACKET', $match[0][$i], $expr);
						}
						$tu[$tuid][] = $match[0][$i];
						break;
					default:
						$tu[$tuid][] = $match[0][$i];
				}
				$prev = $match[0][$i];
			}
			if(sizeof($tu[0]) == 0)
			{
				throw new Opt_EmptyExpression_Exception();
			}
			/*
			 * Now we have an array of translation units and their tokens and
			 * we can process it linearly, thus avoiding recursive calls.
			 */
			foreach($tu as $id => &$tuItem)
			{
				$tuItem = $this->_compileExpression($expr, $allowAssignment, $tuItem, $id);
			}
			$assign = $tu[0][1];
			$variable = $tu[0][2];

			/*
			 * Finally, we have to link all the subexpressions into an output
			 * expression. We use SPL stack to achieve this, because we need
			 * to store the current subexpression status while finding a new one.
			 */
			$tuid = 0;
			$i = -1;
			$cnt = sizeof($tu[0][0]);
			$stack = new SplStack;
			$prev = null;
			$expression = '';

			while(true)
			{
				$i++;
				$token = &$tu[$tuid][0][$i];

				// If we've found a translation unit, we must stop for a while the current one
				// and link the new.
				if(strlen($token) > 0 && $token[0] == $chr)
				{
					$wasAssignment = in_array($tuid, $assignments);
					$stack->push(Array($tuid, $i, $cnt));
					$tuid = (int)ltrim($token, $chr);
					$i = -1;
					$cnt = sizeof($tu[$tuid][0]);
					if($cnt == 0 && $wasAssignment)
					{
						throw new Opt_Expression_Exception('OP_NULL', '', $expr);
					}
					continue;
				}
				else
				{
					$expression .= $token;
				}

				if($i >= $cnt)
				{
					if($stack->count() == 0)
					{
						break;
					}
					// OK, current TU is ready. Check, whether there are unfinished upper-level TUs
					// on the stack
					unset($tu[$tuid]);
					list($tuid, $i, $cnt) = $stack->pop();
				}
				$prev = $token;
			}

			return array(0 => $expression, $assign, $variable);
		} // end parse();

		/**
		 * Compiles a single translation unit in the expression.
		 *
		 * @internal
		 * @param String &$expr A reference to the compiled expressions for debug purposes.
		 * @param Boolean $allowAssignment True, if the assignments are allowed in this unit.
		 * @param Array &$tokens A reference to the array of tokens for this translation unit.
		 * @param String $tu The number of the current translation unit.
		 * @return Array An array build of three items: the compiled expression, the assignment status
		 *    and the variable status (whether the expression is in fact a single variable).
		 */
		protected function _compileExpression(&$expr, $allowAssignment, Array &$tokens, $tu)
		{
			/* The method processes a single translation unit (TU). For example, in the expression
			 *		$a is ($b + $c) * $d
			 * we have the following translation units:
			 * 1. $a is #TU2 * $d
			 * 2. $b + $c
			 *
			 * They are compiled separately and automatically, so you do not have to do this on
			 * your own. This has been done to remove the recursion from the source code, and moreover
			 * it allows, for example, to manage the argument order in the functions.
			 */

			// Operator mappings
			$wordOperators = array(
				'eq' => '==',
				'eqt' => '===',
				'ne' => '!=',
				'net' => '!==',
				'neq' => '!=',
				'neqt' => '!==',
				'lt' => '<',
				'le' => '<=',
				'lte' => '<=',
				'gt' => '>',
				'ge' => '>=',
				'gte' => '>=',
				'and' => '&&',
				'or' => '||',
				'xor' => 'xor',
				'not' => '!',
				'mod' => '%',
				'div' => '/',
				'add' => '+',
				'sub' => '-',
				'mul' => '*',
				'shl' => '<<',
				'shr' => '>>'
			);

			// Previous token information
			$previous = array(
				'token' => null,
				'source' => null,
				'result' => null
			);
			// Some standard "next token sets"
			$valueSet = self::OP_VARIABLE | self::OP_LANGUAGE_VAR | self::OP_STRING | self::OP_NUMBER |
				self::OP_IDENTIFIER | self::OP_PRE_OPERATOR | self::OP_OBJMAN | self::OP_BRACKET;
			$operatorSet = self::OP_OPERATOR | self::OP_POST_OPERATOR | self::OP_NULL;
			// Initial state
			$state = array(
				'next' => $valueSet | self::OP_NULL,	// What token must occur next.
				'step' => 0,		// This flag helps processing brackets by saving some extra token information.
				'func' => 0,		// The function call type: 0 - OPT function (with "$this" as the first argument); 1 - ordinary function
				'oper' => false,	// The assignment flag. The value must be assigned to a variable, so on the left side there must not be any operator (false).
				'clone' => 0,		// We've already used "clone"
				'preop' => false,	// Prefix operators ++ and -- found. This flag is cancelled by any other operator.
				'rev' => NULL,		// Changing the argument order options
				'assign_func' => false,	// Informing the bracket parser that the first argument must be a language block, which must be processed separately.
				'tu'	=> 0,		// What has opened a translation unit? The field contains the token type.
				'variable' => NULL,	// To detect if the expression is a single variable or not.
				'function' => NULL	// Function name for the argument checker errors
			);
			$chr = chr(18);		// Which ASCII code marks the translation unit
			$result = array();	// Here we put the compilation result
			$void = false;		// This is a fake variable for a recursive call, as a last argument (reference)
			$assign = false;
			$to = sizeof($tokens);

			// Loop through the token list.
			for($i = 0; $i < $to; $i++)
			{
				// Some initializing stuff.
				$token = &$tokens[$i];
				$parsefunc = false;
				$current = array(
					'token' => null,		// Symbolic token type. Look at the file header to find the token definitions.
					'source' => $token,		// Original form of the token is also remembered.
					'result' => null,		// Here we have to put the result PHP code generated from the token.
				);
				// Find out, what it is and process it.
				switch($token)
				{
					case '[':
						// This code checks, whether the token is properly used. We have to assign it to one of the token groups.
						if(!($state['next'] & self::OP_SQ_BRACKET))
						{
							throw new Opt_Expression_Exception('OP_SQ_BRACKET', $token, $expr);
						}
						$result[] = '[';
						$state['tu'] = self::OP_SQ_BRACKET_E;
						$state['next'] = self::OP_TU;
						$state['step'] = self::OP_VARIABLE;
						continue;
					case ']':
						if(!($state['next'] & self::OP_SQ_BRACKET_E))
						{
							throw new Opt_Expression_Exception('OP_SQ_BRACKET_E', $token, $expr);
						}
						$current['token'] = $state['step'];
						$current['result'] = ']';
						$state['step'] = 0;
						// This is the way we mark, what tokens can occur next.
						$state['next'] = self::OP_OPERATOR | self::OP_NULL | self::OP_SQ_BRACKET;
						if($state['clone'] == 1)
						{
							$state['next'] = self::OP_NULL | self::OP_SQ_BRACKET;
						}
						break;
					// These tokens are invalid and must produce an error
					case '\'':
					case '"':
					case '{':
					case '}':
						throw new Opt_Expression_Exception('OP_CURLY_BRACKET', $token, $expr);
						break;
					// Text operators.
					case 'add':
					case 'sub':
					case 'mul':
					case 'div':
					case 'mod':
					case 'shl':
					case 'shr':
					case 'eq':
					case 'neq':
					case 'eqt':
					case 'neqt':
					case 'ne':
					case 'net':
					case 'lt':
					case 'le':
					case 'lte':
					case 'gt':
					case 'gte':
					case 'ge':
						// These guys can be also method names, if in proper context
						if($previous['token'] == self::OP_CALL)
						{
							$this->_compileIdentifier($token, $previous['token'], $previous['result'],
								isset($tokens[$i+1]) ? $tokens[$i+1] : null, $operatorSet, $expr, $current, $state);
							break;
						}
					case 'and':
					case 'or':
					case 'xor':
						$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);

						// And these three ones - only strings.
						if($state['next'] & self::OP_STRING)
						{
							$current['result'] = '\''.$token.'\'';
							$current['token'] = self::OP_STRING;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						else
						{
							if(!($state['next'] & self::OP_OPERATOR))
							{
								throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
							}
							$current['result'] = $wordOperators[$token];
							$current['token'] = self::OP_OPERATOR;
							$state['next'] = $valueSet;
							$state['preop'] = false;
						}
						$state['variable'] = false;
						break;
					case 'not':
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
						}
						$current['token'] = self::OP_PRE_OPERATOR;
						$current['result'] = $wordOperators[$token];
						$state['next'] = $valueSet;
						$state['variable'] = false;
						break;
					case 'new':
					case 'clone':
						// These operators are active only if the directive advancedOOP is true.
						if(!$this->_tpl->advancedOOP)
						{
							throw new Opt_ExpressionOptionDisabled_Exception($token, 'security reasons');
						}
						if(!($state['next'] & self::OP_OBJMAN))
						{
							throw new Opt_Expression_Exception('OP_OBJMAN', $token, $expr);
						}
						$current['result'] = $token.' ';
						$current['token'] = self::OP_OBJMAN;
						$state['next'] = ($token == 'new' ? self::OP_IDENTIFIER : self::OP_BLOCK);
						$state['clone'] = 1;
						$state['variable'] = false;
						break;
					case 'is':
						if($state['next'] & self::OP_STRING)
						{
							$current['result'] = '\''.$token.'\'';
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E | self::OP_TU;
							break;
						}
					case '=':
						if(!$allowAssignment)
						{
							throw new Opt_ExpressionOptionDisabled_Exception('Assignments', 'compiler requirements');
						}
						// We have to assign the data to the variable or object field.
						if(($previous['token'] == self::OP_VARIABLE || $previous['token'] == self::OP_FIELD) && !$state['oper'] && $previous['token'] != self::OP_LANGUAGE_VAR)
						{
							$current['result'] = '';
							$current['token'] = self::OP_ASSIGN;
							$state['variable'] = false;
							$state['next'] = self::OP_TU;
							$state['tu'] = self::OP_NULL;
							$assign = true;
						}
						else
						{
							throw new Opt_Expression_Exception('OP_ASSIGN', $token, $expr);
						}
						break;
					case '!==':
					case '==':
					case '===':
					case '!=':
					case '+':
					case '*':
					case '/':
					case '%':
						if(!($state['next'] & self::OP_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);

						$current['result'] = $token;
						$state['next'] = $valueSet;
						$state['oper'] = true;
						$state['preop'] = false;
						$state['variable'] = false;
						break;
					case '-':
						if($state['next'] & self::OP_OPERATOR)
						{
							$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);

							$current['result'] = $token;
							$state['oper'] = true;
							$state['next'] = $valueSet;
							$state['preop'] = false;
						}
						elseif($state['next'] & self::OP_NUMBER | self::OP_VARIABLE | self::OP_IDENTIFIER)
						{
							$current['result'] = $token;
							$state['next'] = self::OP_NUMBER | self::OP_VARIABLE | self::OP_IDENTIFIER;
						}
						else
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$state['variable'] = false;
						break;
					case '~':
						if(!($state['next'] & self::OP_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_OPERATOR', $token, $expr);
						}
						$current['result'] = '.';
						$state['next'] = $valueSet;
						$state['oper'] = true;
						$state['preop'] = false;
						$state['variable'] = false;
						break;
					case '++':
					case '--':
						$current['token'] = self::OP_PRE_OPERATOR;
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							$current['token'] = self::OP_POST_OPERATOR;
							if(!($state['next'] & self::OP_POST_OPERATOR))
							{
								throw new Opt_Expression_Exception('OP_POST_OPERATOR', $token, $expr);
							}
							else
							{
								$state['next'] = self::OP_OPERATOR | self::OP_NULL;
							}
						}
						else
						{
							$state['next'] = self::OP_VARIABLE | self::OP_LANGUAGE_VAR | self::OP_NUMBER;
							$state['preop'] = true;
						}
						$state['oper'] = true;
						$state['variable'] = false;
						$current['result'] = $token;
						break;
					case '!':
						if(!($state['next'] & self::OP_PRE_OPERATOR))
						{
							throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
						}
						$current['result'] = $token;
						$current['token'] = self::OP_PRE_OPERATOR;
						$state['variable'] = false;
						break;
					case 'null':
					case 'false':
					case 'true':
						// These special values are treated as numbers by the compiler.
						if(!($state['next'] & self::OP_NUMBER))
						{
							throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
						}
						$current['token'] = self::OP_NUMBER;
						$current['result'] = $token;
						$state['next'] = $operatorSet;
						break;
					case '.':
						throw new Opt_Expression_Exception('.', $token, $expr);
						break;
					case '::':
						if(!($state['next'] & self::OP_CALL))
						{
							throw new Opt_Expression_Exception('OP_CALL', $token, $expr);
						}
						if(!$this->_tpl->basicOOP)
						{
							throw new Opt_NotSupported_Exception('object-oriented programming', 'disabled');
						}
						// OPT decides from the context, whether "::" means a static
						// or dynamic call.
						if($previous['token'] == self::OP_CLASS)
						{
							$current['result'] = '::';
							$state['call'] = 0;
						}
						else
						{
							$current['result'] = '->';
						}
						$current['token'] = self::OP_CALL;
						$state['next'] = self::OP_IDENTIFIER;
						break;
					case '(':
						// Check, if the parenhesis begins a function/method argument list
						if($previous['token'] == self::OP_METHOD || $previous['token'] == self::OP_FUNCTION || $previous['token'] == self::OP_CLASS)
						{
							// Yes, this is a function call, so we need to find its arguments.
							$args = array();
							for($j = $i + 1; $j < $to && $tokens[$j] != ')'; $j++)
							{
								if($tokens[$j][0] == $chr)
								{
									$args[] = $tokens[$j];
								}
								elseif($tokens[$j] != ',')
								{
									throw new Opt_Expression_Exception('OP_UNKNOWN', $tokens[$j], $expr);
								}
							}
							$argNum = sizeof($args);

							// Optionally, change the argument order
							if(!is_null($state['rev']))
							{
								$this->_reverseArgs($args, $state['rev'], $state['function']);
								$state['rev'] = null;
								$argNum = sizeof($args);
							}

							// Put the parenthesis to the compiled token list.
							$result[] = '(';

							// If we have a call of the assign() function, we need to store the
							// number of the translation unit in the _translationConversion field.
							// This will allow the language variable compiler to notice that here
							// we should have a language call that must be treated in a bit different
							// way.
							if($argNum > 0 && $state['assign_func'])
							{
								$this->_translationConversion = (int)trim($args[0], $chr);
							}
							// Build the argument list.
							for($k = 0; $k < $argNum; $k++)
							{
								$result[] = $args[$k];
								if($k < $argNum - 1)
								{
									$result[] = ',';
								}
							}
							$i = $j-1;
							$state['next'] = self::OP_BRACKET_E;
							$state['step'] = $previous['token'];
							continue;
						}
						else
						{
							if(!($state['next'] & self::OP_BRACKET))
							{
								throw new Opt_Expression_Exception('OP_BRACKET', $token, $expr);
							}
							$result[] = '(';
							$state['tu'] = self::OP_BRACKET_E;
							$state['next'] = self::OP_TU;
							$state['step'] = self::OP_VARIABLE;
						}
						break;
					case ')':
						if($state['step'] == 0)
						{
							throw new Opt_Expression_Exception('OP_BRACKET', $token, $expr);
						}
						else
						{
							if(!($state['next'] & self::OP_BRACKET_E))
							{
								throw new Opt_Expression_Exception('OP_BRACKET_E', $token, $expr);
							}
							$current['token'] = $state['step'];
							$current['result'] = ')';
							$state['step'] = 0;
							$state['next'] = self::OP_OPERATOR | self::OP_NULL | self::OP_CALL;
							if($state['clone'] == 1)
							{
								$state['next'] = self::OP_NULL | self::OP_CALL;
							}
						}
						break;
					default:
						if($token[0] == $chr)
						{
							// We've found another translation unit.
							if(!($state['next'] & self::OP_TU))
							{
								throw new Opt_Expression_Exception('OP_TU', 'Translation unit #'.ltrim($token, $chr), $expr);
							}
							if($previous['token'] != self::OP_ASSIGN)
							{
								$result[] = $token;
							}
							$state['next'] = $state['tu'];
						}
						elseif(preg_match('/^'.$this->_rVariable.'$/', $token))
						{
							// Variable call.
							if(!($state['next'] & self::OP_VARIABLE))
							{
								throw new Opt_Expression_Exception('OP_VARIABLE', $token, $expr);
							}
							// We do the first character test manually, because
							// in regular expression the parser would receive too much rubbish.
							if(!ctype_alpha($token[1]) && $token[1] != '_')
							{
								throw new Opt_Expression_Exception('OP_VARIABLE', $token, $expr);
							}
							// Moreover, we need to know the future (assignments)
							$assignment = null;
							if(isset($tokens[$i+1]) && ($tokens[$i+1] == '=' || $tokens[$i+1] == 'is'))
							{
								$assignment = $tokens[$i+2];
							}

							$out = $this->_compileVariable($token, $assignment);
							if(is_array($out))
							{
								foreach($out as $t)
								{
									$result[] = $t;
								}
								$current['result'] = '';
								$current['token'] = self::OP_VARIABLE;
							}
							else
							{
								$current['result'] = $out;
								$current['token'] = self::OP_VARIABLE;
							}
							if(is_null($state['variable']))
							{
								$state['variable'] = true;
							}
							// Hmmm... and what is the purpose of this IF? Seriously, I forgot.
							// So better do not touch it; it must have been very important.
							if($state['clone'] == 1)
							{
								$state['next'] = self::OP_SQ_BRACKET | self::OP_CALL | self::OP_NULL;
							}
							else
							{
								$state['next'] = $operatorSet | self::OP_SQ_BRACKET | self::OP_CALL;
							}
						}
						elseif(preg_match('/^'.$this->_rLanguageVarExtract.'$/', $token, $found))
						{
							// Extracting the language var.
							if(!($state['next'] & self::OP_LANGUAGE_VAR))
							{
								throw new Opt_Expression_Exception('OP_LANGUAGE_VAR', $token, $expr);
							}
							$current['result'] = $this->_compileLanguageVar($found[1], $found[2], $tu);
							$current['token'] = self::OP_LANGUAGE_VAR;
							$state['next'] = $operatorSet;
						}
						elseif(preg_match('/^'.$this->_rDecimalNumber.'$/', $token))
						{
							// Handling the decimal numbers.
							if(!($state['next'] & self::OP_NUMBER))
							{
								throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
							}
							$current['result'] = $token;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rHexadecimalNumber.'$/', $token))
						{
							// Hexadecimal, too.
							if(!($state['next'] & self::OP_NUMBER))
							{
								throw new Opt_Expression_Exception('OP_NUMBER', $token, $expr);
							}
							$current['result'] = $token;
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rSingleQuoteString.'$/', $token))
						{
							if(!($state['next'] & self::OP_STRING))
							{
								throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
							}
							$current['result'] = $this->_compileString($token);
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rBacktickString.'$/', $token))
						{
							if(!($state['next'] & self::OP_STRING))
							{
								throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
							}
							$current['result'] = $this->_compileString($token);
							$state['next'] = $operatorSet | self::OP_SQ_BRACKET_E;
						}
						elseif(preg_match('/^'.$this->_rIdentifier.'$/', $token))
						{
							$this->_compileIdentifier($token, $previous['token'], $previous['result'],
								isset($tokens[$i+1]) ? $tokens[$i+1] : null, $operatorSet, $expr, $current, $state);
						}
				}
				$previous = $current;
				if($current['result'] != '')
				{
					$result[] = $current['result'];
				}
			}
			// Finally, test if the pre- operators have been used properly.
			$this->_testPreOperators($previous['token'], $state['preop'], $token, $expr);

			// And if we are allowed to finish here...
			if(!($state['next'] & self::OP_NULL))
			{
				throw new Opt_Expression_Exception('OP_NULL', $token, $expr);
			}
			// TODO: For variable detection: check also class/object fields!
			return array($result, $assign, $state['variable']);
		} // end _compileExpression();

		/**
		 * An utility function that allows to test the preincrementation
		 * operators, if they are used in the right place. In case of
		 * problems, it generates an exception.
		 *
		 * @internal
		 * @param Int $previous The previous token type.
		 * @param Boolean $state The state of the "preop" expression parser flag.
		 * @param String $token The current token provided for debug purposes.
		 * @param String &$expr The reference to the parsed expression for debug purposes.
		 */
		protected function _testPreOperators($previous, $state, &$token, &$expr)
		{
			if(($previous == self::OP_METHOD || $previous == self::OP_FUNCTION || $previous == self::OP_EXPRESSION) && $state)
			{
				// Invalid use of prefix operators!
				throw new Opt_Expression_Exception('OP_PRE_OPERATOR', $token, $expr);
			}
		} // end _testPreOperators();

		/**
		 * Compiles the template variable into the PHP code. It can be
		 * generated in two contexts: read and save. The method supports
		 * all the special variables, local template variables and
		 * chooses the correct data formats. Moreover, it provides a
		 * build-in support for sections.
		 *
		 * @internal
		 * @param String $name Variable call
		 * @param String $newValue Null or the new value to assign
		 * @return String The output PHP code.
		 */
		protected function _compileVariable($name, $saveContext = null)
		{
			$value = substr($name, 1, strlen($name) - 1);
			$result = '';
			if(strpos($value, '.') !== FALSE)
			{
				$ns = explode('.', $value);
			}
			else
			{
				$ns = array(0 => $value);
			}

			if($name[0] == '@')
			{
				// The instruction may wish to handle this variable somehow differently.
				if(($to = $this->_compiler->convert('##var_'.$ns[0])) == '##var_'.$ns[0])
				{
					$result = 'self::$_vars';	// Standard handler
				}
				else
				{
					$result = $to;		// Programmer-defined handler
					unset($ns[0]);		// We assume that the variable name is already included into the handler.
				}

				// Link the rest of the array call.
				foreach($ns as $item)
				{
					if(ctype_digit($item))
					{
						$result .= '['.$item.']';
					}
					else
					{
						$result .= '[\''.$item.'\']';
					}
				}
				if($saveContext !== null)
				{
					return array($result.'=', $saveContext);
				}
				return $result;
			}
			else
			{
				/*
				 * This is the variable scanner that parses things like "$var.foo.bar.joe".
				 * Each segment of the name can be parsed in different format, depending on
				 * the programmer settings. Moreover, it recognizes the special calls, like "opt"/"system"
				 * or section element calls.
				 */

				$path = '';
				$previous = null;
				$code = '';
				$count = sizeof($ns);
				$state = array(
					'access' => $this->_tpl->variableAccess,
					'section' => null,
					'first' => false
				);

				// Check the first element for special keywords.
				switch($ns[0])
				{
					case 'opt':
					case 'sys':
					case 'system':
						if($saveContext !== null)
						{
							throw new Opt_AssignNotSupported_Exception($name);
						}
						return $this->_compileSys($ns);
					case 'this':
						$state['access'] = Opt_Class::ACCESS_LOCAL;
						unset($ns[0]);
						break;
					case 'global':
						$state['access'] = Opt_Class::ACCESS_GLOBAL;
						unset($ns[0]);
						break;
				}
				// Scan the rest of the name
				$final = sizeof($ns) - 1;
				foreach($ns as $id => $item)
				{
					$previous = $path;
					if($path == '')
					{
						// Parsing the first element. First, check the conversions.
						if(($to = $this->_compiler->convert('##simplevar_'.$item)) != '##simplevar_'.$item)
						{
							$item = $to;
						}
						$path = $item;
						$state['first'] = true;
					}
					else
					{
						// Parsing one of the later elements
						$path .= '.'.$item;
						$state['first'] = false;
					}

					// Processing sections
					if(!is_null($this->_compiler->isProcessor('section')))
					{
						if(is_null($state['section']))
						{
							// Check if any section with the specified name exists.
							$proc = $this->_compiler->processor('section');
							$sectionName = $this->_compiler->convert($item);
							if(!is_null($section = $proc->getSection($sectionName)))
							{
								$path = $sectionName;
								$state['section'] = $section;

								if($id == $count - 1)
								{
									// This is the last name element.
									if($saveContext !== null)
									{
										if(!$section['format']->property('section:itemAssign'))
										{
											throw new Opt_AssignNotSupported_Exception($name);
										}
										$format->assign('value', $saveContext);
										return $section['format']->get('section:itemAssign');
									}

									return $section['format']->get('section:item');
								}
								continue;
							}
						}
						else
						{
							// The section has been found, we need to process the item.
							$state['section']['format']->assign('item', $item);

							if($saveContext !== null && $id == $final)
							{
								if(!$state['section']['format']->property('section:variableAssign'))
								{
									throw new Opt_AssignNotSupported_Exception($name);
								}
								$state['section']['format']->assign('value', $saveContext);
								$code = $state['section']['format']->get('section:variableAssign');
							}
							else
							{
								$code = $state['section']['format']->get('section:variable');
							}
							$state['section'] = null;
							continue;
						}
					}

					// Now, the normal variables
					if($state['first'])
					{
						if($state['access'] == Opt_Class::ACCESS_GLOBAL)
						{
							$format = $this->_compiler->getFormat('global.'.$path, true);
						}
						else
						{
							$format = $this->_compiler->getFormat($path, true);
						}
						if(!$format->supports('variable'))
						{
							throw new Opt_FormatNotSupported_Exception($format->getName(), 'variable');
						}

						if($final == $id && $saveContext !== null)
						{
							if(!$format->property('variable:assign'))
							{
								throw new Opt_AssignNotSupported_Exception($name);
							}
							$format->assign('access', $state['access']);
							$format->assign('item', $item);
							$format->assign('value', $saveContext);
							$code = $format->get('variable:assign');
						}
						else
						{
							$format->assign('access', $state['access']);
							$format->assign('item', $item);
							$code = $format->get('variable:main');
						}
					}
					else
					{
						// The subitems are processed with the upper-item format
						if($state['access'] == Opt_Class::ACCESS_GLOBAL)
						{
							$format = $this->_compiler->getFormat('global.'.$previous, true);
						}
						else
						{
							$format = $this->_compiler->getFormat($previous, true);
						}
						if(!$format->supports('item'))
						{
							throw new Opt_FormatNotSupported_Exception($format->getName(), 'item');
						}
						if($final == $id && $saveContext !== null)
						{
							if(!$format->property('item:assign'))
							{
								throw new Opt_AssignNotSupported_Exception($name);
							}
							$format->assign('item', $item);
							$format->assign('value', $saveContext);
							$code .= $format->get('item:assign');
						}
						else
						{
							$format->assign('item', $item);
							$code .= $format->get('item:item');
						}
					}
				}
				if($saveContext !== null)
				{
					$out = explode($saveContext, $code);
					if(sizeof($out) == 0)
					{
						return $code;
					}
					return array(0 => $out[0], $saveContext, $out[1]);
				}
				return $code;
			}
		} // end _compileVariable();

		/**
		 * Compiles the call to the language variable into the PHP code.
		 *
		 * @param String $group Group name
		 * @param String $id Message identifier name within a group
		 * @param String $tu The ID of the current translation unit for handling the assign() function properly.
		 * @return String The output PHP code.
		 */
		protected function _compileLanguageVar($group, $id, $tu)
		{
			if(is_null($this->_tf))
			{
				throw new Opl_NoTranslationInterface_Exception('OPT template compiler');
			}
			if($tu === $this->_translationConversion)
			{
				$this->_translationConversion = null;
				return '\''.$group.'\',\''.$id.'\'';
			}
			return '$this->_tf->_(\''.$group.'\',\''.$id.'\')';
		} // end _compileLanguageVar();

		/**
		 * Compiles the special $sys variable to PHP code.
		 *
		 * @param Array $ns The $sys call splitted into array.
		 * @return String The output PHP code.
		 */
		protected function _compileSys(Array $ns)
		{
			switch($ns[1])
			{
				case 'version':
					return '\''.Opt_Class::VERSION.'\'';
				case 'const':
					return 'constant(\''.$ns[2].'\')';
				default:
					if(!is_null($this->_compiler->isProcessor($ns[1])))
					{
						return $this->_compiler->processor($ns[1])->processSystemVar($ns);
					}

					throw new Opt_SysVariableUnknown_Exception('$'.implode('.', $ns));
			}
		} // end _compileSys();

		/**
		 * Compiles the string call in the expression to a suitable PHP source code.
		 *
		 * @internal
		 * @param String $str The "string" string (with the delimiting characters)
		 * @return String The output PHP code.
		 */
		protected function _compileString($str)
		{
			// TODO: Fix
			// COMMENT: Fix what?
			switch($str[0])
			{
				case '\'':
					return $str;
				case '`':
					if(is_null($this->_tpl->backticks))
					{
						throw new Opt_NotSupported_Exception('backticks', 'not configured');
					}
					elseif(is_string($this->_tpl->backticks))
					{
						// A redirect to a function
						return $this->_tpl->backticks.'(\''.str_replace('\'', '\\\'', stripslashes(substr($str, 1, strlen($str) - 2))).'\')';
					}
					elseif(is_array($this->_tpl->backticks) && is_object($this->_tpl->backticks[0]))
					{
						// A redirect to an object method

						return '$this->_tpl->backticks[0]->'.$this->_tpl->backticks[1].'(\''.str_replace('\'', '\\\'', stripslashes(substr($str, 1, strlen($str) - 2))).'\')';
					}
					else
					{
						throw new Opt_InvalidCallback_Exception('backticks');
					}
				default:
					return '\''.$str.'\'';
			}
		} // end _compileString();

		/**
		 * Compiles the specified identifier encountered in the expression
		 * to the PHP code.
		 *
		 * @internal
		 * @param String $token The encountered token.
		 * @param Int $previous Previous token
		 * @param String $pt Used for OOP parsing to determine whether we have a static call.
		 * @param String $next The next token in the list
		 * @param Int $operatorSet The flag of allowed opcodes at this position.
		 * @param String &$expr The current expression (for debug purposes)
		 * @param Array &$current Reference to the current token information
		 * @param Array &$state Reference to the parser state flags.
		 */
		protected function _compileIdentifier($token, $previous, $pt, $next, $operatorSet, &$expr, &$current, &$state)
		{
			if($previous == self::OP_OBJMAN)
			{
				// Class constructor call
				if(($compiled = $this->_compiler->isClass($token)) !== null && $this->_tpl->basicOOP)
				{
					$current['result'] = $compiled;
					$current['token'] = self::OP_CLASS;
					$state['next'] = self::OP_BRACKET | self::OP_NULL;
					if($next == '(')
					{
						$state['func'] = 1;
					}
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Class', $token);
				}
			}
			elseif($next == '(')
			{
				// Function/method call
				if($previous == self::OP_CALL)
				{
					$current['result'] = $token;
					$current['token'] = self::OP_METHOD;
					$state['next'] = self::OP_BRACKET;
					$state['func'] = 1;
				}
				elseif(($compiled = $this->_compiler->isFunction($token)) !== null)
				{
					$name = $compiled;
					if($name[0] == '#')
					{
						$pos = strpos($name, '#', 1);
						if($pos === false)
						{
							throw new Opt_InvalidArgumentFormat_Exception($name, $token);
						}
						$state['rev'] = substr($name, 1, $pos - 1);
						$name = substr($name, $pos+1, strlen($name));
					}
					$current['result'] = $name;
					$current['token'] = self::OP_FUNCTION;
					$state['next'] = self::OP_BRACKET;
					$state['function'] = $token;
				}
				elseif($token == 'assign')
				{
					$current['result'] = '$this->_tf->assign';
					$current['token'] = self::OP_FUNCTION;
					$state['next'] = self::OP_BRACKET;
					$state['assign_func'] = true;
					$state['function'] = $token;
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Function', $token);
				}
			}
			elseif($previous == self::OP_CALL)
			{
				// Class/object field call, check whether static or not.
				$current['result'] = ($pt == '::' ? '$'.$token : $token);
				$current['token'] = self::OP_FIELD;
				$state['next'] = $operatorSet | self::OP_SQ_BRACKET | self::OP_CALL;
				if($state['clone'] == 1)
				{
					$state['next'] = self::OP_SQ_BRACKET | self::OP_CALL | self::OP_NULL;
				}
			}
			elseif($next == '::')
			{
				// Static class call
				if(($compiled = $this->_compiler->isClass($token)) !== null)
				{
					$current['result'] = $compiled;
					$current['token'] = self::OP_CLASS;
					$state['next'] = self::OP_CALL;
				}
				else
				{
					throw new Opt_ItemNotAllowed_Exception('Class', $token);
				}
			}
			else
			{
				// An ending string.
				if(!$state['next'] & self::OP_STRING)
				{
					throw new Opt_Expression_Exception('OP_STRING', $token, $expr);
				}
				$state['next'] = self::OP_NULL;
				$current['token'] = self::OP_STRING;
				$current['result'] = '\''.$token.'\'';
			}
		} // end _compileIdentifier();

		/**
		 * Processes the argument order change functionality for function
		 * parsing in expressions.
		 *
		 * @internal
		 * @param Array &$args Reference to a list of function arguments.
		 * @param String $format The new order format code.
		 * @param String $function The function name provided for debugging purposes.
		 */
		protected function _reverseArgs(&$args, $format, $function)
		{
			$codes = explode(',', $format);
			$newArgs = array();
			$i = 0;
			foreach($codes as $code)
			{
				$data = explode(':', $code);
				if(!isset($args[$i]))
				{
					if(!isset($data[1]))
					{
						throw new Opt_FunctionArgument_Exception($i, $function);
					}
					$newArgs[(int)$data[0]-1] = $data[1];
				}
				else
				{
					$newArgs[(int)$data[0]-1] = $args[$i];
				}
				$i++;
			}
			$args = $newArgs;
		} // end _reverseArgs();
	} // end Opt_Expression_Standard;
