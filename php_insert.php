<?php
	header("Content-type:text/html;charset=utf8");
	//接收数据
	/*********进行添加数据库操作*********/
	//1.连接数据库 mysqli
	$link=@mysqli_connect('localhost','root','') or die('连接数据库失败');
	//2.选择数据库
	mysqli_select_db($link,'txl') or die('选择数据库失败');
	// 3.设置字符集
	mysqli_set_charset($link,'utf8');
	//4.准备操作的SQL语句 添加数据
	$time=time();
	$pwd=md5($_POST['pwd']);
	$sql="INSERT INTO user(name,phone,yzm,pwd,addtime) VALUES('{$_POST['name']}','{$_POST['phone']}','{$_POST['yzm']}','{$pwd}','{$time}')";
	//5.发送SQL语句
	$result=mysqli_query($link,$sql);
	//6.判断返回的结果
	if ($result && mysqli_affected_rows($link)>0) {
		//获取添加数据的id号
		$id=mysqli_insert_id($link);
		echo '<script>alert("添加数据成功,数据库的id号为:'.$id.'");location="./php_select.php"</script>';
	}else{
		echo '<script>alert("添加数据失败");location="./index.html"</script>';
	}
	//7.关闭数据库
	mysqli_close($link);