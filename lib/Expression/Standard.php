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
 * $Id: Class.php 155 2009-07-18 07:25:11Z zyxist $
 */

/**
 * The standard OPT expression engine that implements the official
 * expression syntax.
 *
 * @package Expressions
 */
class Opt_Expression_Standard implements Opt_Expression_Interface
{
	const SCALAR_WEIGHT = 1;
	const PARENTHESES_WEIGHT = 1;
	const CONTAINER_ITEM_WEIGHT = 2;
	const VARIABLE_WEIGHT = 2;
	const SECTION_ITEM_WEIGHT = 2;
	const SECTION_VARIABLE_WEIGHT = 2;
	const LANGUAGE_VARIABLE = 3;
	const MATH_OP_WEIGHT = 5;
	const LOGICAL_OP_WEIGHT = 5;
	const COMPARE_OP_WEIGHT = 5;
	const CONCAT_OP_WEIGHT = 6;
	const ASSIGN_OP_WEIGHT = 15;
	const EXISTS_OP_WEIGHT = 15;
	const DF_OP_WEIGHT = 20;
	const INCDEC_OP_WEIGHT = 30;
	const FUNCTIONAL_OP_WEIGHT = 30;
	const CLONE_WEIGHT = 50;

	const CONTEXT_READ = 0;
	const CONTEXT_ASSIGN = 1;
	const CONTEXT_POSTINCREMENT = 2;
	const CONTEXT_POSTDECREMENT = 3;
	const CONTEXT_PREINCREMENT = 4;
	const CONTEXT_PREDECREMENT = 5;
	const CONTEXT_EXISTS = 6;

	/**
	 * A translation of the context numbers to the
	 * data format calls.
	 *
	 * @var array
	 */
	private $_dfCalls = array(0 =>
		'',
		'.assign',
		'.postincrement',
		'.postdecrement',
		'.preincrement',
		'.predecrement',
		'.exists'
	);


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
	 * The compiled expression.
	 * @var string
	 */
	protected $_compiled;
	/**
	 * Is the assignment operator used at the lowest level?
	 * @var boolean
	 */
	protected $_assign;
	/**
	 * The calculated expression complexity for optimization purposes.
	 * @var integer
	 */
	protected $_complexity;

	/**
	 * The unique identifier generator
	 * @var integer
	 */
	protected $_unique = 0;

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
	 * @param string $expression The expression source
	 * @return array
	 */
	public function parse($expr)
	{
		$this->_unique = 0;

		$lexer = new Opt_Expression_Standard_Lexer($expr);
		$parser = new Opt_Expression_Standard_Parser($this);
		while ($lexer->yylex())
		{
			if($lexer->token != 'w')
			{
				$parser->doParse($lexer->token, $lexer->value);
			}
		}
		$parser->doParse(0, 0);

		return array(
			'bare'			=> $this->_compiled,
			'expression'	=> $this->_compiled,
			'complexity'	=> $this->_complexity,
			'type'			=> Opt_Compiler_Class::COMPOUND
		);
	} // end parse();

	/**
	 * Finalizes the expression parsing. Side effects: the compilation
	 * results are saved into the $_compiled and $_complexity object
	 * fields.
	 * 
	 * @param SplFixedArray $expression The expression array.
	 */
	public function _finalize(SplFixedArray $expression)
	{
		$this->_compiled = $expression[0];
		$this->_complexity = $expression[1];
	} // end _finalize();

	/**
	 * Creates a scalar value.
	 *
	 * @param mixed $value
	 * @param int $weight
	 * @return SplFixedArray
	 */
	public function _scalarValue($value, $weight)
	{
		$array = new SplFixedArray(2);
		$array[0] = $value;
		$array[1] = $weight;

		return $array;
	} // end _scalarValue();

	/**
	 * Prepares a script variable for further parsing. We do not parse it
	 * into PHP here, because we must check if we have an assignment, incrementation
	 * or something else.
	 *
	 * @param string $name The variable name
	 * @return SplFixedArray
	 */
	public function _prepareScriptVar($name)
	{
		$array = new SplFixedArray(3);
		$array[0] = $name;
		$array[1] = '$';

		return $array;
	} // end _prepareScriptVar();

	/**
	 * Prepares a template variable for further parsing. We do not parse it
	 * into PHP here, because we must check if we have an assignment, incrementation
	 * or something else.
	 *
	 * @param string $name The variable name
	 * @return SplFixedArray
	 */
	public function _prepareTemplateVar($name)
	{
		$array = new SplFixedArray(3);
		$array[0] = $name;
		$array[1] = '@';

		return $array;
	} // end _prepareTemplateVar();

	/**
	 * Compiles the variable call in the specified context. It processes the containers,
	 * assignments and other stuff directly related to the variables, returning an
	 * SplFixedArray object with token information.
	 *
	 * @param array $variable The list of container elements
	 * @param string $type The variable type
	 * @param integer $weight The expression weight
	 * @param integer $context The variable occurence context (normal, assignment, etc.)
	 * @param string $contextInfo The information provided by the context
	 * @return SplFixedArray
	 */
	public function _compileVariable(array $variable, $type, $weight, $context = 0, $contextInfo = null)
	{
		$conversion = '##simplevar_';
		$defaultFormat = null;
		if($type == '@')
		{
			$conversion = '##var_';
			$defaultFormat = 'TemplateVariable';
		}

		$state = array(
			'further'	=> false,
			'section'	=> null
		);

		$answer = new SplFixedArray(2);

		// The variable scanner
		$proc = null;
		if($this->_compiler->isProcessor('section') !== null)
		{
			$proc = $this->_compiler->processor('section');
		}

		$count = sizeof($variable);
		$final = $count - 1;
		$localWeight = 0;
		$code = '';
		$path = '';
		$previous = null;
		foreach($variable as $id => $item)
		{
			// Handle conversions
			$previous = $path;
			if($path == '')
			{
				// Parsing the first element. First, check the conversions.
				if(($to = $this->_compiler->convert($conversion.$item)) != $conversion.$item)
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

			// Processing section calls
			if($proc !== null)
			{
				if($state['section'] === null)
				{
					// Check if any section with the specified name exists.
					$sectionName = $this->_compiler->convert($item);

					if(($section = $proc->getSection($sectionName)) !== null)
					{
						$path = $sectionName;
						$state['section'] = $section;

						if($id == $final)
						{
							// This is the last element
							$hook = 'section:item'.$this->_dfCalls[$context];
							if(!$section['format']->property($hook))
							{
								throw new Opt_OperationNotSupported($name, $this->_dfCalls[$context]);
							}
							$section['format']->assign('value', $contextInfo);
							$section['format']->assign('code', $code);

							$code = $section['format']->get($hook);
							$localWeight = self::SECTION_ITEM_WEIGHT;

							break;
						}
						continue;
					}
				}
				else
				{
					// The section has been found, we need to process the item.
					// TODO: Perhaps INCREMENT and DECREMENT must have here a different code...
					// We must remember that the container call may be longer and they may refer
					// to the other part of the chain.
					$state['section']['format']->assign('item', $item);

					$hook = 'section:variable';
					if($id == $final)
					{
						$hook .= $this->_dfCalls[$context];
						$section['format']->assign('value', $contextInfo);
						$section['format']->assign('code', $code);
						if(!$section['format']->property($hook))
						{
							throw new Opt_OperationNotSupported($name, $this->_dfCalls[$context]);
						}
					}
					$code = $section['format']->get($hook);
					$localWeight = self::SECTION_VARIABLE_WEIGHT;

					$state['section'] = null;

					continue;
				}
			}

			// Now, the normal container calls
			if($id == 0)
			{
				// The first element processing
				$format = $this->_compiler->getFormat($path, true);
				if(!$format->supports('variable'))
				{
					throw new Opt_FormatNotSupported_Exception($format->getName(), 'variable');
				}
				// Check if the format supports capturing the whole container
				if($format->property('variable:capture'))
				{
					$format->assign('items', $variable);
					$format->assign('dynamic', $isDynamic);
					$hook = 'capture';
				}
				// An ordinary call
				else
				{
					$hook = 'item';
				}

				if($context > 0)
				{
					$format->assign('value', $contextInfo);
					$format->assign('code', $code);

					if(!$section['format']->property('variable:'.$hook.$this->_dfCalls[$context]))
					{
						throw new Opt_OperationNotSupported($path, $this->_dfCalls[$context]);
					}
				}
				$code = $format->get('variable:'.$hook.$this->_dfCalls[$context]);
				$localWeight = $count * self::CONTAINER_ITEM_WEIGHT;
				if($hook == 'capture')
				{
					break;
				}
			}
			else
			{
				$format = $this->_compiler->getFormat($previous, true);

				$hook = 'item:item';
				if($id == $final)
				{
					$hook .= $this->_dfCalls[$context];
					$section['format']->assign('value', $contextInfo);
					$section['format']->assign('code', $code);

					if(!$section['format']->property($hook))
					{
						throw new Opt_OperationNotSupported($path, $this->_dfCalls[$context]);
					}
					$code = $format->get($hook);
				}
				else
				{
					$code .= $format->get($hook);
				}
				$localWeight += self::CONTAINER_ITEM_WEIGHT;
			}
		}
		$answer[0] = $code;
		$answer[1] = $localWeight + $weight;
		return $answer;
	} // end _compileVariable();

	/**
	 * Processes a binary operator that connects two expressions.
	 *
	 * @param string $operator The PHP operator
	 * @param SplFixedArray $expr1 The left expression
	 * @param SplFixedArray $expr2 The right expression
	 * @param int $weight The operator weight
	 * @return SplFixedArray
	 */
	public function _stdOperator($operator, SplFixedArray $expr1, SplFixedArray $expr2, $weight)
	{
		$expr1[0] = $expr1[0].' '.$operator.' '.$expr2[0];
		$expr1[1] += $expr2[1] + $weight;

		return $expr1;
	} // end _stdOperator();

	/**
	 * The compound expression operator parsing.
	 *
	 * @param string $operator The used operator name
	 * @param array $arguments The operator arguments
	 * @param int $weight The operator weight
	 * @return SplFixedArray
	 */
	public function _expressionOperator($operator, array $arguments, $weight)
	{
		// More complex expressions should be sanitized to avoid
		// potential problems with operator precedence.
		foreach($arguments as &$arg)
		{
			if($arg[1] >= 5)
			{
				$arg[0] = '('.$arg[0].')';
			}
		}

		// Select the operator and the action.
		switch($operator)
		{
			case 'contains':
				// TODO: Add data format support here.
				$finalExpression = 'Opt_Function::contains('.$arguments[0][0].', '.$arguments[1][0].')';
				$finalWeight = $arguments[0][1] + $arguments[1][1] + $weight;
				break;

			case 'contains_both':
				$operator = '&&';
			case 'contains_either':
				// TODO: Add data format support here.
				// TODO: Simplify and optimize the lookup of multiple values by forcing smarter searching
				// by the data format.

				$operator = (isset($operator) ? $operator : '||');
				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = 'Opt_Function::contains('.$arguments[0][0].', '.$arguments[1][0].') '.$operator.' Opt_Function::contains('.$arguments[0][0].', '.$arguments[2][0].')';
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = 'Opt_Function::contains($__expru_'.$this->_unique.' = '.$arguments[0][0].', '.$arguments[1][0].') '.$operator.' Opt_Function::contains($__expru_'.$this->_unique.', '.$arguments[2][0].')';
					$this->_unique++;
				}
				$finalExpression = 'Opt_Function::contains('.$arguments[0][0].', '.$arguments[1][0].')';
				$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				break;
			case 'contains_neither':
				// TODO: Add data format support here.

				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = '!Opt_Function::contains('.$arguments[0][0].', '.$arguments[1][0].') && !Opt_Function::contains('.$arguments[0][0].', '.$arguments[2][0].')';
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = '!Opt_Function::contains($__expru_'.$this->_unique.' = '.$arguments[0][0].', '.$arguments[1][0].') && !Opt_Function::contains($__expru_'.$this->_unique.', '.$arguments[2][0].')';
					$this->_unique++;
				}
				$finalExpression = 'Opt_Function::contains('.$arguments[0][0].', '.$arguments[1][0].')';
				$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				break;
			case 'between':
				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = $arguments[1][0].' < '.$arguments[0][0].' && '.$arguments[0][0].' < '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = $arguments[1][0].' < ($__expru_'.$this->_unique.' = '.$arguments[0][0].') && $__expru_'.$this->_unique.' < '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
					$this->_unique++;
				}
				break;
			case 'not_between':
				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = $arguments[1][0].' >= '.$arguments[0][0].' || '.$arguments[0][0].' >= '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = $arguments[1][0].' >= ($__expru_'.$this->_unique.' = '.$arguments[0][0].') || $__expru_'.$this->_unique.' >= '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
					$this->_unique++;
				}
				break;
			case 'either':
				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = $arguments[1][0].' == '.$arguments[0][0].' || '.$arguments[0][0].' == '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = $arguments[1][0].' == ($__expru_'.$this->_unique.' = '.$arguments[0][0].') || $__expru_'.$this->_unique.' == '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
					$this->_unique++;
				}
				break;
			case 'neither':
				// Decide if we need an optimization, when the tested first expression is too complex.
				if($arguments[0][1] < 5)
				{
					// OK, this is pretty lightweight, we can duplicate it
					$finalExpression = $arguments[1][0].' !== '.$arguments[0][0].' && '.$arguments[0][0].' !== '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
				}
				else
				{
					// This is hard. The result of the first expression should be stored in a
					// temporary variable in order not to calculate everything twice.
					$finalExpression = $arguments[1][0].' !== ($__expru_'.$this->_unique.' = '.$arguments[0][0].') && $__expru_'.$this->_unique.' !== '.$arguments[2][0];
					$finalWeight = $arguments[0][1] + $arguments[1][1] + $arguments[2][1] + $weight;
					$this->_unique++;
				}
				break;
			default:
				// TODO: Error here!
		}
		$arguments[0][0] = $finalExpression;
		$arguments[0][1] = $finalWeight;

		return $arguments[0];
	} // end _expressionOperator();

	/**
	 * Packs the expression within parentheses.
	 *
	 * @param string $what The parenthese type
	 * @param SplFixedArray $expr The expression to pack
	 * @param int $weight The parentheses weight
	 * @return SplFixedArray
	 */
	public function _package($what, SplFixedArray $expr, $weight)
	{
		$expr[0] = '('.$expr[0].')';
		$expr[1] += $weight;

		return $expr;
	} // end _package();


} // end Opt_Expression_Standard;
