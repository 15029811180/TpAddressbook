<?php
	class Model{
		//成员属性
		//数据库链接地址
		protected $host;
		//数据库用户名
		protected $user;
		//数据库密码
		protected $pwd;
		//要操作的数据库表名
		protected $tabName;
		//数据库表前缀
		protected $prefix;
		//要操作的数据库名
		protected $dbname;
		//字符集
		protected $charset;
		//数据库链接资源
		protected $link=null;
		//定义SQL语句
		private $sql;
		//定义缓存文件路径
		public $cache;
		//定义允许用户调用不存在的方法
		private $method=array('limit','where','order','field');
		//定义方法变量
		private $where;
		private $limit;
		private $order;
		private $field;
		//成员方法
		//1.构造方法 初始化成员属性 以及连接数据库
		function __construct($tabName='',$cache='./cache/'){
			//第一次初始化数据库配置类型
			$this->host=DB_HOST;
			$this->user=DB_USER;
			$this->pwd=DB_PWD;
			$this->charset=CHARSET;
			$this->prefix=DB_PREFIX;
			$this->dbname=DB_NAME;
			$this->cache=rtrim($cache,'/').'/';
			//判断是否传入表名，若传入表名则用传入的，若没有需要自己去获取表名
			if ($tabName=='') {
				//没有传入表名 需要从子承的类中获取表名
				// $a=get_class($this);
				// echo strtolower(substr($a,0,-5));
				$this->tabName=$this->prefix.strtolower(substr(get_class($this),0,-5));
			}else{
				//传入了表名
				$this->tabName=$this->prefix.$tabName;
			}
			//初始化数据库链接
			$this->link=$this->connect();
			// var_dump($this->link);
		}
		//6.获取数据表中合法字段
		private function getField(){
			//设置缓存文件路径
			$pathInfo=$this->cache.$this->tabName.'Cache.php';
			if (file_exists($pathInfo)) {
				return include $pathInfo;				
			}else{
				//准备SQL语句 去数据库中读取
				$sql="DESC ".$this->tabName;
				$result=$this->query($sql);
				//将数组中的文件名 写入到文件中保存
				return $this->writeField($result);
			}
		}
		//8.生成字段缓存的方法
		private function writeField(array $data){
			//判断缓存路径是否存在 不存在则创建
			if (!file_exists($this->cache)) {
				mkdir($this->cache);
			}
			//定义缓存文件路径以及名称
			$pathInfo=$this->cache.$this->tabName.'Cache.php';
			//定义一个数组
			$fields=array();
			//遍历数组 获取数组中的字段名 和主键以及自增键的字段名
			foreach ($data as $key => $val) {
				//获取主键
				if ($val['Key']=='PRI') {
					$fields['_pk']=$val['Field'];
				}
				//获取自增减
				if ($val['Extra']=='auto_increment') {
					$fields['_auto']=$val['Field'];
				}
				//获取字段名
				$fields[]=$val['Field'];
			}
			// var_dump($fields);
			//写入文件操作
			file_put_contents($pathInfo,"<?php\r\n return ".var_export($fields,true)."\r\n ?>");
			//返回数组
			return $fields;
			//原样输入 var_export
		}
		//7.定义发送查询数据库SQL语句的方法
		private function query($sql){
			//清空条件
			$this->clearWhere();
			$this->sql=$sql;
			//定义一个返回数据的数组
			$rows=array();
			$result=mysqli_query($this->link,$sql);
			if ($result && mysqli_num_rows($result)>0) {
				while ($row=mysqli_fetch_assoc($result)) {
					$rows[]=$row;
				}
				//返回的是一个二维数组
				return $rows;
			}else{
				return false;
			}
		}
		//2.定义数据库连接的方法
		private function connect(){
			//连接数据库
			$link=@mysqli_connect($this->host,$this->user,$this->pwd,$this->dbname);
			//判断
			if (!$link) {
				return '连接数据库失败';
			}
			//设置字符集
			mysqli_set_charset($link,$this->charset);
			//返回结果
			return $link;
		}
		//3.实现添加方法
		public function insert(array $data){
			// var_dump($data);
			//字段名变量
			$key='';
			//值变量
			$val='';
			//调用安全字段的方法
			$field=$this->getField();
			var_dump($field);
			//遍历数组 组装SQL语句
			foreach ($data as $k=>$v) {
				if (in_array($k,$field)) {
					$key.='`'.$k.'`,';
					$val.="'".$v."',";
				}
			}
			//删除最后的逗号
			$key=rtrim($key,',');
			$val=rtrim($val,',');
			//准备添加的SQL语句
			$sql="INSERT INTO {$this->tabName}({$key}) VALUES ($val)";
			// echo $sql;
			return $this->exec($sql);
		}
		//4.实现发送执行添加、删除、以及修改的SQL语句(增、删、改返回受影响)
		private function exec($sql){
			//保存SQL语句
			$this->sql=$sql;
			//清空条件
			$this->clearWhere();
			$result=mysqli_query($this->link,$sql);
			if ($result && mysqli_affected_rows($this->link)>0){
				//成功 如果有上一步操作的id 则返回上一步操作的id 如果没有则返回受影响行
				return mysqli_insert_id($this->link)?mysqli_insert_id($this->link):
					mysqli_affected_rows($this->link);
			}else{
				//失败
				return false;
			}
		}
		//5.定义一个魔术方法 为了访问私有成员属性开一个后门
		public function __get($name){
			if ($name=='sql') {
			echo $this->sql;
			}else{
				echo '不存在';
			}
		}
		//9.实现删除方法
		public function delete(){
			// echo $this->where;
			if (!empty($this->where)) {
				$where='WHERE '.$this->where;
			}else{
				$where='';
				//如果用户没有传入删除条件，自动判断get数组是否有主键，如果有则按该按键为删除键
				if (!empty($_GET)) {
					//先获取缓存字段
					$field=$this->getField();
					//在缓存字段中获取主键
					$id=$field['_pk'];
					foreach ($_GET as $k=>$v) {
						if ($id==$k) {
							$val=$v;
						}
					}
					$where='WHERE '.$id.'='.$val;
				}
			}
			$sql="DELETE FROM {$this->tabName}  {$where}";
			// echo $sql;
			return $this->exec($sql);
		}
		//11.修改数据的方法
		public function update(array $data){
			//获取缓存的字段 
			$field=$this->getField();
			$update='';
			foreach ($data as $k=>$v) {
				//过滤非法字段
				if(in_array($k,$field) && $k!=$field['_pk']){
					$update.='`'.$k.'`="'.$v.'",';
				}elseif($k==$field['_pk']){
					//要修改数据的数组中有主键的存在 将主键保存
					$con='`'.$k.'`="'.$v.'"';
				}
			}
			$update=rtrim($update,',');
			// echo $con;
			//判断是否有where条件
			if (!empty($this->where)) {
				$where=' WHERE '.$this->where;
			}else{
				$where=' WHERE '.$con;
			}
			//拼接SQL语句
			$sql="UPDATE {$this->tabName} SET {$update} {$where}";
			// echo $sql;
			return $this->exec($sql);
		}
		
		//10.定义一个魔术方法 call 调用一个不存在的方法时自动触发
		function __call($methodName,$args){
			//验证用户调用的方法是否是我预设的方法
			if (in_array($methodName,$this->method)){
				//判断用户是否调用where()方法
				if ($methodName=='where') {
					// var_dump($args);
					$this->where=isset($args[0])?$args[0]:'';
				}elseif ($methodName=='field') {
					//用户需要查询指定字段
					$this->field=$args;
				}elseif ($methodName=='limit') {
					if (count($args)>1) {
						$this->limit=$args[0].','.$args[1];
					}else{
						$this->limit=$args[0];
					}
				}elseif ($methodName=='order') {
					$this->order=$args[0];
				}
			}
			return $this;
		}
		//12.编写查询数据的方法 
		public function select(){
			$limit=$where=$order='';
			//判断是否传入limit条件
			if (!empty($this->limit)) {
				$limit='LIMIT '.$this->limit;
			}
			//判断是否传入order条件
			if (!empty($this->order)) {
				$order='ORDER BY '.$this->order;
			}
			//判断是否传入where条件
			if (!empty($this->where)) {
				$where='WHERE '.$this->where;
			}
			//判断用户是否需要查询指定的字段
			if (!empty($this->field)) {
				// var_dump($this->field);
				//获取合法字段
				$field=$this->getField();
				// var_dump($field);
				//过滤安全字段
				$hefa=array_intersect($this->field,$field);
				//将数组拼接成字符串
				$fields=implode(',',$hefa);
				// echo $fields;
			}else{
				$fields='*';
			}
			//拼接SQL语句
			$sql="SELECT {$fields} FROM {$this->tabName} {$where} {$order} {$limit}";
			//echo $sql;
			//返回二维数组
			return $this->query($sql);
		}
		//13.清空条件
		private function clearWhere(){
			$this->where='';
			$this->limit='';
			$this->field='';
			$this->order='';
		}
		//14.获取总条数
		public function total(){
			$where='';
			if (!empty($this->where)) {
				$where='WHERE '.$this->where;
			}
			//获取主键
			$field=$this->getField();
			if (isset($field['_pk'])) {
				$pk=$field['_pk'];
			}else{
				$pk='';
			}

			$sql="SELECT COUNT($pk) as total FROM {$this->tabName} {$where}";
			// echo $sql;
			return intval($this->query($sql)[0]['total']);
		}
		//15.获取单条数据
		public function find(){
			$where='';
			if (!empty($this->where)) {
				$where='WHERE '.$this->where;
			}
			//判断用户是否需要查询指定的字段
			if (!empty($this->field)) {
				//获取合法字段
				$field=$this->getField();
				// var_dump($field);
				//过滤安全字段
				$hefa=array_intersect($this->field,$field);
				//将数组拼接成字符串
				$fields=implode(',',$hefa);
			}else{
				$fields='*';
			}
			//拼接SQL语句
			$sql="SELECT {$fields} FROM {$this->tabName} {$where} LIMIT 1";
			// echo $sql;
			return $this->query($sql)[0];
			//var_dump($result);
		}
		//16.创建数据表并检验
		public function create(array $data){
			// var_dump($data);
			//调用安全字段的方法
			$name=$data['username'];
			$sql="CREATE TABLE {$name}(
 			id INT AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(18) NOT NULL,
 			sex VARCHAR(3) NOT NULL,
 			addtime INT UNSIGNED NOT NULL,
 			phone CHAR(11) NOT NULL,
 			address VARCHAR(255) NOT NULL)ENGINE=MYISAM DEFAULT CHARSET=UTF8;";
 			// echo $sql;
			$n=$this->exec($sql);
			var_dump($n);
			
		}
		//析构方法
		function __destruct(){
			if ($this->link!=null) {
				mysqli_close($this->link);
			}
		}
	}