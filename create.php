<?php
	header("Content-type:text/html;charset=utf8");
	//判断两次密码是否一致
	if ($_POST['pwd']!=$_POST['repwd']) {
		echo '<script>alert("两次密码不一致,请重新输入!");location="./add.php"</script>';
		die();
	}else{
	include './dbconfig.php';
	include './Model.class.php';
	$m=new Model('txl');
	$result=$m->create($_POST);
	if ($result>0) {
				echo '<script>alert("注册失败");location="./add.php"</script>';
			}else{
				echo '<script>alert("注册成功");location="./php_select.php"</script>';
			}
}