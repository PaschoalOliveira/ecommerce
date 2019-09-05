<?php

namespace HCode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\Product;

class Category extends Model{


	public static function listAll()
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM  tb_categories user ORDER BY descategory") ;

		return $results;
	}

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()
		));

		$this->setData($results[0]);

		Category::updateFile();
	}

	public function get($idcategory)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories category WHERE idcategory = :idcategory", array(
			":idcategory"=>$idcategory
		));

		$this->setData($results[0]);
	}


	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM  tb_categories WHERE idcategory = :idcategory", array(
			":idcategory"=>$this->getidcategory()
		));

		Category::updateFile();
	}


	public static function updateFile()
	{
		$categories = Category::listAll();

		$html = [];

		foreach ($categories as $row) {
				array_push($html, '<li><a href="/categories/'. $row['idcategory'] .'">' . $row['descategory'] . '</a></li>');
		}

		$location = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html";

		file_put_contents($location, implode('',$html));
	}

	public function getProducts($related = true)
	{
		$sql = new Sql();

		if ($related == true) 
		{
			return $sql->select("SELECT * FROM tb_products WHERE idproduct IN (SELECT produto.idproduct FROM TB_PRODUCTS produto INNER JOIN tb_productscategories produtocategoria ON produto.idproduct = produtocategoria.idproduct WHERE idcategory = :idcategory);",
			[
				':idcategory'=>$this->getidcategory()
			]);

		}else{
			return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN (SELECT produto.idproduct FROM TB_PRODUCTS produto INNER JOIN tb_productscategories produtocategoria ON produto.idproduct = produtocategoria.idproduct WHERE idcategory = :idcategory);",
			[
				':idcategory'=>$this->getidcategory()
			]);
		}
	}

	public function getProductsPage($page = 1, $itensPerPage = 3)
	{

		$start = ($page-1) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * from tb_products product
		inner join tb_productscategories productcategories
		on productcategories.idproduct = product.idproduct
		inner join tb_categories category on category.idcategory = productcategories.idcategory
		where category.idcategory = :idcategory
		limit $start,$itensPerPage;",
		[
			'idcategory'=>$this->getidcategory()
		]);


		$resultTotal = $sql->select("SELECT FOUND_ROWS() as nrtotal;");

		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]['nrtotal'],
			'pages'=>ceil($resultTotal[0]['nrtotal'] / $itensPerPage)
		];


	}

	public function addProduct(Product $product){

		$sql = new Sql();

		$sql->query("INSERT INTO tb_productscategories (idcategory,idproduct) values(:idcategory,:idproduct)",[
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
		]);

	}

	public function removeProduct(Product $product){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory and idproduct =:idproduct;",[
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
		]);

	}

}

?>