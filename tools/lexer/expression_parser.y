%token_prefix T_
%name Opt_Expression_Standard_
%declare_class {class Opt_Expression_Standard_Parser}

%syntax_error {
	throw new Exception($yymajor);
}

%left	AND.
%left	OR.
%left	XOR.
%left	EQUALS EQUALS_T NEQUALS NEQUALS_T.
%left	GT GTE LT LTE.
%left	BETWEEN.
%left	ADD SUB MINUS CONCAT.
%left	MUL DIV MOD.
%left	COLON.
%right	EXP NOT.
%right	INCREMENT DECREMENT.

overall_expr	::= expr.
expr			::= expr ADD expr.
expr			::= expr SUB expr.
expr			::= expr MINUS expr.
expr			::= expr MUL expr.
expr			::= expr DIV expr.
expr			::= expr MOD expr.
expr			::= expr AND expr.
expr			::= expr OR expr.
expr			::= expr XOR expr.
expr			::= expr EXP expr.
expr			::= expr EQUALS expr.
expr			::= expr EQUALS_T expr.
expr			::= expr NEQUALS expr.
expr			::= expr NEQUALS_T expr.
expr			::= expr GT expr.
expr			::= expr GTE expr.
expr			::= expr LT expr.
expr			::= expr LTE expr.
expr			::= expr CONCAT expr.
expr			::= expr CONTAINS expr.
expr			::= expr BETWEEN expr AND expr.
expr			::= NOT expr.
expr			::= L_BRACKET expr R_BRACKET.

expr			::= variable INCREMENT.
expr			::= INCREMENT variable.
expr			::= variable DECREMENT.
expr			::= DECREMENT variable.
expr			::= variable ASSIGN expr.
expr			::= variable EXISTS.

expr			::= CLONE expr.

expr			::= variable.
expr			::= static_value.
expr			::= calculated.
expr			::= language_variable.
expr			::= container_creator.
expr			::= object_creator.

variable		::= simple_variable.
variable		::= object_field_call.
variable		::= array_call.

simple_variable	::= script_variable.
simple_variable	::= template_variable.
simple_variable	::= container.

static_value	::= number.
static_value	::= string.
static_value	::= BACKTICK_STRING.
static_value	::= boolean.
static_value	::= NULL.

string			::= STRING.
string			::= IDENTIFIER.

number			::= NUMBER.
number			::= MINUS NUMBER.

boolean			::= TRUE.
boolean			::= FALSE.

container_creator	::= LSQ_BRACKET RSQ_BRACKET.
container_creator	::= LSQ_BRACKET container_def RSQ_BRACKET.
container_def		::= single_container_def.
container_def		::= single_container_def COMMA container_def.

single_container_def	::= expr COLON expr.

script_variable	::= DOLLAR IDENTIFIER.
template_variable	::= AT IDENTIFIER.
language_variable	::= DOLLAR IDENTIFIER AT IDENTIFIER.
container			::= script_variable container_call.
container			::= template_variable container_call.

container_call		::= single_container_call.
container_call		::= single_container_call container_call.

single_container_call	::= DOT IDENTIFIER.
single_container_call	::= DOT NUMBER.
single_container_call	::= DOT L_BRACKET expr R_BRACKET.

object_field_call	::= variable field_call.
object_method_call	::= variable method_call.

method_call		::= OBJECT_OPERATOR functional.
field_call		::= OBJECT_OPERATOR IDENTIFIER.

calculated		::= function_call.
calculated		::= object_method_call.

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
