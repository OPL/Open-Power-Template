%token_prefix T_
%name Opt_Expression_Standard_
%declare_class {class Opt_Expression_Standard_Parser}

%include_class
{
	/**
	 * The expression engine object.
	 * @var Opt_Expression_Standard
	 */
	private $_expr;

	/**
	 * Constructs the expression parser.
	 *
	 * @param Opt_Expression_Standard $expr The expression engine used for parsing.
	 */
	public function __construct(Opt_Expression_Standard $expr)
	{
		$this->_expr = $expr;
	} // end __construct();
}

%syntax_error {
	throw new Exception('Unexpected '.$TOKEN);
}

%left	AND.
%left	OR.
%left	XOR.
%left	EQUALS EQUALS_T NEQUALS NEQUALS_T.
%left	GT GTE LT LTE.
%left	IS_BETWEEN IS_NOT_BETWEEN.
%left	IS_EITHER IS_NEITHER.
%left	CONTAINS CONTAINS_EITHER CONTAINS_NEITHER CONTAINS_BOTH.
%left	IS_IN IS_NOT_IN.
%left	IS_IN_EITHER IS_IN_NEITHER IS_IN_BOTH.
%left	ADD SUB MINUS CONCAT.
%left	MUL DIV MOD.
%left	COLON.
%right	EXP NOT.
%right	ASSIGN.
%right	INCREMENT DECREMENT.

// warning: in case of strange errors while parsing something with this grammar
// that seem to make no sense, please take a look at ParserGenerator/Data.php file,
// method buildshifts(). It must have been rewritten to a non-recursive version
// in order not to crash while parsing this grammar due to "maximum nesting level too deep".
// The bug may lie there.

overall_expr(res)	::= expr(ex).					{	res = $this->_expr->_finalize(ex);	}
expr(res)			::= expr(ex1) ADD expr(ex2).	{	res = $this->_expr->_stdOperator('+', ex1, ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) SUB expr(ex2).					{	res = $this->_expr->_stdOperator('-', ex1,  ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) MINUS expr(ex2).					{	res = $this->_expr->_stdOperator('-', ex1,  ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) MUL expr(ex2).					{	res = $this->_expr->_stdOperator('*',  ex1,  ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) DIV expr(ex2).					{	res = $this->_expr->_stdOperator('/',  ex1,  ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) MOD expr(ex2).					{	res = $this->_expr->_stdOperator('%',  ex1,  ex2, Opt_Expression_Standard::MATH_OP_WEIGHT);	}
expr(res)			::= expr(ex1) AND expr(ex2).					{	res = $this->_expr->_stdOperator('&&',  ex1,  ex2, Opt_Expression_Standard::LOGICAL_OP_WEIGHT);	}
expr(res)			::= expr(ex1) OR expr(ex2).						{	res = $this->_expr->_stdOperator('||',  ex1,  ex2, Opt_Expression_Standard::LOGICAL_OP_WEIGHT);	}
expr(res)			::= expr(ex1) XOR expr(ex2).					{	res = $this->_expr->_stdOperator(' xor ',  ex1,  ex2, Opt_Expression_Standard::LOGICAL_OP_WEIGHT);	}
expr(res)			::= expr(ex1) EXP expr(ex2).					{	res = $this->_expr->_functionalOperator('pow', array( ex1,  ex2), Opt_Expression_Standard::FUNCTIONAL_OP_WEIGHT);	}
expr(res)			::= expr(ex1) EQUALS expr(ex2).					{	res = $this->_expr->_stdOperator('==',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) EQUALS_T expr(ex2).				{	res = $this->_expr->_stdOperator('===',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) NEQUALS expr(ex2).				{	res = $this->_expr->_stdOperator('!=',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) NEQUALS_T expr(ex2).				{	res = $this->_expr->_stdOperator('!==',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) GT expr(ex2).						{	res = $this->_expr->_stdOperator('>',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) GTE expr(ex2).					{	res = $this->_expr->_stdOperator('>=',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) LT expr(ex2).						{	res = $this->_expr->_stdOperator('<',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) LTE expr(ex2).					{	res = $this->_expr->_stdOperator('<=',  ex1,  ex2, Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
expr(res)			::= expr(ex1) CONCAT expr(ex2).					{	res = $this->_expr->_stdOperator('.',  ex1,  ex2, Opt_Expression_Standard::CONCAT_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) CONTAINS expr(ex2).							{	res = $this->_expr->_expressionOperator('contains', array(ex1, ex2), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) CONTAINS_EITHER expr(ex2) OR expr(ex3).		{	res = $this->_expr->_expressionOperator('contains_either', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) CONTAINS_NEITHER expr(ex2) NOR expr(ex3).		{	res = $this->_expr->_expressionOperator('contains_neither', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) CONTAINS_BOTH expr(ex2) AND expr(ex3).		{	res = $this->_expr->_expressionOperator('contains_both', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_BETWEEN expr(ex2) AND expr(ex3).			{	res = $this->_expr->_expressionOperator('between', array(ex1, ex2, ex3), 2 * Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_NOT_BETWEEN expr(ex2) AND expr(ex3).		{	res = $this->_expr->_expressionOperator('not_between', array(ex1, ex2, ex3), 2 * Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_EITHER expr(ex2) OR expr(ex3).				{	res = $this->_expr->_expressionOperator('either', array(ex1, ex2, ex3), 2 * Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_NEITHER expr(ex2) NOR expr(ex3).			{	res = $this->_expr->_expressionOperator('neither', array(ex1, ex2, ex3), 2 * Opt_Expression_Standard::COMPARE_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_IN expr(ex2).								{	res = $this->_expr->_expressionOperator('is_in', array(ex1, ex2), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_NOT_IN expr(ex2).							{	res = $this->_expr->_expressionOperator('is_not_in', array(ex1, ex2), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_EITHER_IN expr(ex2) OR expr(ex3).			{	res = $this->_expr->_expressionOperator('is_either_in', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_NEITHER_IN expr(ex2) NOR expr(ex3).		{	res = $this->_expr->_expressionOperator('is_neither_in', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
cexpr(res)			::= expr(ex1) IS_BOTH_IN expr(ex2) AND expr(ex3).			{	res = $this->_expr->_expressionOperator('is_both_in', array(ex1, ex2, ex3), Opt_Expression_Standard::DF_OP_WEIGHT);	}
expr(res)			::= NOT expr(ex).					{	res = $this->_expr->_unaryOperator('!', ex, Opt_Expression_Standard::LOGICAL_OP_WEIGHT);	}
expr(res)			::= L_BRACKET expr(ex) R_BRACKET.	{	res = $this->_expr->_package('(', ex, Opt_Expression_Standard::PARENTHESES_WEIGHT);	}
expr(res)			::= cexpr(ex).						{	res = ex;	}

expr(res)			::= variable(var) INCREMENT.		{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1], Opt_Expression_Standard::INCDEC_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_POSTDECREMENT, null);	}
expr(res)			::= INCREMENT variable(var).		{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1], Opt_Expression_Standard::INCDEC_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_PREINCREMENT, null);	}
expr(res)			::= variable(var) DECREMENT.		{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1], Opt_Expression_Standard::INCDEC_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_POSTDECREMENT, null);	}
expr(res)			::= DECREMENT variable(var).		{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1], Opt_Expression_Standard::INCDEC_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_PREDECREMENT, null);	}
expr(res)			::= variable(var) ASSIGN expr(expr).	{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1],Opt_Expression_Standard::ASSIGN_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_ASSIGN, expr);	}
expr(res)			::= variable(var) IS expr(expr).		{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1],Opt_Expression_Standard::ASSIGN_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_ASSIGN, expr);	}
expr(res)			::= variable(var) EXISTS.				{	$var = var; res = $this->_expr->_compileVariable($var[0], $var[1],Opt_Expression_Standard::ASSIGN_OP_WEIGHT, Opt_Expression_Standard::CONTEXT_EXISTS, expr);	}

expr(res)			::= CLONE expr(ex).			{	res = $this->_expr->_objective('clone', ex, Opt_Expression_Standard::CLONE_WEIGHT);	}

expr(res)			::= variable(var).				{	res = $this->_expr->_compileVariable(var[0], var[1], 0);	}
expr(res)			::= static_value(val).			{	res =  val;	}
expr(res)			::= calculated(val).				{	res =  val;	}
expr(res)			::= language_variable(val).		{	res =  val;	}
expr(res)			::= container_creator(val).		{	res =  val;	}
expr(res)			::= object_creator(val).			{	res =  val;	}

variable(res)		::= simple_variable(val).		{	val[0] = array(val[0]); res = val;	}
variable(res)		::= object_field_call(val).		{	res =  val;	}
variable(res)		::= class_field_call(val).		{	res =  val;	}
variable(res)		::= array_call(val).				{	res =  val;	}

simple_variable(res)	::= script_variable(val).		{	res =  val;	}
simple_variable(res)	::= template_variable(val).		{	res =  val;	}
simple_variable(res)	::= container(val).				{	res =  val;	}

static_value(res)	::= number(n).				{	res = $this->_expr->_scalarValue(n, Opt_Expression_Standard::SCALAR_WEIGHT);	}
static_value(res)	::= string(s).				{	res = $this->_expr->_scalarValue(s, Opt_Expression_Standard::SCALAR_WEIGHT);	}
static_value(res)	::= BACKTICK_STRING(s).	{	res = $this->_expr->_backtick(s, Opt_Expression_Standard::BACKTICK_WEIGHT);	}
static_value(res)	::= boolean(b).			{	res = $this->_expr->_scalarValue(b, Opt_Expression_Standard::SCALAR_WEIGHT);	}
static_value(res)	::= NULL.				{	res = $this->_expr->_scalarValue('null', Opt_Expression_Standard::SCALAR_WEIGHT);	}

string(s)			::= STRING(val).			{ s = val; }
string(s)			::= IDENTIFIER(val).		{ s = val; }

number(n)			::= NUMBER(val).			{ n =  val; }
number(n)			::= MINUS NUMBER(val).		{ n = - val; }

boolean(b)			::= TRUE.			{ b = 'true'; }
boolean(b)			::= FALSE.			{ b = 'false'; }

container_creator	::= LSQ_BRACKET RSQ_BRACKET.
container_creator	::= LSQ_BRACKET container_def RSQ_BRACKET.
container_def		::= single_container_def.
container_def		::= single_container_def COMMA container_def.

single_container_def	::= expr COLON expr.
single_container_def	::= expr.

script_variable(res)	::= DOLLAR IDENTIFIER(name).	{	res = $this->_expr->_prepareScriptVar(name); }
template_variable(res)	::= AT IDENTIFIER(name).		{	res = $this->_expr->_prepareTemplateVar(name); }
language_variable(res)	::= DOLLAR IDENTIFIER(group) AT IDENTIFIER(id).
container(res)			::= script_variable(var) container_call(cont).
		{
			$var = var;
			array_unshift(cont, $var[0]);
			res = new SplFixedArray(2);
			res[0] = cont;
			res[1] = '$';
		}
container(res)			::= template_variable(var) container_call(cont).
		{
			$var = var;
			array_unshift(cont, $var[0]);
			res = new SplFixedArray(2);
			res[0] = cont;
			res[1] = '@';
		}

container_call(res)		::= single_container_call(f).					{	res = array(f);	}
container_call(res)		::= single_container_call(f) container_call(c).	{	array_unshift(c, f); res = c;	}

single_container_call(res)	::= DOT IDENTIFIER(s).					{	res = s;	}
single_container_call(res)	::= DOT NUMBER(n).						{	res = n;	}
single_container_call(res)	::= DOT L_BRACKET expr(r) R_BRACKET.	{	res = r;	}

object_field_call	::= variable OBJECT_OPERATOR field_call.
object_method_call	::= variable OBJECT_OPERATOR method_call.
object_field_call	::= variable object_call_list OBJECT_OPERATOR field_call.
object_method_call	::= variable object_call_list OBJECT_OPERATOR method_call.

class_field_call	::= IDENTIFIER OBJECT_OPERATOR field_call.
class_method_call	::= IDENTIFIER OBJECT_OPERATOR method_call.
class_field_call	::= IDENTIFIER object_call_list OBJECT_OPERATOR field_call.
class_method_call	::= IDENTIFIER object_call_list OBJECT_OPERATOR method_call.

object_call_list	::= OBJECT_OPERATOR object_call.
object_call_list	::= object_call_list OBJECT_OPERATOR object_call.

object_call		::= method_call.
object_call		::= field_call.

method_call		::= functional.
field_call		::= IDENTIFIER.

calculated		::= function_call.
calculated		::= object_method_call.
calculated		::= class_method_call.

function_call	::= functional.

functional		::= IDENTIFIER L_BRACKET argument_list R_BRACKET.
functional		::= IDENTIFIER L_BRACKET container_def R_BRACKET.
functional		::= IDENTIFIER L_BRACKET R_BRACKET.

argument_list	::= expr.
argument_list	::= expr COMMA argument_list.

array_call		::= simple_variable array_call_list.
array_call_list	::= LSQ_BRACKET expr RSQ_BRACKET.
array_call_list	::= LSQ_BRACKET expr RSQ_BRACKET array_call_list.

object_creator	::= NEW IDENTIFIER.
object_creator	::= NEW IDENTIFIER L_BRACKET argument_list R_BRACKET.
