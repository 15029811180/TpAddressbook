<?php
	//添加用户
	header("Content-type:text/html;charset=utf8");
	include './dbconfig.php';
	include './Model.class.php';
	$m=new Model('user');
	$_POST=array('user'=>'卡哇伊','sex'=>0,'age'=>20,'nvshen'=>'土豆','id'=>2);
	$result=$m->insert($_POST);
	var_dump($result);

	echo $m->sql;

	//删除数据
	include './dbconfig.php';
	include './Model.class.php';
	$m=new Model('user');
	$result=$m->where('id=')->delete();
	var_dump($result);
	echo $m->$sql;

	//数据修改
	include './dbconfig.php';
	include './Model.class.php';
	$m=new Model('user');
	$_POST=array('user'=>'卡哇伊','sex'=>0,'age'=>20,'nvshen'=>'土豆','id'=>2);
	$result=$m->update($_POST);
	var_dump($result);


	//查询条件
	include './dbconfig.php';
	include './Model.class.php';
	$m=new Model('user');
	$result=$m->where('id>10')->limit(5,5)->order('id ASC')->field('id','age','sex','memeda')->select();
	var_dump($result);
	$result=$m->where('id>10')->order('id desc')->select();
	var_dump($result);
