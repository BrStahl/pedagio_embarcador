<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	

$fornecedor_id	= $_POST["fornecedor_id"];
$id				= $_POST["id"];
$nsu	 		= $_POST["nsu"];
$valor			= $_POST["valor"];
$observacao		= $_POST["observacao"];
$id_sistema		= $_POST["id_sistema"];
$id_item		= $_POST["id_item"];

	
	$gravacao = $dados->gravaObservacaoDivergente($fornecedor_id, $id, $nsu, $valor, $observacao, $id_item, $id_sistema);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

