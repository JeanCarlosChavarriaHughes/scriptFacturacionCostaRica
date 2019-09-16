<?php
namespace Paypal;
require_once('../../vendor/autoload.php');
use Rakit\Validation\Validator;
use ValidatorsFields\ExistRule;
use Ajax\Helpers as Helpers;

	/*Instancia validador*/
	$validator = new Validator([
		'required' 			=> ':attribute no puede estar vacío.',
		'max' 				=> ':attribute no puede tener mas de 100 caracteres.',
		'email' 			=> ':attribute parece estar mal escrito.',
		'numeric' 			=> ':attribute debe ser numérico.',
		'digits' 			=> ':attribute debe tener exactamente x digitos.',
		'regex' 			=> 'Escriba :attribute en su formato original.',
		'digits_between'	=> ':attribute debe tener entre x y x digitos.'
	]);

	/*Declara una regla propia*/
	$validator->addValidator('exist', new ExistRule());

	/*Valida cada uno de los campos recibidos por post*/
	$validation = $validator->validate($_POST, [
		'orderID' 	=> 'required|max:100',
		'idFactura' => 'required|numeric|digits_between:1,11|exist:facturas,id_factura',
	]);

	$response = Helpers::updateInvoicePaymentWithPaypal($_POST['orderID'], $_POST['idFactura']);
	// var_dump($response);
	if($response){
		echo json_encode(array("res" => "ok"));
	}else{
		echo json_encode(array("res" => "error"));
	}
?>