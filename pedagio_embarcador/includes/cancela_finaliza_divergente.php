<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	

$lancamento_id	= $_POST["lancamento_id"];
$acao			= $_POST["acao"];
$id_sistema		= $_POST["id_sistema"];
$id_item		= $_POST["id_item"];

	
	$gravacao = $dados->cancelaFinalizaDivergente($acao,$lancamento_id, $id_item, $id_sistema);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

