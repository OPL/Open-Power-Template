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
 * $Id$
 */

/**
 * The CDF parser for OPT. Note: do not edit this file
 * manually. It was generated by PHP Parser Generator by Gregory Beaver.
 * Instead, use the file /tools/lexer/cdf_parser.y and parse
 * it with /tools/lexer/generateExpression.php.
 */
class Opt_Cdf_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof Opt_Cdf_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof Opt_Cdf_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->_string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof Opt_Cdf_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof Opt_Cdf_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

class Opt_Cdf_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};


#line 3 "cdf_parser.y"
class Opt_Cdf_Parser#line 79 "cdf_parser.php"
{
#line 6 "cdf_parser.y"

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
#line 99 "cdf_parser.php"

    const T_COMMA                          =  1;
    const T_ID                             =  2;
    const T_HASH                           =  3;
    const T_LCURBRACKET                    =  4;
    const T_RCURBRACKET                    =  5;
    const T_COLON                          =  6;
    const T_SEMICOLON                      =  7;
    const T_DOT                            =  8;
    const T_PAUSE                          =  9;
    const T_SLASH                          = 10;
    const YY_NO_ACTION = 56;
    const YY_ACCEPT_ACTION = 55;
    const YY_ERROR_ACTION = 54;

    const YY_SZ_ACTTAB = 47;
static public $yy_action = array(
 /*     0 */    55,   31,    1,    9,    6,   15,    5,   24,    1,    9,
 /*    10 */    23,   15,    5,   30,    3,   18,   19,    3,   18,   26,
 /*    20 */    12,   15,    5,   32,   25,    5,    8,   14,   17,   11,
 /*    30 */     4,   27,   16,   13,   10,   12,    7,   21,   22,   29,
 /*    40 */     2,   47,   28,   47,   47,   47,   20,
    );
    static public $yy_lookahead = array(
 /*     0 */    12,   13,   14,   15,    9,   17,   18,   13,   14,   15,
 /*    10 */    19,   17,   18,   20,   21,   22,   20,   21,   22,   15,
 /*    20 */     2,   17,   18,    5,   17,   18,    3,    2,   23,    8,
 /*    30 */     1,    7,    2,    2,    6,    2,   10,    5,   16,   22,
 /*    40 */     4,   24,   23,   24,   24,   24,   19,
);
    const YY_SHIFT_USE_DFLT = -6;
    const YY_SHIFT_MAX = 19;
    static public $yy_shift_ofst = array(
 /*     0 */    31,   31,   18,   33,   31,   31,   33,   30,   25,   36,
 /*    10 */    30,   25,   -5,   23,   21,   29,   26,   24,   28,   32,
);
    const YY_REDUCE_USE_DFLT = -13;
    const YY_REDUCE_MAX = 11;
    static public $yy_reduce_ofst = array(
 /*     0 */   -12,   -6,   -4,   -7,    4,    7,   17,   19,   27,   22,
 /*    10 */     5,   -9,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(2, ),
        /* 1 */ array(2, ),
        /* 2 */ array(2, 5, ),
        /* 3 */ array(2, ),
        /* 4 */ array(2, ),
        /* 5 */ array(2, ),
        /* 6 */ array(2, ),
        /* 7 */ array(2, ),
        /* 8 */ array(2, ),
        /* 9 */ array(4, ),
        /* 10 */ array(2, ),
        /* 11 */ array(2, ),
        /* 12 */ array(9, ),
        /* 13 */ array(3, ),
        /* 14 */ array(8, ),
        /* 15 */ array(1, ),
        /* 16 */ array(10, ),
        /* 17 */ array(7, ),
        /* 18 */ array(6, ),
        /* 19 */ array(5, ),
        /* 20 */ array(),
        /* 21 */ array(),
        /* 22 */ array(),
        /* 23 */ array(),
        /* 24 */ array(),
        /* 25 */ array(),
        /* 26 */ array(),
        /* 27 */ array(),
        /* 28 */ array(),
        /* 29 */ array(),
        /* 30 */ array(),
        /* 31 */ array(),
        /* 32 */ array(),
);
    static public $yy_default = array(
 /*     0 */    54,   34,   54,   45,   54,   39,   54,   54,   54,   54,
 /*    10 */    54,   54,   50,   41,   48,   37,   52,   54,   54,   54,
 /*    20 */    42,   43,   36,   49,   35,   40,   38,   47,   53,   51,
 /*    30 */    46,   33,   44,
);
    const YYNOCODE = 25;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 33;
    const YYNRULE = 21;
    const YYERRORSYMBOL = 11;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    static public $yyFallback = array(
    );
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '<br>';
    }

    static public $yyTraceFILE;
    static public $yyTracePrompt;
    public $yyidx;                    /* Index of top element in stack */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    public $yystack = array();  /* The parser's stack */

    public $yyTokenName = array( 
  '$',             'COMMA',         'ID',            'HASH',        
  'LCURBRACKET',   'RCURBRACKET',   'COLON',         'SEMICOLON',   
  'DOT',           'PAUSE',         'SLASH',         'error',       
  'cdf_file',      'block_list',    'block_item',    'element_selection',
  'block_body',    'element_def',   'item',          'element_id',  
  'body_rules',    'single_rule',   'property_id',   'format_id',   
    );

    static public $yyRuleName = array(
 /*   0 */ "cdf_file ::= block_list",
 /*   1 */ "block_list ::= block_item",
 /*   2 */ "block_list ::= block_item block_list",
 /*   3 */ "block_item ::= element_selection block_body",
 /*   4 */ "element_selection ::= element_def",
 /*   5 */ "element_selection ::= element_def COMMA element_selection",
 /*   6 */ "element_def ::= item",
 /*   7 */ "element_def ::= item element_def",
 /*   8 */ "item ::= ID",
 /*   9 */ "item ::= ID HASH element_id",
 /*  10 */ "block_body ::= LCURBRACKET body_rules RCURBRACKET",
 /*  11 */ "block_body ::= LCURBRACKET RCURBRACKET",
 /*  12 */ "body_rules ::= single_rule",
 /*  13 */ "body_rules ::= single_rule body_rules",
 /*  14 */ "single_rule ::= property_id COLON format_id SEMICOLON",
 /*  15 */ "element_id ::= ID",
 /*  16 */ "element_id ::= ID DOT element_id",
 /*  17 */ "property_id ::= ID",
 /*  18 */ "property_id ::= ID PAUSE property_id",
 /*  19 */ "format_id ::= ID",
 /*  20 */ "format_id ::= ID SLASH format_id",
    );

    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count($this->yyTokenName)) {
            return $this->yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . $this->yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Opt_Cdf_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new Opt_Cdf_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

   function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        $this->yyTokenName[$iLookAhead] . " => " .
                        $this->yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            return;
        }
        $yytos = new Opt_Cdf_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    $this->yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    static public $yyRuleInfo = array(
  array( 'lhs' => 12, 'rhs' => 1 ),
  array( 'lhs' => 13, 'rhs' => 1 ),
  array( 'lhs' => 13, 'rhs' => 2 ),
  array( 'lhs' => 14, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 17, 'rhs' => 1 ),
  array( 'lhs' => 17, 'rhs' => 2 ),
  array( 'lhs' => 18, 'rhs' => 1 ),
  array( 'lhs' => 18, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 20, 'rhs' => 1 ),
  array( 'lhs' => 20, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 4 ),
  array( 'lhs' => 19, 'rhs' => 1 ),
  array( 'lhs' => 19, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 1 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 1 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
    );

    static public $yyReduceMap = array(
        3 => 3,
        4 => 4,
        6 => 4,
        5 => 5,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        15 => 12,
        17 => 12,
        19 => 12,
        13 => 13,
        14 => 14,
        16 => 16,
        18 => 18,
        20 => 20,
    );
#line 30 "cdf_parser.y"
    function yy_r3(){	$this->_loader->_addDefinition(array($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor));	    }
#line 566 "cdf_parser.php"
#line 32 "cdf_parser.y"
    function yy_r4(){	$this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);	    }
#line 569 "cdf_parser.php"
#line 33 "cdf_parser.y"
    function yy_r5(){	$this->yystack[$this->yyidx + 0]->minor[] = $this->yystack[$this->yyidx + -2]->minor; $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;	    }
#line 572 "cdf_parser.php"
#line 36 "cdf_parser.y"
    function yy_r7(){	$this->yystack[$this->yyidx + 0]->minor[] = $this->yystack[$this->yyidx + -1]->minor;	$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;	    }
#line 575 "cdf_parser.php"
#line 38 "cdf_parser.y"
    function yy_r8(){	$this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor, null);	    }
#line 578 "cdf_parser.php"
#line 39 "cdf_parser.y"
    function yy_r9(){	$this->_retvalue = array($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);	    }
#line 581 "cdf_parser.php"
#line 41 "cdf_parser.y"
    function yy_r10(){	$this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;	    }
#line 584 "cdf_parser.php"
#line 42 "cdf_parser.y"
    function yy_r11(){	$this->_retvalue = array();	    }
#line 587 "cdf_parser.php"
#line 43 "cdf_parser.y"
    function yy_r12(){	$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;	    }
#line 590 "cdf_parser.php"
#line 44 "cdf_parser.y"
    function yy_r13(){	$this->_retvalue = array_merge($this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);	    }
#line 593 "cdf_parser.php"
#line 46 "cdf_parser.y"
    function yy_r14(){	$this->_retvalue = array($this->yystack[$this->yyidx + -3]->minor => $this->yystack[$this->yyidx + -1]->minor);	    }
#line 596 "cdf_parser.php"
#line 49 "cdf_parser.y"
    function yy_r16(){	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.'.'.$this->yystack[$this->yyidx + 0]->minor;	    }
#line 599 "cdf_parser.php"
#line 52 "cdf_parser.y"
    function yy_r18(){	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.'-'.$this->yystack[$this->yyidx + 0]->minor;	    }
#line 602 "cdf_parser.php"
#line 55 "cdf_parser.y"
    function yy_r20(){	$this->_retvalue = $this->yystack[$this->yyidx + -2]->minor.'/'.$this->yystack[$this->yyidx + 0]->minor;	    }
#line 605 "cdf_parser.php"

    private $_retvalue;

    function yy_reduce($yyruleno)
    {
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new Opt_Cdf_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
    }

    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 22 "cdf_parser.y"

	throw new Exception('Unexpected token '.$TOKEN);
#line 666 "cdf_parser.php"
    }

    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
    }

    function doParse($yymajor, $yytokenvalue)
    {
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        if ($this->yyidx === null || $this->yyidx < 0) {
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new Opt_Cdf_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, $this->yyTokenName[$yymajor]);
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, $this->yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
