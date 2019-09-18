<?php

use \Hcode\Model\User;

function formatPrice(float $vlPrice)
{
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

?>