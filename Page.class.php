<?php
	//该对象最后返回limit的两个参数 越过多少条 取出多少条
	
	class Page{
		//成员属性
		public $page=1;//当前页
		public $maxRows=0;//总条数
		public $pageSize=0;//每页显示条数
		public $maxPage=0;//总页数
		public $url=null;//当前页面的URL地址
		public $urlParam='';//当前页面的参数
		//成员方法
		//构造方法
		function __construct($maxRows,$pageSize=10){
			//进行初始化赋值
			$this->maxRows=$maxRows;//设置总条数
			$this->pageSize=$pageSize;//设置每页显示条数
			$this->page=isset($_GET['page'])?$_GET['page']:1;
			$this->url=$_SERVER['PHP_SELF'];//获取当前页面的URL地址
			//调用方法获取总页数
			$this->getMaxPage();
			//验证当前页的值
			$this->checkPage();
			//调用URL参数
			$this->urlParam();
		}
		//过滤当前url地址中的参数信息
		private function urlParam(){
			//便利请求中所有get参数
			foreach ($_GET as $k=>$v) {
				//判断参数值和参数名是否有效
				if ($v!='' && $k!='page') {
					$this->urlParam.='&'.$k.'='.$v;
				}
			}
		}
		//计算总页数
		private function getMaxPage(){
			$this->maxPage=ceil($this->maxRows/$this->pageSize);
		}
		//验证当前页
		private function checkPage(){
			if ($this->page>$this->maxPage) {
				$this->page=$this->maxPage;
			}
			if ($this->page<1) {
				$this->page=1;
			}
		}
		//输出页码
		public function showPage(){
			$str='';
			$str.='当前第'.$this->page.'/'.$this->maxPage.'总共'.$this->maxRows.'条&nbsp;';
			$str.='<a href="'.$this->url.'?page='.$this->urlParam.'">首页</a>&nbsp;&nbsp;';
			$str.='<a href="'.$this->url.'?page='.($this->page-1).$this->urlParam.'">上一页</a>&nbsp;&nbsp;';
			$str.='<a href="'.$this->url.'?page='.($this->page+1).$this->urlParam.'">下一页</a>&nbsp;&nbsp;';
			$str.='<a href="'.$this->url.'?page='.$this->maxPage.$this->urlParam.'">尾页</a>&nbsp;&nbsp;';
			return $str;
		}
		//返回分页的LIMIT条件
		public function limit(){
			$num=($this->page-1)*$this->pageSize;
			$limit=$num.','.$this->pageSize;
			return $limit;
		}
	}

	// $page=new Page(50,10);
	// echo $page->limit();

	// echo $page->showPage();