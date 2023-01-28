<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Product;

class Category extends Model
{
    public static function listAll()
    {
        $sql = new Sql();
        return$sql->select("SELECT * FROM tb_categories ORDER BY descategory");
    }

    public function save()
    {
        $sql = new Sql();

        $result = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
            ":idcategory"=>$this->getidcategory(),
            ":descategory"=>$this->getdescategory()
        ));

        $this->setData($result[0]);
        Category::updateDile();
    }

    public function get($id)
    {
        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_categories Where idcategory = :idcategory", array(
            ":idcategory"=>$id
        ));

        $this->setData($result[0]);
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
            ":idcategory"=>$this->getidcategory()
        ));

        Category::updateDile();
    }

    public static function updateDile()
    {
        $categories = Category::listAll();

        $html = [];

        foreach($categories as $row)
        {
              array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }
        
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

    }

    public function getProducts($related = true)
    {
        $sql = new Sql();
        if($related === true)
        {
         $result =  $sql->select("SELECT *FROM tb_products WHERE idproduct IN(
            SELECT a.idproduct
            FROM tb_products a
            INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
            where b.idcategory = :idcategory
            )",array(
                ":idcategory"=>$this->getidcategory()
            ));
        }else {

            $result =  $sql->select("SELECT *FROM tb_products WHERE idproduct NOT IN(
                SELECT a.idproduct
                FROM tb_products a
                INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                where b.idcategory = :idcategory
                )",array(
                    ":idcategory"=>$this->getidcategory()
                ));

        }
        return $result;
    }

    public function addProduct(Product $product)
    {
        $sql = new Sql();
        $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", array(
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ));
    }

    public function removeProduct(Product $product)
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_productscategories  WHERE idcategory = :idcategory AND idproduct = :idproduct", array(
            ":idcategory"=>$this->getidcategory(),
            ":idproduct"=>$product->getidproduct()
        ));
    }

    public function getProductsPage($page = 1,$itemsPerPage = 8)
    {   //calcula o inicio da paginação
        $start = ($page - 1) * $itemsPerPage;

        $sql = new Sql();
        $results = $sql->select("select SQL_CALC_FOUND_ROWS * 
        from tb_products a
        inner join tb_productscategories b on a.idproduct = b.idproduct
        inner join tb_categories c on c.idcategory = b.idcategory
        where c.idcategory = :idcategory
        LIMIT $start,$itemsPerPage;",
        array(
            ":idcategory"=>$this->getidcategory()
        ));

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;"); //retorna o total de linhas da consulta

        return [
            "data"=>Product::checkList($results),
            "total"=>(int)$resultTotal[0]["nrtotal"],
            "pages"=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)//ceil arredonda para cima
        ];
    }
}
?>