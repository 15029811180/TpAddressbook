<?php
	header("Content-type:text/html;charset=utf8");
	include './dbconfig.php';
	include './Model.class.php';
	//添加用户
	$m=new Model('谭俊鹏');
	$result=$m->insert($_POST);
	echo $m->sql;
	if ($result>0) {
		echo '<script>alert("添加成功");location="./php_select.php"</script>';
	}else{
		echo '<script>alert("添加失败");location="./user_info.php"</script>';
	}


?>
