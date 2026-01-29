<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	

$fornecedor_id		= $_POST["fornecedor_id"];
$data		 		= $_POST["data"];
$valor				= $_POST["valor"];
$tarifa				= $_POST["tarifa"];
$id_item			= $_POST["id_item"];
$id_sistema			= $_POST["id_sistema"];

/*
	$data_refeicao = 
	implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
*/
	
	$gravacao = $dados->gravaConferenciaSaldoPedagio($fornecedor_id, $data, $valor, $tarifa, $id_item, $id_sistema);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

