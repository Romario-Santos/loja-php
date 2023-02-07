<?php
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Page;
use \Hcode\Model\Cart;
$app->get('/', function() {
    $products = Product::listAll();
	$page = new Page();
	$page->setTpl("index",[
		"products"=>Product::checkList($products)
	]);

});

//incio categoria do site
$app->get("/categories/:idcategory",function($idcategory){

   //recebe page atual, se nao receber nada define como 1
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);

	$pages = [];
    
	for($i=1; $i <= $pagination['pages']; $i++){
		array_push($pages,[
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}
	$page = new Page();

	$page->setTpl("category",[
		"category"=>$category->getValues(),
		"products"=>$pagination["data"],
		"pages"=>$pages
	]);
});

$app->get("/products/:desurl",function($desurl){

	$product = new Product();
//var_dump($desurl);
	$product->getFromURL($desurl);

	$page = new Page();

//var_dump($product->getValues());
//exit;

	$page->setTpl("product-detail",[
		"product"=>$product->getValues(),
		"categories"=>$product->getCategories()
	]);
	
});

$app->get("/cart",function()
{
 Cart::getFromSession();
   $page = new Page();

   $page->setTpl("cart");
});


//fim categoria do site