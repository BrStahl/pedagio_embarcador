<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/update.php";
$logado    = $_SESSION["usuario_logado"];
$acesso	   = valida_acesso($conSQL, $localItem, $logado);
//$acesso = "permitido";

if($acesso <> "permitido"){
    grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta página');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);

	
	//$descricao_tipo_status = $_POST["descricao_tipo_status"];
	$data = date("d/m/Y H:i:s");	

	//pegando dados da ultima atualização
	$query = "select top 1 
			  CONVERT(varchar(10), data_atualizacao, 103),
			  CONVERT(varchar(10), data_atualizacao, 108),
			  qtde_arquivos
			  from log_atualizacao_conciliacao
			  order by id desc";
	$result = odbc_exec($conSQL, $query) ;	
	$data_ultima_atualizacao = odbc_result($result, 1);	
	$hora_ultima_atualizacao = odbc_result($result, 2);	
	$qtde_ultima_atualizacao = odbc_result($result, 3);		
						

if($fechar != "")
{
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if($atualizar != "")
{
	if ($logado != '')
	{
		//pegando o usuario
		$query = "Select id, convert(varchar(10), getdate(), 103), convert(varchar(5), getdate(), 108), getdate()
				  From usuario
				  Where usuario = '$logado'";
		$result = odbc_exec($conSQL, $query);
		$usuario_id = odbc_result($result, 1);
		$data_atual = odbc_result($result, 2);		
		$hora_atual = odbc_result($result, 3);
		$data_verificacao = odbc_result($result, 4);		
			
	
		$query = "select distinct data_transacao
					from layout_arquivo WITH(NOLOCK)
					where status_id = 'p'
					and tipo_transacao <> 1
					and data_transacao <> 0
					AND CAST(SUBSTRING(CAST(data_transacao AS VARCHAR),1,4)+'-'+SUBSTRING(CAST(data_transacao AS VARCHAR),5,2)+'-'+SUBSTRING(CAST(data_transacao AS VARCHAR),7,2) AS DATE) >= DATEADD(DD, -(select dia_parametro From dias_parametro where parametro_id = 2), GETDATE())";
		$result = odbc_exec($conSQL, $query);
	
		$contador = 0;
	
		 while(odbc_fetch_row($result))
		 {
			$data_layout = odbc_result($result, 1);
	
			//ATUALIZANDO NUMERODOCUMENTO Corpore(ADTO)
			$query1 = "UPDATE FXCX
						SET FXCX.NUMERODOCUMENTO = 
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
							AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2   
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))							 
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'		  
						WHERE
							LAYOUT_ARQUIVO.data_transacao = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query1;
			$result1 = odbc_exec($conSQL, $query1) or die('Erro ao atualizar o Corpore');


			//ATUALIZANDO CAMPO DATAS TABELA FXCX, SOMENTE QUANDO DATAS E-CARGO E PAMCARY FOREM DIFERENTES
			$query6 = "UPDATE FXCX
						SET FXCX.DATA = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME),
						FXCX.DATACOMPENSACAO = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))								   
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
						WHERE
							LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						AND LAYOUT_ARQUIVO.DATA_TRANSACAO <> 
						REPLACE(CONVERT(VARCHAR(10), LANCAMENTO_OPERACAO.DATA_EFET, 120),'-','')
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query6;
			$result6 = odbc_exec($conSQL, $query6) or die('Erro6 ao atualizar o Corpore');


			//ATUALIZANDO CAMPO DATAS TABELA FLAN, SOMENTE QUANDO DATAS E-CARGO E PAMCARY FOREM DIFERENTES
			$query7 = "UPDATE FLAN
						SET FLAN.DATABAIXA = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME), 
						FLAN.DATAPAG = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))								   
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
						WHERE
							LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						AND LAYOUT_ARQUIVO.DATA_TRANSACAO <> 
						REPLACE(CONVERT(VARCHAR(10), LANCAMENTO_OPERACAO.DATA_EFET, 120),'-','')
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query7;
			$result7 = odbc_exec($conSQL, $query7) or die('Erro7 ao atualizar o Corpore');


			//ATUALIZANDO CAMPO DATAS TABELA FLANBAIXA, SOMENTE QUANDO DATAS E-CARGO E PAMCARY FOREM DIFERENTES
			$query8 = "UPDATE FLANBAIXA
						SET FLANBAIXA.DATABAIXA = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME), 
						FLANBAIXA.DATACONTABILIZBX = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2  
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))								  
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
						WHERE
							LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						AND LAYOUT_ARQUIVO.DATA_TRANSACAO <> 
						REPLACE(CONVERT(VARCHAR(10), LANCAMENTO_OPERACAO.DATA_EFET, 120),'-','')
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query8;
			$result8 = odbc_exec($conSQL, $query8) or die('Erro8 ao atualizar o Corpore');


			//ATUALIZANDO CAMPO DATAS TABELA CPARTIDA, SOMENTE QUANDO DATAS E-CARGO E PAMCARY FOREM DIFERENTES
			$query9 = "UPDATE CPARTIDA
						SET CPARTIDA.DATA = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME), 
						CPARTIDA.DATA2 = CAST(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)AS DATETIME)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))								   
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
						JOIN CORPORE..CPARTIDA CPARTIDA WITH (NOLOCK) ON
							CPARTIDA.CODCOLIGADA = FLAN.CODCOLIGADA
							AND CAST(FLAN.IDLAN AS VARCHAR(20)) = SUBSTRING(CPARTIDA.INTEGRACHAVE,2,20) 
							AND SUBSTRING(CPARTIDA.INTEGRACHAVE,1,1)='B' 
							AND CPARTIDA.INTEGRAAPLICACAO='F'
						WHERE
							LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						AND LAYOUT_ARQUIVO.DATA_TRANSACAO <> 
						REPLACE(CONVERT(VARCHAR(10), LANCAMENTO_OPERACAO.DATA_EFET, 120),'-','')
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query9;
			$result9 = odbc_exec($conSQL, $query9) or die('Erro9 ao atualizar o Corpore');


			//VERIFICA SE O DIA É FINAL DE SEMANA
			$query10 = "select 
						case DATEPART( dw , '$data_layout')
							when 1 then 'FDS'
							when 2 then 'UTIL'
							when 3 then 'UTIL'
							when 4 then 'UTIL'
							when 5 then 'UTIL'
							when 6 then 'UTIL'
							when 7 then 'FDS'	
						end	";
			$result10 = odbc_exec($conSQL, $query10) ;	
			$dia_semana = odbc_result($result10, 1);				
			
			if ($dia_semana == 'UTIL')
			{
				//VERIFICA SE O DIA É FERIADO
				$query11 = "SELECT 'FERIADO' 
							FROM CORPORE..GFERIADO GFERIADO WITH(NOLOCK) 
							WHERE CODCALENDARIO = 'CONC_BANCARIA' 
							--AND TIPO = 'NA'
							AND DIAFERIADO = '$data_layout'";
				$result11 = odbc_exec($conSQL, $query11) ;	
				$feriado = odbc_result($result11, 1);					
				
				if ($feriado == 'FERIADO')
				{
					$query12 = "select DBO.PROXIMO_DIAUTIL1('$data_layout', 'CONC_BANCARIA')";
					$result12 = odbc_exec($conSQL, $query12) ;	
					$proximo_dia_util = odbc_result($result12, 1);									
				}
				else
					$proximo_dia_util = '';
			}
			else
			{
				$query13 = "select DBO.PROXIMO_DIAUTIL1('$data_layout', 'CONC_BANCARIA')";
				$result13 = odbc_exec($conSQL, $query13) ;	
				$proximo_dia_util = odbc_result($result13, 1);	
			}


			if ($proximo_dia_util != '')
			{

				//ATUALIZANDO CAMPO DATAS TABELA FXCX, SOMENTE QUANDO FOR FIM DE SEMANA
				$query14 = "UPDATE FXCX
							SET FXCX.DATA = '$proximo_dia_util',
							FXCX.DATACOMPENSACAO = '$proximo_dia_util'
							FROM
								Corpore..FXCX FXCX WITH(NOLOCK)
							JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
								FLANBAIXA.IDXCX = FXCX.IDXCX
								AND FLANBAIXA.STATUS = 0
							AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
							JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
								FLAN.IDLAN = FLANBAIXA.IDLAN
							AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
							JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
								LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2
								AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
								AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
								AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
								AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
								OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))									    
							JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
								PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
								LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
								AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
							WHERE
								LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
								AND FLAN.CODTDO = 'ADT'	
							AND FXCX.CODCOLIGADA = 1
							--AND FXCX.COMPENSADO = 1
							";
				//print "<br>".$query14;
				$result14 = odbc_exec($conSQL, $query14) or die('Erro14 ao atualizar o Corpore');
	
	
				//ATUALIZANDO CAMPO DATAS TABELA FLAN, SOMENTE QUANDO FOR FIM DE SEMANA
				$query15 = "UPDATE FLAN
							SET FLAN.DATABAIXA = '$proximo_dia_util', 
							FLAN.DATAPAG = '$proximo_dia_util'
							FROM
								Corpore..FXCX FXCX WITH(NOLOCK)
							JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
								FLANBAIXA.IDXCX = FXCX.IDXCX
								AND FLANBAIXA.STATUS = 0
							AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
							JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
								FLAN.IDLAN = FLANBAIXA.IDLAN
							AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
							JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
								LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2  
								AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
								AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
								AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
								AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
								OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))									    
							JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
								PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
								LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
								AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
							WHERE
								LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
								AND FLAN.CODTDO = 'ADT'	
							AND FXCX.CODCOLIGADA = 1
							--AND FXCX.COMPENSADO = 1
							";
				//print "<br>".$query7;
				$result15 = odbc_exec($conSQL, $query15) or die('Erro15 ao atualizar o Corpore');
	
	
				//ATUALIZANDO CAMPO DATAS TABELA FLANBAIXA, SOMENTE QUANDO FOR FIM DE SEMANA
				$query16 = "UPDATE FLANBAIXA
							SET FLANBAIXA.DATABAIXA = '$proximo_dia_util', 
							FLANBAIXA.DATACONTABILIZBX = '$proximo_dia_util'
							FROM
								Corpore..FXCX FXCX WITH(NOLOCK)
							JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
								FLANBAIXA.IDXCX = FXCX.IDXCX
								AND FLANBAIXA.STATUS = 0
							AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
							JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
								FLAN.IDLAN = FLANBAIXA.IDLAN
							AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
							JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
								LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2 
								AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
								AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
								AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
								AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
								OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))									    
							JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
								PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
								LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
								AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
							WHERE
								LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
								AND FLAN.CODTDO = 'ADT'	
							AND FXCX.CODCOLIGADA = 1
							--AND FXCX.COMPENSADO = 1
							";
				//print "<br>".$query8;
				$result16 = odbc_exec($conSQL, $query16) or die('Erro16 ao atualizar o Corpore');
	
	
				//ATUALIZANDO CAMPO DATAS TABELA CPARTIDA, SOMENTE QUANDO FOR FIM DE SEMANA
				$query17 = "UPDATE CPARTIDA
							SET CPARTIDA.DATA = '$proximo_dia_util', 
							CPARTIDA.DATA2 = '$proximo_dia_util'
							FROM
								Corpore..FXCX FXCX WITH(NOLOCK)
							JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
								FLANBAIXA.IDXCX = FXCX.IDXCX
								AND FLANBAIXA.STATUS = 0
							AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
							JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
								FLAN.IDLAN = FLANBAIXA.IDLAN
							AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
							JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
								LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2    
								AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
								AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
								AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
								AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
								OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))									    
							JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
								PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
								LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
								AND LAYOUT_ARQUIVO.STATUS_ID = 'P'	
							JOIN CORPORE..CPARTIDA CPARTIDA WITH (NOLOCK) ON
								CPARTIDA.CODCOLIGADA = FLAN.CODCOLIGADA
								AND CAST(FLAN.IDLAN AS VARCHAR(20)) = SUBSTRING(CPARTIDA.INTEGRACHAVE,2,20) 
								AND SUBSTRING(CPARTIDA.INTEGRACHAVE,1,1)='B' 
								AND CPARTIDA.INTEGRAAPLICACAO='F'
							WHERE
								LAYOUT_ARQUIVO.DATA_TRANSACAO = $data_layout
								AND FLAN.CODTDO = 'ADT'	
							AND FXCX.CODCOLIGADA = 1
							--AND FXCX.COMPENSADO = 1
							";
				//print "<br>".$query17;
				$result17 = odbc_exec($conSQL, $query17) or die('Erro17 ao atualizar o Corpore');
				
			}//fim if dia_util


			//ATUALIZANDO TABELA LAYOUT ARQUIVO (ADTO)
			$query2 = "UPDATE LAYOUT_ARQUIVO
						SET USER_RESP_ATUALIZACAO = $usuario_id, DATA_ATUALIZACAO = GETDATE(), LAYOUT_ARQUIVO.STATUS_ID = 'a'
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID = 1
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (2081)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) 
							OR (LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))									    
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'		  
						WHERE
							LAYOUT_ARQUIVO.data_transacao = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query2;						
			$result2 = odbc_exec($conSQL, $query2) or die('Erro ao atualizar a tabela layout_arquivo - ADTO');


			//ATUALIZANDO Corpore (saldo) - JOIN PELO LANCAMENTO OPERACAO
			$query4 = "UPDATE FXCX
						SET FXCX.NUMERODOCUMENTO = 
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID IN (1, 1086)
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535, 1086)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) OR 
							(LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))							   
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.id_parcela_cliente = LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID 
		
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query1;
			$result4 = odbc_exec($conSQL, $query4) or die('Erro2 ao atualizar o Corpore');

	
			//ATUALIZANDO TABELA LAYOUT ARQUIVO (SALDO) - JOIN PELO LANCAMENTO OPERACAO
			$query5 = "UPDATE LAYOUT_ARQUIVO
						SET USER_RESP_ATUALIZACAO = $usuario_id, DATA_ATUALIZACAO = GETDATE(), LAYOUT_ARQUIVO.STATUS_ID = 'a'
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2  
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID IN (1, 1086)
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535, 1086)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) OR 
							(LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))							  
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.id_parcela_cliente = LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID
	
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query2;						
			$result5 = odbc_exec($conSQL, $query5) or die('Erro ao atualizar a tabela layout_arquivo - SALDO');
		
		
		//ATUALIZACAO DE SALDO NOVAMENTE CASO O LANCAMENTO OPERACAO NÃO ESTEJA NA TABELA LAYOUT ARQUIVO

			//ATUALIZANDO Corpore (saldo) - JOIN PELA DATA, VALOR E CPF/CNPJ
			$query4 = "UPDATE FXCX
						SET FXCX.NUMERODOCUMENTO = 
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8)
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2 
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID IN (1, 1086)
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535, 1086)	
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) OR 
							(LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))							   
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							((LAYOUT_ARQUIVO.DATA_TRANSACAO = CONVERT(VARCHAR,FLAN.DATABAIXA,112))
							AND (LAYOUT_ARQUIVO.VALOR_TRANSACAO = REPLACE(LANCAMENTO_OPERACAO.VALOR_LANC_RS,'.',''))
						    AND (cpf_cnpj_contratado COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS = CASE WHEN PESSOA.INDICATIVO_PF_PJ = 'PF'
																								   THEN PESSOA.PF_CPF
																								   ELSE PESSOA.PJ_CGC
																						   END))
		
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query1;
			$result4 = odbc_exec($conSQL, $query4) or die('Erro3 ao atualizar o Corpore');

	
			//ATUALIZANDO TABELA LAYOUT ARQUIVO (SALDO) - JOIN PELO LANCAMENTO OPERACAO
			$query5 = "UPDATE LAYOUT_ARQUIVO
						SET USER_RESP_ATUALIZACAO = $usuario_id, DATA_ATUALIZACAO = GETDATE(), LAYOUT_ARQUIVO.STATUS_ID = 'a'
						FROM
							Corpore..FXCX FXCX WITH(NOLOCK)
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2  
							AND LANCAMENTO_OPERACAO.TAB_STATUS_ID IN (1, 1086)
							AND LANCAMENTO_OPERACAO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LANCAMENTO_OPERACAO.TAB_STATUS_PAGTO_ID IN (1,135,525,535, 1086)
							AND ((LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID = 17) OR 
							(LANCAMENTO_OPERACAO.TAB_FORMA_PAGTO_ID IS NULL))							  
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							((LAYOUT_ARQUIVO.DATA_TRANSACAO = CONVERT(VARCHAR,FLAN.DATABAIXA,112))
							AND (LAYOUT_ARQUIVO.VALOR_TRANSACAO = REPLACE(LANCAMENTO_OPERACAO.VALOR_LANC_RS,'.',''))
						    AND (cpf_cnpj_contratado COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS = CASE WHEN PESSOA.INDICATIVO_PF_PJ = 'PF'
																								   THEN PESSOA.PF_CPF
																								   ELSE PESSOA.PJ_CGC
																						   END))
	
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query2;						
			$result5 = odbc_exec($conSQL, $query5) or die('Erro1 ao atualizar a tabela layout_arquivo - SALDO');

	
			//$contador = $contador + $somatoria;
			$proximo_dia_util = '';
	
		 }//fim while 


			//pegando a quantidade de atualizados
			$query3 = "select COUNT(*)
					  from layout_arquivo WITH(NOLOCK)
					  where status_id = 'a'
					  and data_atualizacao >= '$data_verificacao'";
			$result3 = odbc_exec($conSQL, $query3) ;	
			$contador = odbc_result($result3, 1);		


		 
			//INSERINDO NA TABELA DE LOG
			$query = "insert into log_atualizacao_conciliacao (data_atualizacao, qtde_arquivos) 
						values (getdate(), $contador)";
			//print "<br>".$query2;						
			$result = odbc_exec($conSQL, $query) or die('Erro ao atualizar a tabela de log');

	
		 if ($data_layout != '')
			print "<script type='text/javascript'> alert(unescape('Atualiza%e7%e3o feita com sucesso!'));</script>";
		 else
			print "<script type='text/javascript'> alert(unescape('Nenhum registro foi atualizado!'));</script>";
			
			
			//atualiza o PHP com as novas atualizações
			print"<script language='javascript'>window.location.href='update.php';</script>";
			
	}
	else
		print "<script type='text/javascript'> alert(unescape('Sess%e3o Expirada, favor logar novamente!'));</script>";
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.js"></script>

<script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../SCA/includes/js/jquery-autocomplete/lib/thickbox.css"/> 

<style type="text/css">
fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }

</style>


</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:600px">
<fieldset>
<legend>Atualiza&ccedil;&atilde;o</legend> 

    <table width="502" border="0" align="center">
	<tr>
	  <td width="421">&nbsp;</td>
	</tr>
      <tr>
	      <td><div align="center">
	        <input name="atualizar" type="submit" class="botao_site" value=" Atualizar Dados" id="atualizar" />
          </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
	  <td><div align="center"><?php 
				print "<font color='#0000FF' size='-1'><b>&Uacute;ltima Atualiza&ccedil;&atilde;o realizada em ".$data_ultima_atualizacao." &agrave;s ".$hora_ultima_atualizacao;
	  
	  	?>
	  </div></td>
	</tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>


<table width="491" border="0" align="center">        
        <tr>
          <td class="txt_home"><div align="center">
          <div align="center"><font color="#0000FF" size="-1"><b><?php print "Total de Lan&ccedil;amentos Financeiros RM Atualizados: ".$qtde_ultima_atualizacao ?></div></td>
      </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td class="txt_home"><div align="center">
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
          <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->
        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
