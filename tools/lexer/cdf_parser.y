%token_prefix T_
%name Opt_Cdf_
%declare_class {class Opt_Cdf_Parser}

%syntax_error {
	throw new Exception('Unexpected token '.$TOKEN);
}

cdf_file	::= block_list.	{	echo "cdf_file accepted\n";	}
block_list	::= block_item.
block_list	::= block_item block_list.

block_item	::= element_selection block_body.	{	echo "block_item accepted\n";	}

element_selection	::= element_def.
element_selection	::= element_def COMMA element_selection.

element_def	::= item.
element_def ::= item element_def.

item	::= ID.
item	::= ID HASH element_id.

block_body	::= LCURBRACKET body_rules RCURBRACKET.	{	echo "block_body accepted\n";	}
block_body	::= LCURBRACKET RCURBRACKET.	{	echo "block_body accepted\n";	}
body_rules	::= single_rule.
body_rules	::= single_rule body_rules.

single_rule	::= property_id(pid) COLON format_id(fid) SEMICOLON.	{	echo 'single_rule '.pid.': '.fid."\n";	}

element_id(s)	::= ID(v).	{	s = v;	}
element_id(s)	::= ID(v) DOT element_id(r).	{	s = v.'.'.r;	}

property_id(s)	::= ID(v).	{	s = v;	}
property_id(s)	::= ID(v) PAUSE property_id(r).	{	s = v.'-'.r;	}

format_id(s)	::= ID(v).	{	s = v;	}
format_id(s)	::= ID(v) SLASH format_id(r).	{	s = v.'/'.r;	}