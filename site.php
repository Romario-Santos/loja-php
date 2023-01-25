<?php
use \Hcode\Model\Products;
use \Hcode\Model\Category;
use \Hcode\Page;

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");

});

//incio categoria do site
$app->get("/categories/:idcategory",function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);
	$page = new Page();
	$page->setTpl("category",[
		"category"=>$category->getValues(),
		"products"=>[]
	]);
});
//fim categoria do site