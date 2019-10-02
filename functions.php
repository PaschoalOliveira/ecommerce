<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;

function formatPrice($vlPrice)
{
	if(!($vlPrice > 0)) $vlPrice =0;
	return number_format($vlPrice, 2, ",", ".");
}

function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);
}

function getUserName()
{
	$user = User::getFromSession();

	//var_dump($user);

	return $user->getdesperson();
}

function getCartNrQtd()
{
	$cart = Cart::getFromSession();
	$totals = $cart->getProductsTotals();

	return $totals['nrqtd'];
}

function getCartVlSubtotal()
{
	$cart = Cart::getFromSession();
	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);
}

?>