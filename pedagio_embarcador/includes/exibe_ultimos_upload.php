<?php



include('../class/Dados.php');


$dados = new Dados();


	$relatorio = $dados->ultimaInformacao();  
  
			

	print $relatorio->informacao;

?>