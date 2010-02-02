%token_prefix T_
%name Opt_Cdf_
%declare_class {class Opt_Cdf_Parser}

%include_class
{
	/**
	 * The CDF loader object.
	 * @var Opt_Cdf_Loader
	 */
	private $_loader;

	/**
	 * Constructs the CDF parser.
	 *
	 * @param Opt_Cdf_Loader $expr The CDF loader used for parsing.
	 */
	public function __construct(Opt_Cdf_Loader $loader)
	{
		$this->_loader = $loader;
	} // end __construct();
}

%syntax_error {
	throw new Exception('Unexpected token '.$TOKEN);
}

cdf_file	::= block_list.
block_list	::= block_item.
block_list	::= block_item block_list.

block_item	::= element_selection(sel) block_body(body).	{	$this->_loader->_addDefinition(array(sel, body));	}

element_selection(res)	::= element_def(def).								{	res = array(def);	}
element_selection(res)	::= element_def(def) COMMA element_selection(sel).	{	sel[] = def; res = sel;	}

element_def(res)	::= item(i).					{	res = array(i);	}
element_def(res)	::= item(i) element_def(def).	{	def[] = i;	res = def;	}

item(res)	::= ID(s).						{	res = array(s, null);	}
item(res)	::= ID(s) HASH element_id(eid).	{	res = array(s, eid);	}

block_body(res)	::= LCURBRACKET body_rules(ruls) RCURBRACKET.	{	res = ruls;	}
block_body(res)	::= LCURBRACKET RCURBRACKET.	{	res = array();	}
body_rules(res)	::= single_rule(rul).					{	res = rul;	}
body_rules(res)	::= single_rule(rul) body_rules(ruls).	{	res = array_merge(rul, ruls);	}

single_rule(f)	::= property_id(pid) COLON format_id(fid) SEMICOLON.	{	f = array(pid => fid);	}

element_id(s)	::= ID(v).	{	s = v;	}
element_id(s)	::= ID(v) DOT element_id(r).	{	s = v.'.'.r;	}

property_id(s)	::= ID(v).	{	s = v;	}
property_id(s)	::= ID(v) PAUSE property_id(r).	{	s = v.'-'.r;	}

format_id(s)	::= ID(v).	{	s = v;	}
format_id(s)	::= ID(v) SLASH format_id(r).	{	s = v.'/'.r;	}