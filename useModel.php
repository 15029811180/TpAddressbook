<?php
	header("Content-type:text/html;charset=utf8");
	//判断两次密码是否一致
	if ($_POST['pwd']!=$_POST['repwd']) {
		echo '<script>alert("两次密码不一致,请重新输入!");location="./add.php"</script>';
		die();
	}else{
	include './dbconfig.php';
	include './Model.class.php';
	//添加用户
	$m=new Model('user');
	$result=$m->insert($_POST);
	if ($result>0) {
		echo '<script>alert("添加成功");location="./php_select.php"</script>';
	}else{
		echo '<script>alert("添加失败");location="./add.php"</script>';
	}
	//创建用户数据表
	$m=new Model('txl');
	$result=$m->create($_POST);
	var_dump($result);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<style type="text/css">
	</style>
</head>
<body>
		<form action="text.php" method="post">
			<!-- 隐藏于提交会员名 -->
			<input type="hidden" name="name" value="<?=$_POST['username']?>"/>
		</form>
		</body>
</html>
	// echo $m->sql;
	
	// $m->where(' id=1 || user"aa"')->delete();
	//$m->delete($_GET['id']);
	
	//$result=$m->delete();
	//$result=$m->where('id=37')->delete();
	//var_dump($result);
	//echo $m->sql;
	// $_POST=array('user'=>'卡哇伊','sex'=>0,'age'=>20,'nvshen'=>'土豆','id'=>2);
	// $result=$m->where('id=35')->update($_POST);
	// var_dump($result);
	// $result=$m->update($_POST);
	// var_dump($result);

	//$sql="UPDATE user SET user= ,sex= ,age=  where id=1";
	//
	//查询条件
	// $result=$m->where('id>10')->limit(5,5)->order('id ASC')->field('id','age','sex','memeda')->select();
	// // var_dump($result);

	// // $result=$m->where('id>10')->order('id desc')->select();
	// // var_dump($result);
	// //
	// //获取总条数
	// $total=$m->where('id>10')->total();
	// var_dump($total);

	// $total=$m->total();
	// var_dump($total);


	// //查询单条数据
	// $row=$m->where('id=2')->field('id','age','user')->find();
	// var_dump($row);