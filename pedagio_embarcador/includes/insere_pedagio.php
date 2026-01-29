<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$value      = $_POST["value"];
$date       = $_POST["date"];
$driverID   = $_POST["driverID"];
$freightID  = $_POST["freightID"];
$type       = $_POST["type"];
$op 		= $_POST["op"];

if($logado != ""){


	$query_verifica_usuario = "SELECT 
												id
											FROM 
												usuario
											WHERE
												usuario = '$logado'
												and status = 'a'";
											
				$result_motorista = odbc_exec($conSQL, $query_verifica_usuario) or die("Erro ao pegar id do motorista");
				$usuario_id = odbc_result($result_motorista,1);

	if($op == 'insere_id'){
		
		$query_verifica = "SELECT ID FROM REL_PEDAGIO_DIGITADO WHERE STATUS_ID = 'A' AND VALE_FRETE_ID = $freightID";
		$result = odbc_exec($conSQL, $query_verifica) or die("Erro ao pegar id do verifica");
		$rel_pedagio_id = odbc_result($result,1);

		$insert = "INSERT INTO REL_PEDAGIO_DIGITADO (VALE_FRETE_ID,ID_PAMCARD,USER_INCL,STATUS_ID,DT_HR_INCL)
					   VALUES($freightID,$value,$usuario_id,'a',GETDATE())";
		odbc_exec($conSQL, $insert) or die("Erro insert rel pedagio");

		if($rel_pedagio_id){
			$update = "UPDATE REL_PEDAGIO_DIGITADO SET STATUS_ID = 'i' WHERE ID = $rel_pedagio_id";
			odbc_exec($conSQL, $update) or die("Erro update rel pedagio");
		}

		echo "OK";


	}else{

			
				
				
				switch($type){
					case 1: 
						$dados = "VAL_PEDAGIO";
					break;
					case 2: 
						$dados = "DATA_ULT_ALT";
					break;
				}
				
				/*if($data != ""){
					if($type == 1){
						$dado_inserido = "replace(replace('$data','.','' ), ',', '.')";
					}else
						if($type == 2){
							$data_nova = implode(preg_match("~\/~", $data) == 0 ? "/" : "-",
										array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
							
							$dado_inserido = "'$data_nova'";
						}else
							if($type == 3){
								$dado_inserido = "'$data'";
							}
				}else{
					$dado_inserido = "null";
				}*/
				
				//FORMATA O VALORES PARA INSERIR NA BASE DE DADOS, SE NÃO TIVER, EXIBE NULO
					
				if($value != ""){
					$valor_inserido = "replace(replace('$value','.','' ), ',', '.')";
				}else{
					$valor_inserido = "null";
				}
				
				if($date != ""){
						
					$data = substr($date, 0, 10);
					
					$nova_data = implode(preg_match("~\/~", $data) == 0 ? "/" : "-",
								array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
								
					$hora = substr($date, 11,5);
					
					$data_formatada = "'".$nova_data." ".$hora."'";			 

				}else{
					$data_formatada = "null";
				}
				
				switch($type){
					case 1: 
						$dado_inserido = $valor_inserido;
					break;
					case 2: 
						$dado_inserido = $data_formatada;
					break;
				}
				
			if($date != "" && $value != ""){
			
				//comentar verificação do pedágio 
				$query_verifica_pedagio = "SELECT
													MR.VIAGEM_ID
												
											FROM
													CARGOSOL..VALE_FRETE AS VF
												JOIN CARGOSOL..MANIFESTO_ROD AS MR
														ON VF.VALE_FRETE_ID = MR.VALE_FRETE_ID
												
											WHERE
												VF.COLABORADOR_ID = $driverID
												AND VF.VAL_PEDAGIO = $valor_inserido
												AND VF.DATA_ULT_ALT BETWEEN DATEADD(MI, -30, $data_formatada) AND DATEADD(MI, 30, $data_formatada) 
												AND VF.VALE_FRETE_ID != $freightID
												AND VF.TAB_STATUS_ID = 1";
					
					//print_r($query_verifica_pedagio); die();

					$result_verifica_pedagio = odbc_exec($conSQL, $query_verifica_pedagio) or die("Erro  ao verificar pedagio");
					$verifica_pedagio = odbc_result($result_verifica_pedagio,1);
					
					if($verifica_pedagio == ""){
										
						$query_update = "UPDATE CARGOSOL..VALE_FRETE
										SET $dados = $dado_inserido
										WHERE VALE_FRETE_ID = $freightID";
						
						odbc_exec($conSQL, $query_update) or die("Erro ao atualizar pedagio");
						
						$query_sp = "SP_COVRE_INC_LOG_SISTEMA 201, $usuario_id, $freightID, 2, 'ADICIONADO PELO SCA - CONCILICAO PAMCARY'";
						odbc_exec($conSQL, $query_sp) or die("Erro ao executar SP");
					
					}else{
						echo "Pedagio ja existente"."|".$verifica_pedagio;
					}
				
			}else{
					
					$query_update = "UPDATE CARGOSOL..VALE_FRETE
									SET $dados = $dado_inserido
									WHERE VALE_FRETE_ID = $freightID";
					
					odbc_exec($conSQL, $query_update) or die("Erro ao atualizar pedagio");
						
					$query_sp = "SP_COVRE_INC_LOG_SISTEMA 201, $usuario_id, $freightID, 2, 'ADICIONADO PELO SCA - CONCILICAO PAMCARY'";
					odbc_exec($conSQL, $query_sp) or die("Erro ao executar SP");
					
			}
		}

}else{
	echo "Sessão expirada, favor logar novamente";
}

?>