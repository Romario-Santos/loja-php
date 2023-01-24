<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;

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
    }
}
?>