<?php

	// Actually this is my private pagination system
	// I was using it for tests, but feel free to use it,
	// if you don't mind the quite ugly code
	//                              --- Zyx ---

	class pageSystem implements ioptPagesystem{
		private $ppp;	    // Positions per page
		private $positions;	    // Count of positions
		private $active;	    // Active page
		private $pages;
		private $from_pos;	    // First position in the actual page.

		private $link;
		private $buffer;
		private $prev;

		public function __construct($positions, $ppp, $link)
		{
			$this -> ppp = $ppp;
			$this -> positions = $positions;
			$this -> link = $link;
			if(!isset($_GET['from']))
			{
				$this -> from_pos = 1;
			}
			else
			{
				if($_GET['from'] == 'last')
				{
					$_GET['goto'] = 'last';
				}
				$this -> from_pos = $_GET['from'];
			}
			
			$mod = $this -> positions % $this -> ppp;
			$a	 = $this -> positions - $mod;

			$b	 = $a / $this -> ppp;

			if($mod > 0){
				$c = 1;
			}else{
				$c = 0;
			}
			$this -> pages = $b + $c;
			
			if($this -> from_pos > $this -> pages){
				$this -> from_pos = $this -> pages;
			}
			if($this -> from_pos < 1){
				$this -> from_pos = 1;
			}
		} // end __construct();
		
		public function parseGoto()
		{
			if(isset($_GET['goto'])){
				if($_GET['goto'] == 'last')
				{
					$this -> from_pos = $this -> pages;
				}
				else
				{
					$this -> from_pos = 1;
				}
			}
		} // end parseGoto();

		public function startPos()
		{
			return (($this -> from_pos - 1) * $this -> ppp);
		} // end startPos();

		public function endPos()
		{
			return $this -> from_pos * $this -> ppp;
		} // end endPos();
		
		public function limitClause()
		{
			return 'LIMIT '.(($this -> from_pos - 1) * $this -> ppp).', '.$this->ppp;
		} // end limitClause();
		
		public function count()
		{
			return $this -> pages;
		} // end pageCount();
		
		public function positions(){
			/*if($this -> from_pos + $this -> ppp > $this -> positions){
				return $this -> positions + 1;
			}else{
				return $this -> from_pos + $this -> ppp;
			} */
		} // end positions();
		
		public function active()
		{
			return $this -> from_pos;		
		} // end activePage();
		
		private function l($page)
		{
			if(strpos($this -> link, '?') == 0)
			{
				return $this->link.'?from='.$page;
			}
			else
			{
				return $this -> link.'&amp;from='.$page;
			}

		} // end l();

		public function getPage()
		{
			if(!is_array($this -> buffer))
			{
				$this -> prepare(); // generate the list of pages, if it does not exist
			}
			$page = key($this -> buffer); // try to get the next page
			if(!is_null($page))
			{
				if($this -> prev + 1 != $page) // there is a hole in the page numbers, insert the separator here.
				{
					$this -> prev = $page - 1;
					return array('t' => 2, 'p' => 0, 'l' => '');
				}

				$this -> prev = $page;
				next($this->buffer); // jump to the next page
				return array('t' => ($page == $this->from_pos ? 1 : 0), // return the page, checking whether it's the active one
					'p' => $page,
					'l' => $this -> l($page)
				);
			}
		} // end getPage();

		public function nextPage()
		{
			if($this -> from_pos < $this -> pages)
			{
				return array('t' => 0, 'p' => $this->from_pos+1, 'l' => $this->l($this->from_pos+1));
			}
		} // end nextPage();

		public function prevPage()
		{
			if($this -> from_pos > 1)
			{
				return array('t' => 0, 'p' => $this->from_pos-1, 'l' => $this->l($this->from_pos-1));
			}
		} // end prevPage();

		public function firstPage()
		{
			if($this -> pages > 1)
			{
				return array('t' => 0, 'p' => 1, 'l' => $this->l(1));
			}
		} // end firstPage();

		public function lastPage()
		{
			if($this -> pages > 1)
			{
				return array('t' => 0, 'p' => $this->pages, 'l' => $this->l($this->pages));
			}
		} // end lastPage();

		private function x($page)
		{
			if($page >= 1 && $page <= $this -> pages)
			{
				$this -> buffer[$page] = 1;
			}
		} // end x();
		
		public function prepare()
		{
			$this -> buffer = array();
			$html = '';
			if($this -> pages > 8)
			{
				$this -> buffer[1] = 1;
				for($i = $this -> from_pos - 3; $i < $this -> from_pos + 4; $i++)
				{
					$this -> x($i);				
				}
				$this -> buffer[$this -> pages] = 1;
			}
			else
			{
				for($i = 1; $i <= $this -> pages; $i++)
				{
					$this -> buffer[$i] = 1;
				}
			}
			reset($this -> buffer);
			$this -> prev = 0;
		} // end prepare();
	
	}

?>
