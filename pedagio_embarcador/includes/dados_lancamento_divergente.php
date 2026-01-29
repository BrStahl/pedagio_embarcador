<?php
include('../class/Dados.php');


$dados = new Dados();

$nsu			= $_POST["nsu"]; 
$data			= $_POST["data"]; 
$valor			= $_POST["valor"]; 
$fornecedor_id	= $_POST["fornecedor_id"]; 
$id				= $_POST["id"]; 

$rel_dados = $dados->campoLancamentoDivergente($fornecedor_id, $id, $nsu, $data, $valor); 
$rel_campo = $rel_dados->campos; 


print $rel_campo;


?>