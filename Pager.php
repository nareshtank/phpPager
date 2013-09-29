<?php
require_once("CommonMethods.php");

class PagerClass {
	public function __construct($dbConn, $sql, $total, $webroot='', $fileName='', $options=array(), $seo_url=false) {

		$this->options=array('perPage'=>'10',
								'delta'=>'5',	#Page Links
								'separator'=>'	',
								'urlVar'=>'page',
								'sessionVar'=>'perpage',
								'linkClass'=>'inactive_page',
								'curPageLinkClassName'=>"active_page",
								'mode'=>'Sliding',	#Sliding /* Proposed: Jumping */
								'nextName'=>'Next',
								'prevName'=>'Previous'
							);

							
		
		if($fileName=='') {
			$this->fileName=substr($_SERVER['SCRIPT_NAME'],1);
		} else {
			$this->fileName=$fileName;
		}
		
		$this->dbConn = $dbConn;
		$this->sql=$sql;
		$this->webroot=$webroot;
		$this->total=$total;
		foreach($this->options as $key=>$value) {
			if(array_key_exists($key,$options)) {
				$this->$key=$options[$key];
			} else {
				$this->$key=$this->options[$key];
			}
		}
		$this->seo_url = $seo_url;
		$this->page=1;
		if(isset($_REQUEST[$this->urlVar])) $this->page=$_REQUEST[$this->urlVar];

		if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['search_pagging_cmb']) && $_POST['search_pagging_cmb']=='y') {
			$this->perPage=$_POST[$this->sessionVar];
			$this->page=1;
			$_SESSION['npager'][$this->sessionVar]=$this->perPage;
		} elseif(isset($_GET[$this->sessionVar])) {
			$this->perPage=$_GET[$this->sessionVar];
			$_SESSION['npager'][$this->sessionVar]=$this->perPage;
		} elseif(isset($_SESSION['npager'][$this->sessionVar])) {
			$this->perPage=$_SESSION['npager'][$this->sessionVar];
		}
		if($this->perPage==0) $this->perPage=$this->options['perPage'];
		
		$this->qryStr=$_SERVER['QUERY_STRING'];
		if($this->qryStr!='') {
			$a=explode('&',$this->qryStr);
			foreach($a as $k=>$v) {
				if(substr($v,0,strlen($this->urlVar))==$this->urlVar) {
					unset($a[$k]);
				}
			}
			$this->qryStr=implode('&',$a);
			$this->qryStr='?'.$this->qryStr.'&';
		} else {
			$this->qryStr='?';
		}
		if($this->seo_url == true)
		{
			$this->qryStr = str_replace("?","",$this->qryStr);
			$data_arr = explode("&", $this->qryStr);
			foreach($data_arr as $key=>$value)
			{
				if($value != '')
				{
					$value_arr = explode("=", $value);
					if($value_arr[0]!=$this->urlVar)
					{
						$value_str[] = $value_arr[1];
					}
				}
			}
			if(!empty($value_str))
			{
				$this->qryStr = implode("/", $value_str);
			}
		}		
		
		$this->upperLimit=$this->perPage*$this->page;
		$this->lowerLimit=$this->upperLimit-$this->perPage;
		$this->totalPage=ceil($this->total/$this->perPage);
	}
	
	public function getDataArray() {
		$this->limit='LIMIT '.$this->lowerLimit.', '.$this->perPage;
		$this->rows=executeJoinQuery($this->sql.' '.$this->limit);
		return $this->rows;
	}

	public function getTotalPages() {
		return $this->totalPage;
	}
	
	public function getCurrentPage() {
		if($this->totalPage=='0') return '0';
		return $this->page;
	}

	public function getFirst($n=false) {
		if($n) {
			if($this->seo_url == false)
			{
				$this->firstPage='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'=1" title="First Page" class="'.$this->linkClass.'">1</a>';
			}
			else
			{
				$this->firstPage='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".'1" title="First Page" class="'.$this->linkClass.'">1</a>';
			}
		} elseif($this->lowerLimit) {
			if($this->seo_url == false)
			{
				$this->firstPage='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'=1" title="First Page" class="'.$this->linkClass.'">1</a>';
			}
			else
			{
				$this->firstPage='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".'1" title="First Page" class="'.$this->linkClass.'">1</a>';
			}
		} else {
			$this->firstPage='';
		}
		return $this->firstPage;
	}
	
	public function getLast($n=false) {
		if($n) {
			if($this->seo_url == false)
			{
				$this->lastPage='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'='.$this->totalPage.'" title="Last Page" class="'.$this->linkClass.'">'.$this->totalPage.'</a>';
			}
			else
			{
				$this->lastPage='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".$this->totalPage.'" title="Last Page" class="'.$this->linkClass.'">'.$this->totalPage.'</a>';
			}
		} elseif($this->total>$this->upperLimit) {
			if($this->seo_url == false)
			{
				$this->lastPage='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'='.$this->totalPage.'" title="Last Page" class="'.$this->linkClass.'">'.$this->totalPage.'</a>';
			}
			else
			{
				$this->lastPage='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".$this->totalPage.'" title="Last Page" class="'.$this->linkClass.'">'.$this->totalPage.'</a>';
			}
		} else {
			$this->lastPage='';
		}
		return $this->lastPage;
	}

	public function getPreviousLink() {
		$this->prevLink='';
		if($this->page!='1') {
			if($this->seo_url == false)
			{
				$this->prevLink='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'='.($this->page-1).'" title="Previous Page" class="'.$this->linkClass.'">'.$this->prevName.'</a>';
			}
			else
			{
				$this->prevLink='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".($this->page-1).'" title="Previous Page" class="'.$this->linkClass.'">'.$this->prevName.'</a>';
			}
		}
		return $this->prevLink;
	}
	
	public function getNextLink() {
		$this->nextLink='';
		if($this->page<$this->totalPage) {
			if($this->seo_url == false)
			{
				$this->nextLink='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'='.($this->page+1).'" title="Next Page" class="'.$this->linkClass.'">'.$this->nextName.'</a>';
			}
			else
			{
				$this->nextLink='<a href="'.$this->webroot.$this->fileName."/".$this->qryStr."/".($this->page+1).'" title="Next Page" class="'.$this->linkClass.'">'.$this->nextName.'</a>';
			}
		}
		return $this->nextLink;
	}

	public function getLinks($option='') {
		$this->links=array();
		$fl=$this->page-floor($this->delta/2);
		if($fl<=0) {
			$fl=1;
		}
		$ll=$this->delta+$fl-1;
		if($ll>$this->totalPage) {
			$k=$ll-$this->totalPage;
			$fl=$fl-$k;
			$ll=$this->totalPage;
		}
		if($fl<=0) {
			$fl=1;
		}
		
		if($option=='all') {
			if($this->totalPage<=1) return '';
		}
		
		if($option=='all') {
			if($this->getFirst()!='') $this->links[]='['.$this->getFirst().']';
			if($this->getPreviousLink()!='') $this->links[]=$this->getPreviousLink();
		}

		if($this->seo_url == false)
		{
			for($i=$fl;$i<=$ll;$i++) {
				if($i!=$this->page) {
					$this->links[]='<a href="'.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'='.$i.'" title="page-'.$i.'" class="'.$this->linkClass.'">'.$i.'</a>';
				} else {
					$this->links[]='<span class="'.$this->curPageLinkClassName.'" title="page-'.$i.'">'.$i.'</span>';
				}
			}
		}
		else
		{
			for($i=$fl;$i<=$ll;$i++) {
				
				if($i!=$this->page) {
					$this->links[]='<a href="'.$this->webroot.$this->fileName.'/'.$this->qryStr.'/'.$i.'" title="page-'.$i.'" class="'.$this->linkClass.'">'.$i.'</a>';
				} else {
					$this->links[]='<span class="'.$this->curPageLinkClassName.'" title="page-'.$i.'">'.$i.'</span>';
				}
			}
		}	
			
		if($option=='all') {
			if($this->getNextLink()!='') $this->links[]=$this->getNextLink();
			if($this->getLast()!='') $this->links[]='['.$this->getLast().']';
		}

		$this->links=implode($this->separator,$this->links);

		return $this->links;
	}

	public function getDropDown($start=5,$end=30,$step=5,$showAllData=true,$attributes=array()) {
		$this->dropDown='';
		
		$tmp='';
		if(array_key_exists('id',$attributes)) {
			$tmp.=' id="'.$attributes['id'].'"';
		}
		if(array_key_exists('class',$attributes)) {
			$tmp.=' class="'.$attributes['class'].'"';
		}
		$onchange='javascript:location.href=\''.$this->webroot.$this->fileName.$this->qryStr.$this->urlVar.'=1&'.$this->sessionVar.'=\'+this.value+\'\'';
		if(array_key_exists('onchange',$attributes)) {
			$onchange=$attributes['onchange'];
		}
		
		$this->dropDown.='<select onchange="'.$onchange.'" name="'.$this->sessionVar.'" '.$tmp.'>';
		$a=array();
		for($i=$start;$i<=$end;$i=$i+$step) {
			$this->dropDown.='<option value="'.$i.'" '.m__writeselected($this->perPage,$i).'>'.$i.'</option>';
			array_push($a,$i);
		}
		if($showAllData) {
			if(in_array($this->perPage,$a)) $this->dropDown.='<option value="'.$this->total.'" >'.$this->total.'</option>';
			else $this->dropDown.='<option value="'.$this->total.'" selected="selected" >'.$this->total.'</option>';
		}
		$this->dropDown.='</select>';
		return $this->dropDown;
	}
}
?>
