<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;

class Product extends Model
{
    public static function listAll()
    {
        $sql = new Sql();
        return$sql->select("SELECT * FROM tb_products ORDER BY desproduct");
    }
     
    public static function checkList($list)
    {
        foreach($list as &$row)
        {
            $p = new Product();
            $p->setData($row);
            $row = $p->getValues();
        }
        return $list;
    }
    public function save()
    {
        $sql = new Sql();

        $result = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
            ":idproduct"=>$this->getidproduct(),
            ":desproduct"=>$this->getdesproduct(),
            ":vlprice"=>$this->getvlprice(),
            ":vlwidth"=>$this->getvlwidth(),
            ":vlheight"=>$this->getvlheight(),
            ":vllength"=>$this->getvllength(),
            ":vlweight"=>$this->getvlweight(),
            ":desurl"=>$this->getdesurl()
        ));

        $this->setData($result[0]);
        Category::updateDile();
    }

    public function get($id)
    {
        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_products Where idproduct = :idproduct", array(
            ":idproduct"=>$id
        ));

        $this->setData($result[0]);
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct"=>$this->getidproduct()
        ));

        Category::updateDile();
    }
    
    public function checkPhoto()
    {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "site" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg"))
        {
            $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
        }
        else
        {
            $url = "/res/site/img/product.jpg";
        }
        $this->setdesphoto($url);
    }
    
    public function getValues()
    {
        $this->checkPhoto();
        $values = parent::getValues();
        return $values;
    }


    public function setPhoto($file)
    {  
          //pega o nome do arquivo e faz um array com o nome e a extens??o
        $extension = explode(".",$file["name"]);
        //pega a ultima posi????o do array que ?? a extens??o
        $extension = end($extension);
    
        //verifica se a extens??o ?? jpg,jpeg,png ou gif
        switch($extension)
        {
            case "jpg":
            case "jpeg":
                //pega a imagem e cria uma imagem a partir dela
            $image = imagecreatefromjpeg($file["tmp_name"]);
            break;
            case "gif" :
            $image = imagecreatefromgif($file["tmp_name"]);
            break;
            case "png" :
            $image = imagecreatefrompng($file["tmp_name"]);
            break;
        
        }
    //pega o destino da imagem
        $destino = $_SERVER['DOCUMENT_ROOT'].
             DIRECTORY_SEPARATOR . "res" . 
             DIRECTORY_SEPARATOR . "site" . 
             DIRECTORY_SEPARATOR . "img" . 
             DIRECTORY_SEPARATOR . "products" .
             DIRECTORY_SEPARATOR . $this->getidproduct() . ".jpg";
    //gera a imagem
        imagejpeg($image,$destino);
    //destroi a imagem da memoria
        imagedestroy($image);
    
        $this->checkPhoto();
    
    }

    public function getFromURL($desurl)
    {
        $sql = new Sql();

        $rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", array(
            ":desurl"=>$desurl
        ));

        $this->setData($rows[0]);
    }


    public function getCategories()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct", array(
            ":idproduct"=>$this->getidproduct()
        ));
    }
    
}
?>