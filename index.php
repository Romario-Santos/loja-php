<?php 
session_start();
require_once("vendor/autoload.php");
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");

});

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

//crud de usuários

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

//fim do crud de usuários


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

$app->run();

 ?>