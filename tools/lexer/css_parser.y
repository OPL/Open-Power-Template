%token_prefix T_
%name Opt_Cdf_GeneralCss_
%declare_class {class Opt_Cdf_GeneralCss_Parser}

general_css			::= element_block.
element_block		::= single_element element_block.
element_block		::= single_element.

element_block		::= single_rule.
element_block		::= single_macro.

single_macro		::= macro_name macro_arguments SEMICOLON.
single_macro		::= macro_name macro_arguments rule_block SEMICOLON.
single_macro		::= macro_name rule_block SEMICOLON.

macro_name			::= AT IDENTIFIER.
macro_arguments		::= value macro_arguments.
macro_arguments		::= value.

rule_block			::= LCURBRACKET rule_block_content RCURBRACKET.
rule_block_content	::= single_rule rule_block_content.
rule_block_content	::= single_rule.

single_rule			::= identifiers_list LCURBRACKET rule_content RCURBRACKET.
rule_content		::= single_property SEMICOLON rule_content.
rule_content		::= single_property SEMICOLON.
rule_content		::= single_property.

single_property		::= PROP_ID COLON value.