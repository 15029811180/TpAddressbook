<?php
	header("Content-type:text/html;charset=utf8");
	$name=$_POST['name'];
	//查询数据库的内容
	//1.连接数据库
	$link=@mysqli_connect('localhost','root','') or die('连接数据库失败');
	//2.选择数据库
	mysqli_select_db($link,'txl') or die('选择数据库失败');
	//3.设置字符集
	mysqli_set_charset($link,'utf8');
	/******************加入分页技术*********************/
	//求出总页数
	$sql="SELECT * FROM {$name}";
	$result=mysqli_query($link,$sql);
	$total=mysqli_num_rows($result);
	//包含分页类
	include './Page.class.php';
	$page=new Page($total,5);
	$limit="LIMIT ".$page->limit();
	/*******************分页效果结束********************/
	//4.准备查询的SQL语句
	$sql="SELECT * FROM {$name}";
	//5.发送SQL语句
	$result=mysqli_query($link,$sql);
	//6.判断并且处理结果
	if ($result &&mysqli_num_rows($result)>0) {
		//查询到数据 遍历到表格中显示
		echo '<table border="0" align="left" width="1300">';
			echo '<tr>';
				echo '<td colspan="7" align="left"><h2>通讯录管理</h2></td>';
				echo '<td colspan="3" align="right"><a href="add.php">搜索</a> | <a href="user_info.php">添加</a> | <a href="add.php">修改密码</a> | <a href="index.html">注销</a></td>';
			echo '</tr>';
		echo '</table>';
		echo '<table border="0" cellspacing="0" align="center" width="1200">';
			echo '<tr bgcolor="white" height="20">';
			echo '</tr>';
			echo '<tr bgcolor="grey">';
				echo '<th>';
				echo '<th>姓名';
				echo '<th>性别';
				echo '<th>电话';
				echo '<th>添加时间';
				echo '<th>地址';
				echo '<th>操作';
			echo '</tr>';
			echo '<tr bgcolor="white" height="20">';
			echo '</tr>';
			//得到数据
			// $row=mysqli_fetch_assoc($result);
			// var_dump($row);
			while($row=mysqli_fetch_assoc($result)){
				echo '<tr align="center" bgcolor="silver">';
					echo'<td>'.$row['id'].'</td>';
					echo'<td>'.$row['username'].'</td>';
					echo'<td>'.$row['sex'].'</td>';
					echo'<td>'.$row['phone'].'</td>';
			 		echo '<td>'.date('Y-m-d H:i:s',$row['addtime']).'</td>';
					echo'<td>'.$row['address'].'</td>';
					echo'<td><a href="user_info.php?id='.$row['id'].'">修改</a> | <a href="del_action.php?id='.$row['id'].'">删除</a></td>';
				echo '</tr>';
			echo '<tr bgcolor="white" height="20">';
			echo '</tr>';
					}

		//添加分页效果
		echo '<tr bgcolor="white">';
			echo '<td colspan="2" align="center"><a href="add.php">添加会员</a></td>';
			echo '<td colspan="1" align="center"><a href="add.php">删除</a></td>';
			echo '<td align="right" colspan="4">';
				//显示分页
				echo $page->showPage();
			echo '</td>';
		echo '</tr>';
	} else {
		echo '<td colspan="3" align="center"><a href="add.php">添加</a> |</td>';
	}
	//7.关闭数据库
	mysqli_close($link);
	