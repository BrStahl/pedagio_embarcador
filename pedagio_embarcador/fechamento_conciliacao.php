<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/fechamento_conciliacao.php";
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


$data_1 		   = $_POST["data_1"];
$data_10	       = $_POST["data_10"];
$lancamento_ecargo = $_POST["lancamento_ecargo"];
$status_id		   = $_POST["status_id"];
$status			   = $_POST["status"];

if($status == 2){
	$select_diferenca = "selected='selected'";	
}


if($cancelar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}

if($pesquisar != ""){
	//PASSA O PARAMETRO PARA SELECIONAR O 
	$mes_selected = "selected_".$mes;
	$$mes_selected = "selected='selected'"; 
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

<script>

function atualiza_fechamento(mes, ano, tipo, id_fechamento){
	//alert(mes);
	//alert(ano);
	$.ajax({
			type: "POST",//define o metódo de passagem de parametros
			 url: "includes/fechamento_periodo.php", //chama uma pagina
			data: "mes="+mes+"&ano="+ano+"&tipo="+tipo+"&id_fechamento="+id_fechamento,  
	     success: function(msg){  //pega o retorno da pagina chamada
					  document.form1.pesquisar.click();
					  alert(msg);
				  }							
	});	

}
	
</script>

</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1250px">

<fieldset>
  <legend>Fechamento Concilia&ccedil;&atilde;o</legend>
  <table width="1200" border="0" align="center">
  	<tr>
  		<td width="300" align="left" rowspan="10">
          <table width="300" border="1" align="left">
            <tr>
              <td width="10%"><div align="left"><strong><font size="-1">Per&iacute;odo:</font></strong></font><FONT color="#FF0000" size="-1">*</FONT></div></td>
             <!-- <td width="90%">&nbsp;
                <input type="text" name="data_1" id="data_1" size="7" maxlength="10" value="<?php //print $data_1 ?>" onkeyup="mascaraData(this, 'data_1')"/>
                <strong><font size="-1">&nbsp;a&nbsp;</font></strong><font size="-1">
                  <input type="text" name="data_10" id="data_10" size="7" maxlength="10" value="<?php //print $data_10 ?>" onkeyup="mascaraData(this, 'data_10')"/>
                </font></td> -->
               <td width="85%">
                    &nbsp;
                    <select name="mes" id="mes">
                        <option></option>
                        <option value="1"  <?php echo $selected_1; ?>>Janeiro</option>
                        <option value="2"  <?php echo $selected_2; ?>>Fevereiro</option>
                        <option value="3"  <?php echo $selected_3; ?>>Mar&ccedil;o</option>
                        <option value="4"  <?php echo $selected_4; ?>>Abril</option>
                        <option value="5"  <?php echo $selected_5; ?>>Maio</option>
                        <option value="6"  <?php echo $selected_6; ?>>Junho</option>
                        <option value="7"  <?php echo $selected_7; ?>>Julho</option>
                        <option value="8"  <?php echo $selected_8; ?>>Agosto</option>
                        <option value="9"  <?php echo $selected_9; ?>>Setembro</option>
                        <option value="10" <?php echo $selected_10; ?>>Outubro</option>
                        <option value="11" <?php echo $selected_11; ?>>Novembro</option>
                        <option value="12" <?php echo $selected_12; ?>>Dezembro</option>
                    </select>
                    &nbsp;Ano:
                    <input type="text" name="ano" id="ano" size="1" maxlength="4" value=<?php echo $ano; ?> >
               </td> 
            </tr>
            <tr>
              <td><div align="left"><strong><font size="-1">Status:</font></strong></div></td>
              <td>&nbsp;
                <select name="status" id="status">
                  <option value="1" >Todos</option>
                  <option value="2" <?php echo $select_diferenca; ?>>Diferen&ccedil;a&nbsp;</option>
                </select></td>
            </tr>
            <tr>
              <td colspan="2" align="left"><span class="txt_home">
                <input name="pesquisar" type="submit" class="botao_site" value="Pesquisar" id="pesquisar" />
              </span>
              
              <?php
                
                if($pesquisar != ""){
                    
					if($mes != "" && $ano != ""){
		  				if($ano >= 2000 && $ano <= 3000){
							$query_verifica_fechamento = "SELECT 
															FCP.FECHAMENTO_CONCILIACAO_PAMCARY_ID,
															U.NOME,
															CONVERT(VARCHAR(10), DT_HR_INCL, 103)+' - '+CONVERT(VARCHAR(5), DT_HR_INCL, 108) 
														 FROM 
															FECHAMENTO_CONCILIACAO_PAMCARY AS FCP WITH (NOLOCK)
														 JOIN USUARIO AS U WITH (NOLOCK)
															ON FCP.USER_INCL = U.ID
														 WHERE
															FCP.MES_FECHAMENTO = $mes
															AND FCP.ANO_FECHAMENTO = $ano
															AND FCP.STATUS_FECHAMENTO = 'a'";
																
							$result_verifica_fechamento = odbc_exec($conSQL, $query_verifica_fechamento) or die("Erro ao verificar o fechamento");
							$verifica_fechamento = odbc_result($result_verifica_fechamento,1);
							$usuario_fechamento  = odbc_result($result_verifica_fechamento,2);
							$data_fechamento     = odbc_result($result_verifica_fechamento,3);
							
							if($verifica_fechamento != ""){//OU SEJA, SE JÁ TIVER FECHADO O PERÍODO.
								echo "&nbsp;
								  <span class='txt_home'>
									<input type='button' name='abrir_periodo' id='abrir_periodo' class='botao_site' value='Abrir Per&iacute;odo'  
									 onclick='atualiza_fechamento($mes, $ano, 2, $verifica_fechamento)'
									/>
								  </span>";
												  
							}else{
								echo "&nbsp;
								  <span class='txt_home'>
									<input type='button' name='fechar_periodo' id='fechar_periodo' class='botao_site' value='Fechar Per&iacute;odo'  
									 onclick='atualiza_fechamento($mes, $ano, 1, 0)'
									/>
								  </span>";
							}
						}
					}
                }
                
            echo "</td>
                 </tr>
                </table>";	
   	
	if($pesquisar != ""){
	 
	  if($mes != "" && $ano != ""){
		  
		if($ano >= 2000 && $ano <= 3000){
			
		    if($verifica_fechamento != ""){
			  $status_fechamento = "Fechado";
			  $cor_fonte_status = "#FF0000"; 
		    }else{
			  $status_fechamento = "Aberto";
			  $cor_fonte_status = "#008000"; 
		    }
	   
			echo"</td>
				 <td width='625' align='left' rowspan='10'>
						<table border='1' width='400'>
							<tr height='26'>
								<td width='150'><b><font size='-1'>Status do Per&iacute;odo:</font></b></td>
								<td width='250'><b><font size='-1' color='$cor_fonte_status'>&nbsp;$status_fechamento</font></b></td>
							</tr>
							<tr height='26'>
								<td width='150'><b><font size='-1'>Fechado por:</font></b></td>
								<td width='250'><font size='-1'>&nbsp;$usuario_fechamento</font></td>
							</tr>
							<tr height='26'>
								<td width='150'><b><font size='-1'>Data:</font></b></td>
								<td width='250'><font size='-1'>&nbsp;$data_fechamento</font></td>
							</tr>
						</table>
				  </td>
				 </tr>
				</table>";
			
			echo "<table>
					<tr>
					  <td>&nbsp;</td>
					</tr>
				  </table>";
			
						
				$ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
			
				
				$nova_dt_inicial = $ano."-".$mes."-01";
							/*implode(preg_match("~\/~", $data_1) == 0 ? "/" : "-",
							array_reverse(explode(preg_match("~\/~", $data_1) == 0 ? "-" : "/", $data_1)));*/
							
				$nova_dt_final = $ano."-".$mes."-".$ultimo_dia_mes;
						   /*implode(preg_match("~\/~", $data_10) == 0 ? "/" : "-",
							array_reverse(explode(preg_match("~\/~", $data_10) == 0 ? "-" : "/", $data_10)));*/
							
				//echo $nova_dt_inicial." | ".$nova_dt_final;
				 
				if($status == 2){
					$condicao = " AND (ADTO.SALDO_BRADESCO != ADTO.SALDO_PAMCARY or ADTO.SALDO_PAMCARY != ADTO.SALDO_ECARGO or
									   SALDO.SALDO_BRADESCO != SALDO.SALDO_PAMCARY or ADTO.SALDO_PAMCARY != ADTO.SALDO_ECARGO or
									   PEDAGIO.SALDO_BRADESCO != PEDAGIO.SALDO_PAMCARY or PEDAGIO.SALDO_PAMCARY != PEDAGIO.SALDO_ECARGO)";
				}else{
					$condicao = "";
				}
				
				$query  = "SELECT
								ADTO.DATA_FORMATADA_ADTO,
								ISNULL(ADTO.SALDO_BRADESCO,0) ADTO_BRADESCO,
								ISNULL(ADTO.SALDO_PAMCARY,0) ADTO_PAMCARY,
								ISNULL(ADTO.SALDO_ECARGO,0) ADTO_ECARGO,
								ISNULL(SALDO.SALDO_BRADESCO,0) SALDO_BRADESCO,
								ISNULL(SALDO.SALDO_PAMCARY,0) SALDO_PAMCARY,
								ISNULL(SALDO.SALDO_ECARGO,0) SALDO_ECARGO,
								ISNULL(PEDAGIO.SALDO_BRADESCO,0) PEDAGIO_BRADESCO,
								ISNULL(PEDAGIO.SALDO_PAMCARY,0) PEDAGIO_PAMCARY,
								ISNULL(PEDAGIO.SALDO_ECARGO,0) PEDAGIO_ECARGO,
								ISNULL(DIVERSOS.SALDO_BRADESCO_CREDITO,0) DIVERSOS_BRADESCO_CREDITO,
								ISNULL(DIVERSOS.SALDO_BRADESCO_DEBITO,0) DIVERSOS_BRADESCO_DEBITO,
								ISNULL(DIVERSOS.SALDO_PAMCARY,0) DIVERSOS_PAMCARY,
								CASE DATEPART(W, ADTO.DATA_ADTO) 
										WHEN 1 THEN 'Domingo'
										WHEN 2 THEN 'Segunda-Feira'
										WHEN 3 THEN 'Ter&ccedil;a-Feira'
										WHEN 4 THEN 'Quarta-Feira'
										WHEN 5 THEN 'Quinta-Feira' 
										WHEN 6 THEN 'Sexta-Feira'
										WHEN 7 THEN 'S&aacute;bado'
								END AS 'DIA DA SEMANA'
							FROM
								/*ADIANTAMENTO*/	
								(SELECT 
									TABELA_ADTO.DATA_FORMATADA_FXCX DATA_FORMATADA_ADTO,
									TABELA_ADTO.DATA_FXCX DATA_ADTO,
									SUM(TABELA_ADTO.DEBITO_BRADESCO) SALDO_BRADESCO,
									SUM(TABELA_ADTO.VALOR_PAMCARY) SALDO_PAMCARY,
									SUM(TABELA_ADTO.VALOR_ECARGO) SALDO_ECARGO
									
								 FROM
								(SELECT DISTINCT 
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_FORMATADA_FXCX,
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 120) DATA_FXCX,
									CASE WHEN FXCX.VALOR < 0  
											THEN CAST(ABS(FXCX.VALOR)  AS NUMERIC(15,2))
									END DEBITO_BRADESCO,
									RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
									CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2)) VALOR_PAMCARY,
									LO.VALOR_LANC_RS VALOR_ECARGO,
									LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE
							
									FROM CORPORE..FXCX FXCX WITH (NOLOCK)
									JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS
										AND LAYOUT_ARQUIVO.TIPO_TRANSACAO = 2
										AND DATEADD(DD,DATEDIFF(DD,0,CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$nova_dt_inicial') AND '$nova_dt_final'
										AND LAYOUT_ARQUIVO.IDENTIFICACAO_PARCELA = 1	
									LEFT JOIN CARGOSOL..PESSOA PES WITH (NOLOCK) ON
										ISNULL(PES.PJ_CGC, PES.PF_CPF) = LAYOUT_ARQUIVO.CPF_CNPJ_CONTRATADO COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS	
									LEFT JOIN CARGOSOL..LANCAMENTO_OPERACAO LO WITH (NOLOCK) ON
										LO.LANCAMENTO_OPERACAO_ID = LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE
										AND LO.TAB_STATUS_ID IN (1,1103)
										AND LO.SUB_GRUPO_CONTABIL_ID IN (2081,5060)
										AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
										AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
										
									WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
									AND FXCX.CODCXA = '00014'
									AND FXCX.CODCOLIGADA = 1
									AND FXCX.COMPENSADO = 1
									) AS TABELA_ADTO  
								GROUP BY
									TABELA_ADTO.DATA_FORMATADA_FXCX,
									TABELA_ADTO.DATA_FXCX
								) AS ADTO
							LEFT JOIN 
								/*DIVERSOS*/
								(SELECT
									DIVERSOS_TABLE.DATA_FORMATADA_FXCX DATA_FORMATADA_ADTO,
									DIVERSOS_TABLE.DATA_FXCX DATA_ADTO,
									SUM(DIVERSOS_TABLE.CREDITO_BRADESCO) SALDO_BRADESCO_CREDITO,
									SUM(DIVERSOS_TABLE.DEBITO_BRADESCO) SALDO_BRADESCO_DEBITO,
									SUM(DIVERSOS_TABLE.VALOR_PAMCARY) SALDO_PAMCARY
								  FROM	 
									 (SELECT DISTINCT
										CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_FORMATADA_FXCX,
										CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 120) DATA_FXCX,
										FXCX.NUMERODOCUMENTO NSU_BRADESCO,
										CASE WHEN FXCX.VALOR >= 0  
												THEN cast(FXCX.VALOR  as numeric(15,2))
										end CREDITO_BRADESCO,
										CASE WHEN FXCX.VALOR < 0  
												THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
										end DEBITO_BRADESCO,
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
										NULL DESCRICAO_PAMCARY,
										CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2)) VALOR_PAMCARY,
										LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE
							
										FROM CORPORE..FXCX FXCX WITH (NOLOCK)
										LEFT JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
											RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS
							
										LEFT JOIN LANCAMENTO_DIVERGENTE LD WITH (NOLOCK) ON
											LD.NSU = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS	
									
										LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
											FRECONCILRELAC.IDXCX = FXCX.IDXCX
										AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
										
										LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
											FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
										AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
										
										LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
											P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
											AND P_PO.TAB_STATUS_ID = 1
											
										WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
										AND FXCX.CODCXA = '00014'
										AND FXCX.CODCOLIGADA = 1
										AND FXCX.COMPENSADO = 1
										AND LAYOUT_ARQUIVO.ID IS NULL) AS DIVERSOS_TABLE
										
										GROUP BY
										 DIVERSOS_TABLE.DATA_FORMATADA_FXCX,
										 DIVERSOS_TABLE.DATA_FXCX
									) AS DIVERSOS
									ON ADTO.DATA_ADTO = DIVERSOS.DATA_ADTO
							LEFT JOIN
									(	SELECT
									TABELA_SALDO.DATA_FORMATADA_FXCX DATA_FORMATADA_SALDO,
									TABELA_SALDO.DATA_FXCX DATA_SALDO,
									SUM(TABELA_SALDO.DEBITO_BRADESCO) SALDO_BRADESCO,
									SUM(TABELA_SALDO.VALOR_PAMCARY) SALDO_PAMCARY,
									SUM(TABELA_SALDO.VALOR_ECARGO) SALDO_ECARGO
								FROM
								(SELECT DISTINCT
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_FORMATADA_FXCX,
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 120) DATA_FXCX,
									FXCX.NUMERODOCUMENTO NSU_BRADESCO,
									CASE WHEN FXCX.VALOR < 0  
											THEN CAST(ABS(FXCX.VALOR)  AS NUMERIC(15,2))
									END DEBITO_BRADESCO,		 
									RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
									CASE WHEN LEN(VALOR_TRANSACAO) = 1
											THEN CAST('0.0'+CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR) AS NUMERIC(15,2))
											ELSE CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2))
									END VALOR_PAMCARY,
									LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
									LO.VALOR_LANC_RS VALOR_ECARGO,
									LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE
							
									FROM CORPORE..FXCX FXCX WITH (NOLOCK)
									JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS 
										AND DATEADD(DD,DATEDIFF(DD,0,CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$nova_dt_inicial') AND '$nova_dt_final'
										
									LEFT JOIN CARGOSOL..PESSOA PES WITH (NOLOCK) ON
										ISNULL(PES.PJ_CGC, PES.PF_CPF) = LAYOUT_ARQUIVO.CPF_CNPJ_CONTRATADO COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
									LEFT JOIN CARGOSOL..COLABORADOR CO  WITH (NOLOCK)ON
										CO.PESSOA_ID = PES.PESSOA_ID
									
									LEFT JOIN CARGOSOL..LANCAMENTO_OPERACAO LO WITH (NOLOCK) ON
										LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = LO.LANCAMENTO_OPERACAO_ID
										AND LO.TAB_STATUS_ID IN (1, 1086)
										AND LO.SUB_GRUPO_CONTABIL_ID IN (5015)
										AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535,1086)	
										AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
							
									WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
									AND FXCX.CODCXA = '00014'
									AND FXCX.CODCOLIGADA = 1
									AND FXCX.COMPENSADO = 1
									AND LAYOUT_ARQUIVO.TIPO_TRANSACAO = 2
									AND LAYOUT_ARQUIVO.IDENTIFICACAO_PARCELA = 3
									
									UNION
									
									--SALDO 2
									SELECT DISTINCT 
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_FORMATADA_FXCX,
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 120) DATA_FXCX,
									FXCX.NUMERODOCUMENTO NSU_BRADESCO,
									CASE WHEN FXCX.VALOR < 0  
											THEN CAST(ABS(FXCX.VALOR)  AS NUMERIC(15,2))
									END DEBITO_BRADESCO,		 
									RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
									CASE WHEN LEN(VALOR_TRANSACAO) = 1
											THEN CAST('0.0'+CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR) AS NUMERIC(15,2))
											ELSE CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2))
									END VALOR_PAMCARY,
									LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
									LO.VALOR_LANC_RS VALOR_ECARGO,
									LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE
									 
									FROM CORPORE..FXCX FXCX WITH (NOLOCK)
									JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS
									LEFT JOIN CARGOSOL..PESSOA PES WITH (NOLOCK) ON
										ISNULL(PES.PJ_CGC, PES.PF_CPF) = LAYOUT_ARQUIVO.CPF_CNPJ_CONTRATADO COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
									LEFT JOIN CARGOSOL..COLABORADOR CO  WITH (NOLOCK)ON
										CO.PESSOA_ID = PES.PESSOA_ID
									
									JOIN CARGOSOL..LANCAMENTO_OPERACAO LO WITH (NOLOCK) ON
										ISNULL(LO.FORNECEDOR_ID, LO.COLABORADOR_ID) = PES.PESSOA_ID
										AND REPLACE(LO.VALOR_LANC_RS,'.',',') = CASE WHEN LEN(VALOR_TRANSACAO) = 1
																						THEN '0,0'+CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR)
																						ELSE SUBSTRING(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR))-2)+','+SUBSTRING(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), LEN(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR))-1, 2)
																				END
										AND (LAYOUT_ARQUIVO.DATA_TRANSACAO BETWEEN CONVERT(VARCHAR,ISNULL(LO.DATA_EFET, FXCX.DATACOMPENSACAO),112) AND CONVERT(VARCHAR,DATEADD(HH,3,ISNULL(LO.DATA_EFET, LO.DATA_VENCTO)),112)) 
										AND ((LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = 0) OR (LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE IS NULL))
										AND LO.TAB_STATUS_ID IN (1, 1086)
										AND LO.SUB_GRUPO_CONTABIL_ID IN (5015)
										AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535,1086)	
										AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
										
										
									LEFT JOIN LANCAMENTO_DIVERGENTE LD WITH (NOLOCK) ON
										LD.NSU = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS	
								
									LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
										FRECONCILRELAC.IDXCX = FXCX.IDXCX
									AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
									
									LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
										FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
									AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
									
									LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
										P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
										AND P_PO.TAB_STATUS_ID = 1
										
									WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
									AND FXCX.CODCXA = '00014'
									AND FXCX.CODCOLIGADA = 1
									AND FXCX.COMPENSADO = 1
									AND LAYOUT_ARQUIVO.TIPO_TRANSACAO = 2
									AND LAYOUT_ARQUIVO.IDENTIFICACAO_PARCELA = 3) AS TABELA_SALDO
								GROUP BY
									TABELA_SALDO.DATA_FORMATADA_FXCX,
									TABELA_SALDO.DATA_FXCX) AS SALDO
								ON SALDO.DATA_SALDO = ADTO.DATA_ADTO
							LEFT JOIN
								(	SELECT
									TABELA_PEDAGIO.DATA_FORMATADA_FXCX DATA_FORMATADA_PEDAGIO,
									TABELA_PEDAGIO.DATA_FXCX DATA_PEDAGIO,
									SUM(TABELA_PEDAGIO.DEBITO_BRADESCO) SALDO_BRADESCO,
									SUM(TABELA_PEDAGIO.VALOR_PAMCARY) SALDO_PAMCARY,
									SUM(TABELA_PEDAGIO.VALOR_ECARGO) SALDO_ECARGO
									
								FROM
								(SELECT DISTINCT
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_FORMATADA_FXCX,
									CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 120) DATA_FXCX,
									CASE WHEN FXCX.VALOR < 0  
											THEN CAST(ABS(FXCX.VALOR)  AS NUMERIC(15,2))
									END DEBITO_BRADESCO,
									
									RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
							
									CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2)) VALOR_PAMCARY,
									--VF.VALE_FRETE_ID LANCAMENTO_ECARGO,
									VF.VAL_PEDAGIO VALOR_ECARGO
	
									FROM CORPORE..FXCX FXCX WITH (NOLOCK)
									LEFT JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS
										AND DATEADD(DD,DATEDIFF(DD,0,CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)),0) BETWEEN DATEADD(DD, -30, '$nova_dt_inicial') AND '$nova_dt_final'
										
									/*COMEÇA*/	
									LEFT JOIN CARGOSOL..PESSOA PES WITH (NOLOCK) 
										ON ISNULL(PES.PJ_CGC, PES.PF_CPF) = LAYOUT_ARQUIVO.CPF_CNPJ_CONTRATADO COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
									LEFT JOIN CARGOSOL..COLABORADOR CO  WITH (NOLOCK)ON
										CO.PESSOA_ID = PES.PESSOA_ID
										AND (CO.TAB_TIPO_VINCULO_ID != 1 OR CO.COLABORADOR_ID IN (4106, 25328, 25819))
									LEFT JOIN CARGOSOL..VALE_FRETE VF WITH (NOLOCK) ON
										VF.COLABORADOR_ID = CO.COLABORADOR_ID
										AND VF.TAB_STATUS_ID = 1
										AND REPLACE(VF.VAL_PEDAGIO,'.',',') = CASE WHEN LEN(VALOR_TRANSACAO) = 1
																						THEN '0,0'+CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR)
																						ELSE SUBSTRING(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR))-2)+','+SUBSTRING(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), LEN(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR))-1, 2)
																			  END
										AND LAYOUT_ARQUIVO.DATA_TRANSACAO = CONVERT(VARCHAR, ISNULL(VF.DATA_ULT_ALT, VF.DATA_EMISSAO),112)
										AND CAST(REVERSE(SUBSTRING(REVERSE(LAYOUT_ARQUIVO.HORA_TRANSACAO),3,4)) AS INT) BETWEEN SUBSTRING(CAST(CAST(DATEADD(MI, -30, ISNULL(CONVERT(VARCHAR(5), VF.DATA_ULT_ALT, 108) , CONVERT(VARCHAR(5), VF.DATA_EMISSAO, 108))) AS TIME) AS VARCHAR), 1, 2)+SUBSTRING(CAST(CAST(DATEADD(MI, -30, ISNULL(CONVERT(VARCHAR(5), VF.DATA_ULT_ALT, 108) , CONVERT(VARCHAR(5), VF.DATA_EMISSAO, 108))) AS TIME) AS VARCHAR), 4, 2) AND
																				                                                SUBSTRING(CAST(CAST(DATEADD(MI,  30, ISNULL(CONVERT(VARCHAR(5), VF.DATA_ULT_ALT, 108) , CONVERT(VARCHAR(5), VF.DATA_EMISSAO, 108))) AS TIME) AS VARCHAR), 1, 2)+SUBSTRING(CAST(CAST(DATEADD(MI,  30, ISNULL(CONVERT(VARCHAR(5), VF.DATA_ULT_ALT, 108) , CONVERT(VARCHAR(5), VF.DATA_EMISSAO, 108))) AS TIME) AS VARCHAR), 4, 2)	
															
									LEFT JOIN CARGOSOL..MANIFESTO_ROD AS MR WITH(NOLOCK) ON
										MR.VALE_FRETE_ID = VF.VALE_FRETE_ID
										AND MR.TAB_STATUS_ID = 1								
										
									/*FINALIZA*/
										
									WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
									AND FXCX.CODCXA = '00014'
									
									AND FXCX.CODCOLIGADA = 1
									AND FXCX.COMPENSADO = 1
									AND LAYOUT_ARQUIVO.TIPO_TRANSACAO = 1
									AND LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = 0				
									AND LAYOUT_ARQUIVO.IDENTIFICACAO_PARCELA = 0) AS TABELA_PEDAGIO		
									
								GROUP BY
									TABELA_PEDAGIO.DATA_FORMATADA_FXCX,
									TABELA_PEDAGIO.DATA_FXCX) AS PEDAGIO
							   ON PEDAGIO.DATA_PEDAGIO = ADTO.DATA_ADTO
									
							WHERE
								1=1
								$condicao
									 
							ORDER BY
								ADTO.DATA_ADTO";
					
					$result = odbc_exec($conSQL, $query) or die ('Erro ao consultar dados');
					
					
					echo "<table width='1400' border='1' align='center'>
							<tr>
							  <td colspan='1' bgcolor='#999999'><div align='center'><strong>Data</strong></div></td>
							  <td width='1' rowspan='1000'><div align='center'></div></td>
							  <td colspan='1' bgcolor='#999999'><div align='center'><strong>Dia Semana</strong></div></td>
							  <td width='1' rowspan='1000'><div align='center'></div></td>
							  <td colspan='3' bgcolor='#999999'><div align='center'><strong>Adiantamento</strong></div></td>
							  <td width='1' rowspan='1000'><div align='center'></div></td>
							  <td colspan='3' bgcolor='#999999'><div align='center'><strong>Ped&aacute;gio</strong></div></td>
							  <td width='1' rowspan='1000'><div align='center'></div></td>
							  <td colspan='3' bgcolor='#999999'><div align='center'><strong>Saldo</strong></div></td>
							  <td width='1' rowspan='1000'><div align='center'></div></td>
							  <td colspan='3' bgcolor='#999999'><div align='center'><strong>Outros</strong></div></td>
							</tr>
							<tr>
							  <td width='100' bgcolor='#CCCCCC'><strong><font size='-1'></font></strong></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong></strong></font></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Bradesco</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Pamcary</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>E-Cargo</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Bradesco</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Pamcary</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>E-Cargo</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Bradesco</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Pamcary</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>E-Cargo</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Cr&eacute;dito Bradesco</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>D&eacute;bito Bradesco</font></strong></div></td>
							  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Pamcary</font></strong></div></td>
							</tr>";
					
					$total_adto_bradesco     		 = 0;
					$total_adto_pamcary      		 = 0;
					$total_adto_ecargo       		 = 0;
					$total_pedagio_bradesco  		 = 0;
					$total_pedagio_pamcary   		 = 0;
					$total_pedagio_ecargo    		 = 0;
					$total_saldo_bradesco    		 = 0;
					$total_saldo_pamcary     		 = 0;
					$total_saldo_ecargo      		 = 0;
					$total_diversos_bradesco_credito = 0;
					$total_diversos_bradesco 		 = 0;
					$total_diversos_pamcary  		 = 0;
					
					while(odbc_fetch_row($result)){
				 
					$data_formatada    		   = odbc_result($result, 1);
					$adto_bradesco 	   		   = odbc_result($result, 2);
					$adto_pamcary 	   		   = odbc_result($result, 3);
					$adto_ecargo 	  		   = odbc_result($result, 4);
					$saldo_bradesco    		   = odbc_result($result, 5);
					$saldo_pamcary 	   		   = odbc_result($result, 6);
					$saldo_ecargo 	   		   = odbc_result($result, 7);
					$pedagio_bradesco  		   = odbc_result($result, 8);
					$pedagio_pamcary   		   = odbc_result($result, 9);
					$pedagio_ecargo    		   = odbc_result($result, 10);
					$diversos_bradesco_credito = odbc_result($result, 11);
					$diversos_bradesco 		   = odbc_result($result, 12);
					$diversos_pamcary  		   = odbc_result($result, 13);
					$dia_semana 	   		   = odbc_result($result, 14);
		
					//PINTA A COR DA FONTE SE O VALOR DO PAMCARY FOR MENOR QUE O DO BRADESCO
					if($saldo_pamcary < $saldo_bradesco){
						$cor_font_saldo1 = "#FF0000";
					}else{
						$cor_font_saldo1 = "#000000";
					}
					if($pedagio_pamcary < $pedagio_bradesco){
						$cor_font_pedagio1 = "#FF0000";
					}else{
						$cor_font_pedagio1 = "#000000";
					}
					if($adto_pamcary < $adto_bradesco){
						$cor_font_adto1 = "#FF0000";
					}else{
						$cor_font_adto1 = "#000000";
					}
					
					//PINTA A COR DA FONTE SE O VALOR DO ECARGO FOR MENOR QUE O DO PAMCARY
					if($saldo_ecargo < $saldo_pamcary){
						$cor_font_saldo2 = "#FF0000";
					}else{
						$cor_font_saldo2 = "#000000";
					}
					if($pedagio_ecargo < $pedagio_pamcary){
						$cor_font_pedagio2 = "#FF0000";
					}else{
						$cor_font_pedagio2 = "#000000";
					}
					if($adto_ecargo < $adto_pamcary){
						$cor_font_adto2 = "#FF0000";
					}else{
						$cor_font_adto2 = "#000000";
					}
					
					//SOMA TODOS OS VALORES PARA EXIBIR NO FINAL DA TABELA
					$total_adto_bradesco             = $total_adto_bradesco + $adto_bradesco;
					$total_adto_pamcary              = $total_adto_pamcary + $adto_pamcary;
					$total_adto_ecargo               = $total_adto_ecargo + $adto_ecargo;
					$total_pedagio_bradesco          = $total_pedagio_bradesco + $pedagio_bradesco;
					$total_pedagio_pamcary           = $total_pedagio_pamcary + $pedagio_pamcary;
					$total_pedagio_ecargo            = $total_pedagio_ecargo + $pedagio_ecargo;
					$total_saldo_bradesco            = $total_saldo_bradesco + $saldo_bradesco;
					$total_saldo_pamcary             = $total_saldo_pamcary + $saldo_pamcary;
					$total_saldo_ecargo              = $total_saldo_ecargo + $saldo_ecargo;
					$total_diversos_bradesco_credito = $total_diversos_bradesco_credito + $diversos_bradesco_credito;
					$total_diversos_bradesco         = $total_diversos_bradesco + $diversos_bradesco;
					$total_diversos_pamcary          =  $total_diversos_pamcary + $diversos_pamcary;
					
					
					//PINTA A COR DA FONTE SE O VALOR TOTAL DO PAMCARY FOR MENOR QUE O DO BRADESCO
					if($total_saldo_pamcary < $total_saldo_bradesco){
						$cor_font_total_saldo1 = "#FF0000";
					}else{
						$cor_font_total_saldo1 = "#000000";
					}
					
					if($total_pedagio_pamcary < $total_pedagio_bradesco){
						$cor_font_total_pedagio1 = "#FF0000";
					}else{
						$cor_font_total_pedagio1 = "#000000";
					}
		
					if($total_adto_pamcary < $total_adto_bradesco){
						$cor_font_total_adto1 = "#FF0000";
					}else{
						$cor_font_total_adto1 = "#000000";
					}
					
					//PINTA A COR DA FONTE SE O VALOR DO ECARGO FOR MENOR QUE O DO PAMCARY
					if($total_saldo_ecargo < $total_saldo_pamcary){
						$cor_font_total_saldo2 = "#FF0000";
					}else{
						$cor_font_total_saldo2 = "#000000";
					}
					if($total_pedagio_ecargo < $total_pedagio_pamcary){
						$cor_font_total_pedagio2 = "#FF0000";
					}else{
						$cor_font_total_pedagio2 = "#000000";
					}
					if($total_adto_ecargo < $total_adto_pamcary){
						$cor_font_total_adto2 = "#FF0000";
					}else{
						$cor_font_total_adto2 = "#000000";
					}
					
					//ALTERA OS VALORES PARA FORMATO BRASILEIRO (999.999,99)
					$adto_bradesco     = number_format($adto_bradesco, 2, ',', '.');
					$adto_pamcary      = number_format($adto_pamcary, 2, ',', '.');
					$adto_ecargo       = number_format($adto_ecargo, 2, ',', '.');
					$saldo_bradesco    = number_format($saldo_bradesco, 2, ',', '.');
					$saldo_pamcary     = number_format($saldo_pamcary, 2, ',', '.');
					$saldo_ecargo      = number_format($saldo_ecargo, 2, ',', '.');
					$pedagio_bradesco  = number_format($pedagio_bradesco, 2, ',', '.');
					$pedagio_pamcary   = number_format($pedagio_pamcary, 2, ',', '.');
					$pedagio_ecargo    = number_format($pedagio_ecargo, 2, ',', '.');
					$diversos_bradesco_credito = number_format($diversos_bradesco_credito, 2, ',', '.');
					$diversos_bradesco = number_format($diversos_bradesco, 2, ',', '.');
					$diversos_pamcary  = number_format($diversos_pamcary, 2, ',', '.');
					
					//ALTERA OS VALORES DOS TOTAIS PARA FORMATO BRASILEIRO (999.999,99)
					$total_adto_bradesco_formatado = number_format($total_adto_bradesco, 2, ',', '.'); 
					$total_adto_pamcary_formatado  = number_format($total_adto_pamcary, 2, ',', '.'); 
					$total_adto_ecargo_formatado   = number_format($total_adto_ecargo, 2, ',', '.'); 
					$total_pedagio_bradesco_formatado = number_format($total_pedagio_bradesco, 2, ',', '.'); 
					$total_pedagio_pamcary_formatado  = number_format($total_pedagio_pamcary, 2, ',', '.'); 
					$total_pedagio_ecargo_formatado   = number_format($total_pedagio_ecargo, 2, ',', '.'); 
					$total_saldo_bradesco_formatado = number_format($total_saldo_bradesco, 2, ',', '.'); 
					$total_saldo_pamcary_formatado  = number_format($total_saldo_pamcary, 2, ',', '.'); 
					$total_saldo_ecargo_formatado   = number_format($total_saldo_ecargo, 2, ',', '.');
					$total_diversos_bradesco_credito_formatado = number_format($total_diversos_bradesco_credito, 2, ',', '.'); 
					$total_diversos_bradesco_formatado = number_format($total_diversos_bradesco, 2, ',', '.'); 
					$total_diversos_pamcary_formatado  = number_format($total_diversos_pamcary, 2, ',', '.');  
									
					//SE O VALOR FOR 0, DEIXA A LINHA VAZIA!
					if($adto_bradesco == '0,00'){
						$adto_bradesco = '&nbsp;';
					}
					if($adto_pamcary == '0,00'){
						$adto_pamcary = '&nbsp;';
					}
					if($adto_ecargo == '0,00'){
						$adto_ecargo = '&nbsp;';
					}
					if($saldo_bradesco == '0,00'){
						$saldo_bradesco = '&nbsp;';
					}
					if($saldo_ecargo == '0,00'){
						$saldo_ecargo = '&nbsp;';
					}
					if($saldo_pamcary == '0,00'){
						$saldo_pamcary = '&nbsp;';
					}
					if($pedagio_bradesco == '0,00'){
						$pedagio_bradesco = '&nbsp;';
					}	
					if($pedagio_pamcary == '0,00'){
						$pedagio_pamcary = '&nbsp;';
					}
					if($pedagio_ecargo == '0,00'){
						$pedagio_ecargo = '&nbsp;';
					}
					if($diversos_bradesco_credito == '0,00'){
						$diversos_bradesco_credito = '&nbsp;';
					}
					if($diversos_bradesco == '0,00'){
						$diversos_bradesco = '&nbsp;';
					}
					if($diversos_pamcary == '0,00'){
						$diversos_pamcary = '&nbsp;';
					}
					
					//SE O VALOR TOTAL FOR 0, DEIXA A LINHA VAZIA!
					if($total_adto_bradesco_formatado == '0,00'){
						$total_adto_bradesco_formatado = '&nbsp;';
					}
					if($total_adto_pamcary_formatado == '0,00'){
						$total_adto_pamcary_formatado = '&nbsp;';
					}
					if($total_adto_ecargo_formatado == '0,00'){
						$total_adto_ecargo_formatado = '&nbsp;';
					}
					if($total_pedagio_bradesco_formatado == '0,00'){
						$total_pedagio_bradesco_formatado = '&nbsp;';
					}
					if($total_pedagio_pamcary_formatado == '0,00'){
						$total_pedagio_pamcary_formatado = '&nbsp;';
					}
					if($total_pedagio_ecargo_formatado == '0,00'){
						$total_pedagio_ecargo_formatado = '&nbsp;';
					}
					if($total_saldo_bradesco_formatado == '0,00'){
						$total_saldo_bradesco_formatado = '&nbsp;';
					}
					if($total_saldo_pamcary_formatado == '0,00'){
						$total_saldo_pamcary_formatado = '&nbsp;';
					}
					if($total_saldo_ecargo_formatado == '0,00'){
						$total_saldo_ecargo_formatado = '&nbsp;';
					}
					if($total_diversos_bradesco_credito_formatado == '0,00'){
						$total_diversos_bradesco_credito_formatado = '&nbsp;';
					}
					if($total_diversos_bradesco_formatado == '0,00'){
						$total_diversos_bradesco_formatado = '&nbsp;';
					}
					if($total_diversos_pamcary_formatado == '0,00'){
						$total_diversos_pamcary_formatado = '&nbsp;';
					}
	
							
					echo "<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
								<div align='center'>$data_formatada</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'>
								<div align='center'><font size='-2' color='$cor'>$dia_semana</font></div>
							  </td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
								<div align='center'>$adto_bradesco</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor_font_adto1'>
								<div align='center'>$adto_pamcary</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'>
								<div align='center'><font size='-2' color='$cor_font_adto2'>$adto_ecargo</font></div>
							  </td>
							  
							  <td bgcolor='#FFFFFF'>
								<div align='center'><font size='-2' color='$cor'>$pedagio_bradesco</font></div>
							  </td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor_font_pedagio1'>
								<div align='center'>$pedagio_pamcary</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor_font_pedagio2'>
								<div align='center'>$pedagio_ecargo</div>
							  </font></td>
							  
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
								<div align='center'>$saldo_bradesco</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor_font_saldo1'>
								<div align='center'>$saldo_pamcary</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor_font_saldo2'>
								<div align='center'>$saldo_ecargo</div>
							  </font></td>
							  <td bgcolor='#FFFFFF'><div align='center'><font size='-2' color='$cor'>
								<div align='center'>$diversos_bradesco_credito</div>
							  </font></div></td>
							  <td bgcolor='#FFFFFF'><div align='center'><font size='-2' color='$cor'>
								<div align='center'>$diversos_bradesco</div>
							  </font></div></td>
							  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
								<div align='center'>$diversos_pamcary</div>
							  </font></td>
							  
							</tr>";
					
					}
					
				echo "<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							  <td bgcolor='#CCCCCC'><font size='-2'>
								<div align='left'><b>TOTAL:</b></div>
							  </font></td>
							  <td bgcolor='#CCCCCC'>
								<div align='center'>&nbsp;
							  </td>
							  <td bgcolor='#CCCCCC'><font size='-2'>
								<div align='center'><b>$total_adto_bradesco_formatado</b></div>
							  </font></td>
							  <td bgcolor='#CCCCCC'><font size='-2' color='$cor_font_total_adto1'>
								<div align='center'><b>$total_adto_pamcary_formatado</b></div>
							  </font></td>
							  <td bgcolor='#CCCCCC'>
								<div align='center'><font size='-2' color='$cor_font_total_adto2'><b>$total_adto_ecargo_formatado</b></font></div>
							  </td>
							  
							  <td bgcolor='#CCCCCC'>
								<div align='center'><font size='-2'><b>$total_pedagio_bradesco_formatado</b></font></div>
							  </td>
							  <td bgcolor='#CCCCCC'><font size='-2' color='$cor_font_total_pedagio1'>
								<div align='center'><b>$total_pedagio_pamcary_formatado</b></div>
							  </font></td>
							  <td bgcolor='#CCCCCC'><font size='-2' color='$cor_font_total_pedagio2'>
								<div align='center'><b>$total_pedagio_ecargo_formatado</b></div>
							  </font></td>						  
							  
							  <td bgcolor='#CCCCCC'>
								<div align='center'><font size='-2'><b>$total_saldo_bradesco_formatado</b></font></div>
							  </td>
							  <td bgcolor='#CCCCCC'><font size='-2' color='$cor_font_total_saldo1'>
								<div align='center'><b>$total_saldo_pamcary_formatado</b></div>
							  </font></td>
							  <td bgcolor='#CCCCCC'><font size='-2' color='$cor_font_total_saldo2'>
								<div align='center'><b>$total_saldo_ecargo_formatado</b></div>
							  </font></td>
							  
							  
							  <td bgcolor='#CCCCCC'><div align='center'><font size='-2'>
								<div align='center'><b>$total_diversos_bradesco_credito_formatado</b></div>
							  </font></div></td>
							  <td bgcolor='#CCCCCC'><div align='center'><font size='-2'>
								<div align='center'><b>$total_diversos_bradesco_formatado</b></div>
							  </font></div></td>
							  <td bgcolor='#CCCCCC'><font size='-2'>
								<div align='center'><b>$total_diversos_pamcary_formatado</b></div>
							  </font></td>
							  
							  
							</tr>";
			
		  }else{
		  		print "<script language = 'javascript'> alert('Favor preencher um ano v\u00e1lido');</script>";
		  }
			
		}else{
			if($mes == "" && $ano == ""){
				print "<script language = 'javascript'> alert('Favor selecionar o m\u00eas e preencher o ano');</script>";
			}else{
				if($ano == ""){
					print "<script language = 'javascript'> alert('Favor preencher o ano');</script>";
				}else{
					print "<script language = 'javascript'> alert('Favor selecionar o m\u00eas');</script>";
				}
			}
			
			
		}
	
	}
	?>
  </table>

  <table width="619" border="0" align="center">
    <tr>
      <td height="22" class="txt_home">&nbsp;</td>
      <td class="txt_home">&nbsp;</td>
      <td class="txt_home">&nbsp;</td>
    </tr>
    <tr>
      <td width="273" class="txt_home">&nbsp;</td>
      <td width="70" class="txt_home"><input name="cancelar" type="submit" class="botao_site" value=" Fechar " id="cancelar" /></td>
      <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->
      <td width="287" class="txt_home">&nbsp;</td>
    </tr>
  </table>
</fieldset>
</form>
</body>
</html>
<?php
}//else
?>
