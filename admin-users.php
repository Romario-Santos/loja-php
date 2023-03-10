<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;


$app->get("/admin/users/:iduser/delete",function($iduser){
	User::verifylogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;
});

$app->get("/admin/users",function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$users = User::listAll();
	$page->setTpl("users",["users"=>$users]);
});

$app->get("/admin/users/create",function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
});

$app->post("/admin/users/create",function(){
	User::verifyLogin();
	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

	$user->setData($_POST);
	$user->save();

	header("Location: /admin/users");
	exit;
	
});

$app->get("/admin/users/:iduser",function($iduser){
	User::verifyLogin();
	
	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update",["user"=>$user->getValues()]);
});

$app->post("/admin/users/:iduser",function($iduser){
	User::verifyLogin();
	$user = new user();

	$user->get($iduser);
	
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
	
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
	
});