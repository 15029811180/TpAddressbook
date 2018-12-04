<?php
	//定义权限默认选中的变量
	$pu=$vip=$jin=$chao='';
	//本页面需要实现两个功能 既实现添加会员 也要实现 修改会员时显示会员原数据内容
	//1.如果该页面是点击修改传过来的 则会传入id
	if (isset($_GET['id'])) {
		$str='修改会员';
		$submit='修改';
		$action='update';
		$hidden='<input type="hidden" name="id" value="'.$_GET['id'].'">';
		include './dbconfig.php';
		$link=@mysqli_connect(HOST,USER,PWD,DBNAME)or die('链接或选择数据库失败');
		mysqli_set_charset($link,CHARSET);
		//准备查询一条SQL语句
		$sql="SELECT * FROM user WHERE id={$_GET['id']}";
		$result=mysqli_query($link,$sql);
		if ($result && mysqli_num_rows($result)>0) {
			//返回关联数组
			$row=mysqli_fetch_assoc($result);
			}
			if (isset($row['level'])) {
				switch ($row['level']) {
					case '0':
						$pu='selected';
						break;
					case '1':
						$vip='selected';
						break;
					case '2':
						$jin='selected';
						break;
					case '3':
						$chao='selected';
						break;
				}
			}
	}else{
		$str='用户注册';
		$submit='添加';
		$action='add';
		$hidden='';
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
	<center>
		<h1><?=$str?></h1>
		<form action="useModel.php?a=<?=$action;?>" method="post">
			用&nbsp;&nbsp;户&nbsp;&nbsp;名:
			<input type="text" name="username" value="<?php echo isset($row['username'])?$row['username']:'' ?>"/><br/>
			密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码:
			<input type="password" name="pwd" value=""/><br/>
			确认密码:
			<input type="password" name="repwd" value=""/><br/><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="<?=$submit?>"  style="width: 178px;height: 20px;" />
		</form>
	</center>
</body>
</html>