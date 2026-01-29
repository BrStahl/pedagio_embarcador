<?php



include('../class/Dados.php');


$dados = new Dados();

$aba					= $_POST["aba"];
$lancamento_ecargo		= $_POST["lancamento_ecargo"];
$status_id				= $_POST["status_id"];
$ponto_operacao			= $_POST["ponto_operacao"];
$data_ini				= $_POST["data_ini"];
$data_fim				= $_POST["data_fim"];

	$data_inicial = 
	implode(preg_match("~\/~", $data_ini) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data_ini) == 0 ? "-" : "/", $data_ini)));

	$data_final = 
	implode(preg_match("~\/~", $data_fim) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim)));


	$relatorio = $dados->exibeDados($aba, $lancamento_ecargo, $status_id, $ponto_operacao, $data_inicial, $data_final, $tipo_relatorio_id);  
  
			

	print $relatorio->relatorio;

?>