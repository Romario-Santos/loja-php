<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Product;
use \Hcode\Model\User;

class Cart extends Model
{
    const SESSION = "Cart";

    public static function getFromSession()
    {
        $cart = new Cart();

        if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0)
        {
            $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
        }else{
            $cart->getFromSessionID();

            if(!(int)$cart->getidcart() > 0)
            {
                $data = [
                    'dessessionid'=>session_id()
                ];
                
                if(User::checkLogin(false))
                {
                    $user = User::getFromSession();
                    $data['iduser'] = $user->getiduser();
                }

                $cart->setData($data);

                $cart->save();

                $cart->setTosession();


            }
        }
        return $cart;
    }


    public function setTosession()
    {
        $_SESSION[Cart::SESSION] = $this->getValues();
    }

    


    public function save()
    {
        $sql = new Sql();
        $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", array(
            ":idcart"=>$this->getidcart(),
            ":dessessionid"=>$this->getdessessionid(),
            ":iduser"=>$this->getiduser(),
            ":deszipcode"=>$this->getdeszipcode(),
            ":vlfreight"=>$this->getvlfreight(),
            ":nrdays"=>$this->getnrdays()
        ));

        
            $this->setData($results[0]);
        
    }

    public function get($idcart)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", array(
            ":idcart"=>$idcart
        ));
        if(count($results) > 0)
        {
            $this->setData($results[0]);
        }
        
    }

    public function getFromSessionID()
    {
        //var_dump(session_id());

        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_carts WHERE  dessessionid = :dessessionid ", array(
            ":dessessionid"=>session_id()
        ));
        if(count($results) > 0)
        {
            $this->setData($results[0]);
        }
        
        
        
    }
}
?>