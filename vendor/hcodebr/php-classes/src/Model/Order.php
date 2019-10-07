<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model{


	const ERROR = "Order-Error";
	const SUCCESS = "Order-Success";

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_orders_save(:idorder,:idcart,:iduser,:idstatus,:idaddress,:vltotal)",
			[":idorder"=>$this->getidorder(),
			":idcart"=>$this->getidcart(),
			":iduser"=>$this->getiduser(),
			":idstatus"=>$this->getidstatus(),
			":idaddress"=>$this->getidaddress(),
			":vltotal"=>$this->getvltotal()
			]);

		if(count($results) > 0)
		{
			$this->setData($results[0]);
		}

	}

	public function get($idorder)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_orders a 
				INNER JOIN tb_ordersstatus b USING(idstatus) 
				INNER JOIN tb_carts c USING(idcart)
				INNER JOIN tb_users d ON d.iduser = a.iduser
				INNER JOIN tb_addresses e USING(idaddress)
				INNER JOIN tb_persons f on f.idperson = d.idperson
				WHERE a.idorder = :idorder ",
				[":idorder"=>$idorder
				]);

		if(count($results) > 0)
		{
			$this->setData($results[0]);
		}			
	}

	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_orders a 
				INNER JOIN tb_ordersstatus b USING(idstatus) 
				INNER JOIN tb_carts c USING(idcart)
				INNER JOIN tb_users d ON d.iduser = a.iduser
				INNER JOIN tb_addresses e USING(idaddress)
				INNER JOIN tb_persons f on f.idperson = d.idperson
				ORDER by a.dtregister DESC");

	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", array(
			":idorder"=>$this->getidorder()
		));
	}

	public function getCart():Cart
	{
		$cart = new Cart();
		$cart->get((int)$this->getidcart());

		return $cart;
	}

	public static function setMsgError($msg)
	{

		$_SESSION[Order::ERROR] = $msg;
	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[Order::ERROR]) && $_SESSION[Order::ERROR]) ? $_SESSION[Order::ERROR] : "";

		Order::clearMsgError();
		return $msg;
	}

	public static function clearMsgError()
	{
		$_SESSION[Order::ERROR] = NULL;	
	}

	public static function setSuccess($msg)
	{

		$_SESSION[Order::SUCCESS] = $msg;
	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[Order::SUCCESS]) && $_SESSION[Order::SUCCESS]) ? $_SESSION[Order::SUCCESS] : "";

		Order::clearSuccess();
		return $msg;
	}

	public static function clearSuccess()
	{
		$_SESSION[Order::SUCCESS] = NULL;	
	}

}

?>