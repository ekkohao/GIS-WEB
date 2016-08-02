<?php
class pagination {
private $total;      //总记录  
private $pgnsize;    //每页显示多少条  
public $limit;          //limit  
private $page;           //当前页码  
private $pagenum;      //总页码  
private $url;           //地址  
private $bothnum;      //两边保持数字分页的量  
public function __construct($_total,$_pgnsize=0){
	$this->total = $_total ? $_total : 1;
	$this->pgnsize = ($_pgnsize==0)?PGN_SIZE:$_pgnsize;
	$this->pgnnum = ceil($this->total / $this->pgnsize);  
	$this->pgn = $this->setPgn();
	$this->limit = "LIMIT ".($this->pgn-1)*$this->pgnsize.",$this->pgnsize";
	$this->url = $this->setUrl();
	$this->bothnum = 2;
}
//获取当前页码
private function setPgn() {  
	if (empty($_GET['pgn'])||!is_numeric($_GET['pgn']))
		return 1;
	$t= min(max(1,$_GET['pgn']),$this->pgnnum);
	return $t;
}   

//获取地址  
private function setUrl() {  
	$_url = $_SERVER["REQUEST_URI"];  
	$_par = parse_url($_url);
	if (isset($_par['query'])) {  
		parse_str($_par['query'],$_query);  
		unset($_query['pgn']);  
		$_url = $_par['path'].'?'.http_build_query($_query);  
	}
	return $_url;  
}     //数字目录
private function pgnList() {  
	$_pgnlist='';
	for ($i=$this->bothnum;$i>=1;$i--) {  
		$_pgn = $this->pgn-$i;  
		if ($_pgn < 1) continue;  
		$_pgnlist .= '<li><a href="'.$this->url.'&pgn='.$_pgn.'">'.$_pgn.'</a></li>';  
	}  
	$_pgnlist .= '<li><span class="me">'.$this->pgn.'</span></li>';  
	for ($i=1;$i<=$this->bothnum;$i++) {  
		$_pgn = $this->pgn+$i;  
		if ($_pgn > $this->pgnnum) break;  
		$_pgnlist .= '<li><a href="'.$this->url.'&pgn='.$_pgn.'">'.$_pgn.'</a></li>';  
	}  
	return $_pgnlist;  
}  

//首页  
private function first() {  
	if ($this->pgn > $this->bothnum+1) {  
		return ' <li><a href="'.$this->url.'">1</a></li><li><span>...</span></li>';  
	}  
	return '';
}  

//上一页  
private function prev() {  
	if ($this->pgn == 1) {  
		return '<li><span class="disabled">上一页</span></li>';  
	}  
	return '<li><a href="'.$this->url.'&pgn='.($this->pgn-1).'">上一页</a></li>';  
}  

//下一页  
private function next() {  
	if ($this->pgn == $this->pgnnum) {  
		return '<li><span class="disabled">下一页</span></li>';  
	}  
	return '<li><a href="'.$this->url.'&pgn='.($this->pgn+1).'">下一页</a></li>';  
}  

//尾页  
private function last() {  
	if ($this->pgnnum - $this->pgn > $this->bothnum) {  
		return '<li><span>...</span></li><li><a href="'.$this->url.'&pgn='.$this->pgnnum.'">'.$this->pgnnum.'</a></li>';  
	} 
	return ''; 
}  

//分页信息  
public function showpgn() {
	if($this->pgnnum<=1)
		return null;
	$_pgn = '<ul class="pagination">';
	$_pgn .= $this->prev();
	$_pgn .= $this->first();  
	$_pgn .= $this->pgnList();  
	$_pgn .= $this->last();     
	$_pgn .= $this->next();
	$_pgn .='</ul>';
	return $_pgn;  
}  
}
?>