<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get("/admin", function() {
    
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("index");

});

$app->get("/admin/login", function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("login");
});

$app->post("/admin/login",function(){
	//var_dump($_POST);
	User::Login($_POST["deslogin"],$_POST["despassword"]);
	header("Location: /admin");
	exit;
});

$app->get("/admin/logout",function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});



//esqueci a senha

$app->get("/admin/forgot",function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot");
});

$app->post("/admin/forgot",function(){
	$user = User::getForgot($_POST['email']);	
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent",function(){
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");
});


//resetar a senha
$app->get("/admin/forgot/reset",function(){
    
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset",array(
		"name"=>$user["desperson"],
		"code"=>$_GET['code']
	));
});

$app->post("/admin/forgot/reset",function(){
//valida o codigo
	$forgot = User::validForgotDecrypt($_POST["code"]);
//seta o codigo como usado
	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");
});

//fim do esqueci a senha