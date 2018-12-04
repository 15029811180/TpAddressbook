<?php
	//接受上一个页面传过来的内容 并且 判断上一个页面要做哪一个操作
	var_dump($_GET);
	// var_dump($_POST);
	// var_dump($_FILES);
	// var_dump($_POST);
	// 1.判断用户的操作
	switch ($_GET['a']) {
		case 'add':
		//判断两次密码是否一致
			if ($_POST['pwd']!=$_POST['repwd']) {
				echo '<script>alert("两次密码不一致");location="./add.php"</script>';
				die();
			}
			//如果两次密码一致 拼接SQL语句
			$time=time();
			$pwd=md5($_POST['pwd']);
			$sql="INSERT INTO user(username,pwd) VALUE ('{$_POST['username']}','{$pwd}')";
			//调用添加函数
			add($sql);
			break;
		case 'update'://修改操作
			// var_dump($_POST);
			//1.判断是否修改密码
			if (empty($_POST['pwd']) && empty($_POST['repwd'])) {
				//没有修改密码
				$sql="UPDATE user SET username='{$_POST['username']}',level='{$_POST['level']}' WHERE id={$_POST['id']}";
			}else{
				//修改密码
				if ($_POST['pwd']!=$_POST['repwd']) {
					echo '<script>alert("两次密码不一致");location="./add.php"</script>';
					die();
				}
				$pwd=md5($_POST['pwd']);
				$sql="UPDATE user SET username='{$_POST['username']}',pwd='{$pwd}',level='{$_POST['level']}' WHERE id={$_POST['id']}";
			}
			//调用修改函数
			update($sql);
			break;
		case 'del'://删除操作
			if (isset($_GET['id'])) {
				//定义删除的SQL语句
				$sql="DELETE FROM user WHERE id={$_GET['id']}";
				//调用删除函数
				del($sql);
			}
		case 'addinfo';
			//用户要做添加会员详细操作
			//1.需要做文件上传
			include './upload_func.php';
			$result=upload('pic','./upload');
				//如果文件上传函数执行成功 则获取上传的图片名称
			if (is_array($result)) {
				$pic=$result['name'];
			}else{
				//如果上传头像没有成功 执行添加默认头像做头像
				$pic='./moren.jpg';
			}
			//将获得的爱好数组 转换成字符串
			$hobby=implode(',',$_POST['hobby']);
			//拼接SQl语句
			$sql="INSERT INTO user_info(uid,name,pic,age,sex,hobby,xueli,ysr,hf,address,phone,email) VALUES('{$_POST['uid']}','{$_POST['name']}','{$pic}','{$_POST['age']}','{$_POST['sex']}','{$_POST['hobby']}','{$_POST['xueli']}','{$_POST['ysr']}','{$_POST['hf']}','{$_POST['address']}','{$_POST['phone']}','{$_POST['email']}')";
			//调用添加详细信息的函数
			insert($sql);
			break;
		case 'updateinfo';
		// var_dump($_POST);
			//修改会员详细信息
			//注意：用户有可能修改会员图片 也有可能没有修改会员图片
			if (!empty($_FILES['pic']['name'])) {
				//有图片上传
				include './upload_func.php';
				$result=upload('pic','upload');
				if (is_array($result)) {
					$pic=",pic='{$result['name']}'";
					$_POST['pictrue']=false;
				}else{
					//若没有上传成功 则使用原图片名称
					$pic=",pic='{$_POST['ypic']}'";
					$_POST['pictrue']=true;
				}
			}else{
				$pic='';
				$_POST['pictrue']=true;
			}
			$hobby=implode(',',$_POST['hobby']);
			$sql="UPDATE user_info SET name='{$_POST['name']}',sex='{$_POST['sex']}'{$pic},age='{$_POST['age']}',xueli='{$_POST['xueli']}',ysr='{$_POST['ysr']}',hf='{$_POST['hf']}',phone='{$_POST['phone']}',email='{$_POST['email']}',address='{$_POST['address']}',hobby='{$hobby}' WHERE uid={$_POST['uid']}";
			// echo $sql;
			// exit;
			updateinfo($sql);
	}

	/*
	无论是做增删改查 都需要连接数据库 选择数据库 设置字符集
	 */
	//定义一个连接数据库函数
	function construct(){
		//包含数据库配置文件
		include './dbconfig.php';
		//连接数据库
		$link=@mysqli_connect(HOST,USER,PWD)or die('连接数据库失败');
		//选择数据库
		mysqli_select_db($link,DBNAME);
		//设置字符集
		mysqli_set_charset($link,CHARSET);
		//返回数据库连接标识
		return $link;
	}
	//定义修改会员详细信息的函数
	function updateinfo($sql){
		$link=construct();
		$result=mysqli_query($link,$sql);
		if ($result && mysqli_affected_rows($link)>0) {
			if ($_POST['pictrue']==false) {
				//如果用户上传图片 删除原图
				unlink('./upload/'.$_POST['ypic']);
			}
			//如果用户传入照片 将原图片删除
			echo '<script>alert("修改信息成功");location="./user_info.php?id='.$_POST['uid'].'&username='.$_POST['username'].'"</script>';
		}else{
			echo '<script>alert("修改信息失败");location="./user_info.php?id='.$_POST['uid'].'&username='.$_POST['username'].'"</script>';
		}
		mysqli_close($link);
	}
	//定义添加详细信息的函数
	function insert($sql){
		$link=construct();
		$result=mysqli_query($link,$sql);
		if ($result && mysqli_affected_rows($link)>0) {
			echo '<script>alert("添加信息成功");location="./user_info.php?id='.$_POST['uid'].'&username='.$_POST['username'].'"</script>';
		}else{
			echo '<script>alert("添加信息失败");location="./user_info.php?id='.$_POST['uid'].'&username='.$_POST['username'].'"</script>';
		}
		mysqli_close($link);
	}
	//定义修改函数
	function update($sql){
		$link=construct();
		$result=mysqli_query($link,$sql);
		if ($result && mysqli_affected_rows($link)>0) {
			echo '<script>alert("修改成功");location="./select.php"</script>';
		}else{
			echo '<script>alert("修改失败");location="./add.php?id='.$_POST['id'].'"</script>';
		}
		mysqli_close($link);
	}
	//定义一个删除函数
	function del($sql){
		$link=construct();
		$sql_info="DELETE FROM user_info WHERE uid={$_GET['id']}";
		//执行删除附表的内容
		$result_info=mysqli_query($link,$sql_info);
		// if ($result_info && mysqli_affected_rows($link)>0) {
		//执行删除主表的内容
		$result=mysqli_query($link,$sql);
		if ($result && mysqli_affected_rows($link)>0) {
			echo '<script>alert("删除成功");location="./select.php"</script>';
		}else{
			echo '<script>alert("删除失败");location="./select.php"</script>';
			}	

		mysqli_close($link);
	}
	//制作添加函数
	function add($sql,$id=''){
			//调用连接数据库函数
			$link=construct();
			//发送SQL语句
			$result=mysqli_query($link,$sql);
			//判断并且处理结果
			if ($result && mysqli_affected_rows($link)>0) {
				//成功后跳转到查询页面
				echo '<script>alert("注册成功");location="./php_select.php"</script>';
			}else{
				echo '<script>alert("注册失败");location="./add.php"</script>';
			}
			//关闭数据库
			mysqli_close($link);
	}