<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");

$logado = $_SESSION["usuario_logado"];

$mes           = $_POST["mes"];
$ano           = $_POST["ano"];
$tipo          = $_POST["tipo"];
$id_fechamento = $_POST["id_fechamento"];

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
	
	if($tipo == 1){
		$insere_dados = "INSERT INTO FECHAMENTO_CONCILIACAO_PAMCARY(MES_FECHAMENTO, ANO_FECHAMENTO, STATUS_FECHAMENTO, USER_INCL, DT_HR_INCL)
								VALUES($mes, $ano, 'a', $usuario_id, GETDATE())";
		odbc_exec($conSQL, $insere_dados);
		
		echo "Período fechado";
	}else{
		$atualiza_dados = "UPDATE FECHAMENTO_CONCILIACAO_PAMCARY
							SET STATUS_FECHAMENTO = 'i',
								USER_ULT_ALT = $usuario_id,
								DT_HR_ULT_ALT = GETDATE()
							WHERE FECHAMENTO_CONCILIACAO_PAMCARY_ID = $id_fechamento";
		odbc_exec($conSQL, $atualiza_dados);
		
		echo "Período aberto novamente";
	}

}