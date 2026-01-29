<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/comparacao_tratativa.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

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


$data_1 			= $_POST["data_1"];
$lancamento_ecargo	= $_POST["lancamento_ecargo"];
$status_id			= $_POST["status_id"];


if($cancelar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
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

<script language="javascript">
function esconde_aparece(reduz, updown)
{
  		if($("#"+updown).html().indexOf("botao_down_big.png") > -1)
		{
        	$("#"+reduz).show("slow"); 
			$("#"+updown).html("<img src='botao_up_big.png' width='20' height='20' style='border:none'>");
		}
		else
		{
	    	$("#"+reduz).hide("slow");
			$("#"+updown).html("<img src='botao_down_big.png' width='20' height='20' style='border:none'>");			
		}
}
</script>

</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1300px">
<fieldset>
<legend>Compara&ccedil;&atilde;o e Tratativa</legend> 

<table width="1228" border="1" align="center">
	<tr>
	  <td width="10%"><div align="left"><strong><font size="-1">Data:</strong></div></td>
	  <td width="90%">&nbsp;
	    <input type="text" name="data_1" id="data_1" size="7" maxlength="10" value="<?php print $data_1 ?>" onkeyup="mascaraData(this, 'data_1')"/></td>
	</tr>
	<tr>
	  <td><div align="left"><strong><font size="-1">Lan&ccedil;. E-cargo:</strong></div></td>
	  <td>&nbsp;
    	<?php
			$query = "Select 'v' sigla, 'Vazio'
					  union
					  Select 't' sigla, 'Todos'
					  order by sigla";
			$result = odbc_exec($conSQL, $query);           
	  
			print "<select name='lancamento_ecargo' id='lancamento_ecargo' class='lista' >";
			
			while(odbc_fetch_array($result))
			{
				if (odbc_result($result, 1) == $lancamento_ecargo)
					$selected = "selected='selected'";
				else
					$selected = "";
				
				 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
			}     
			print"</select>";
		?>      
      
      </td>
	  </tr>
	<tr>
	  <td><div align="left"><strong><font size="-1">Status:</strong></div></td>
	  <td>&nbsp;
	    <?php
			$query = "Select 'd' sigla, 'Diferenca'
					  union
					  Select 't' sigla, 'Todos'
					  order by sigla desc";
			$result = odbc_exec($conSQL, $query);           
	  
			print "<select name='status_id' id='status_id' class='lista' >";
			
			while(odbc_fetch_array($result))
			{
				if (odbc_result($result, 1) == $status_id)
					$selected = "selected='selected'";
				else
					$selected = "";
				
				 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
			}     
			print"</select>";
		?></td>
	  </tr>
	<tr>
	  <td colspan="2"><span class="txt_home">
	    <input name="pesquisar" type="submit" class="botao_site" value="Pesquisar" id="pesquisar" />
	  </span></td>
	  </tr>
</table>
<table width="479" border="0" align="center">
	<tr>
	  <td width="473">&nbsp;</td>
	  </tr>
</table>

<?php

if (($pesquisar != '') || ($atualizar != ''))
{
	//limpando a tabela verifica_lancamento_operacao
	$query = "truncate table verifica_lancamento_operacao";	
	//print $query2;
	odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao limpar a tabela verifica_lancamento_operacao<br>");	


	//convertendo a data
	$data_pesquisa = 
	implode(preg_match("~\/~", $data_1) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data_1) == 0 ? "-" : "/", $data_1))); 

	
	if ($lancamento_ecargo == 'v')
		$condicao_lancamentos = "and LO.LANCAMENTO_OPERACAO_ID is null and tipo_transacao = 2";
	else
		$condicao_lancamentos = "";

	if ($status_id == 'd')
		$condicao_status = "and (((TIPO_TRANSACAO = 2) and (LANCAMENTO_ID is null) and (LANCAMENTO_ECARGO is null) 
						 and (CREDITO_BRADESCO is null) and (LANCAMENTO_BRADESCO LIKE '%CARGA CARTAO%')) or 
						((NSU_PAMCARY is null) and (CREDITO_BRADESCO is null) and (LANCAMENTO_BRADESCO LIKE '%CARGA CARTAO%')))";
	else
		$condicao_status = "";
		

	$query = "
select *
from (
		select distinct
		CONVERT(varchar(10), FXCX.DATA, 103) DATA_BRADESCO,
		ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
		SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
		FXCX.NUMERODOCUMENTO NSU_BRADESCO,
		CASE WHEN FXCX.VALOR >= 0  
				THEN cast(FXCX.VALOR  as numeric(15,2))
		end CREDITO_BRADESCO,
		CASE WHEN FXCX.VALOR < 0  
				THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
		end DEBITO_BRADESCO,
		 
		SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
		RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
		case when data_transacao IS not null	
				then case when tipo_transacao = 2
							then case when LO.SUB_GRUPO_CONTABIL_ID = 5015
										then 'SALD AGREG'
										else case when TAB_TIPO_VINCULO_ID = 1
													then 'ADTO FROTA'
													else 'ADTO AGREG'
											 end	
								 end
							else 'PEDAGIO'
					 end
				else NULL
		END DESCRICAO_PAMCARY,
		cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_PAMCARY,
		
		convert(varchar(10), isnull(LO.data_efet,LO.data_vencto), 103) DATA_ECARGO,
		LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
		LO.valor_lanc_rs VALOR_ECARGO,
		
		tipo_transacao TIPO_TRANSACAO,
		ld.id LANCAMENTO_ID
		 
		FROM Corpore..FXCX FXCX with (nolock)
		LEFT JOIN layout_arquivo LAYOUT_ARQUIVO with (nolock) on
			RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
		
		LEFT JOIN CARGOSOL..PESSOA PES with (nolock) on
			isnull(PES.pj_cgc, pes.pf_cpf) = layout_arquivo.cpf_cnpj_contratado collate SQL_Latin1_General_CP1_CI_AS
		LEFT JOIN CARGOSOL..COLABORADOR co  with (nolock)on
			co.PESSOA_ID = pes.pessoa_id
		
		LEFT JOIN CARGOSOL..LANCAMENTO_OPERACAO LO with (nolock) on
			ISNULL(LO.FORNECEDOR_ID, LO.COLABORADOR_ID) = PES.PESSOA_ID
			AND replace(lo.valor_lanc_rs,'.',',') = SUBSTRING(cast(LAYOUT_ARQUIVO.valor_transacao as varchar), 1, len(cast(LAYOUT_ARQUIVO.valor_transacao as varchar))-2)+','+SUBSTRING(cast(LAYOUT_ARQUIVO.valor_transacao as varchar), len(cast(LAYOUT_ARQUIVO.valor_transacao as varchar))-1, 2)
			AND dateadd(dd,datediff(dd,0,isnull(LO.data_efet,LO.data_vencto)),0) = SUBSTRING(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR),1,4)+'-'+SUBSTRING(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR),5,2)+'-'+SUBSTRING(CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR),7,2)
			AND LO.TAB_STATUS_ID = 1
			AND LO.SUB_GRUPO_CONTABIL_ID IN (5015,2081)
		    AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
			AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
			
		LEFT JOIN lancamento_divergente ld with (nolock) on
			ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS	
	
		LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
		    FRECONCILRELAC.IDXCX = FXCX.IDXCX
		AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
		
		LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
		    FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
		AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
	
			
		WHERE dateadd(dd,datediff(dd,0,FXCX.DATA),0) = '$data_pesquisa'
		AND FXCX.CODCXA = '00014'
		AND FXCX.CODCOLIGADA = 1
		AND FXCX.COMPENSADO = 1
		$condicao_lancamentos
	)dados
where NSU_BRADESCO is not null
$condicao_status

ORDER BY TIPO_TRANSACAO DESC, DOCUMENTO, DEBITO_BRADESCO";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('Erro1');

	print"<table width='1243' border='1' align='center'>
	<tr>
	  <td colspan='6' bgcolor='#999999'><div align='center'><strong>Bradesco</strong></div></td>
	  <td width='8' rowspan='1000'><div align='center'></div></td>
	  <td colspan='4' bgcolor='#999999'><div align='center'><strong>Pamcary</strong></div></td>
	  <td width='5' rowspan='1000'><div align='center'></div></td>
	  <td colspan='3' bgcolor='#999999'><div align='center'><strong>E-<br />
	    Cargo</strong></div></td>
	  </tr>
	<tr>
	  <td width='72' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Data</strong></div></td>
	  <td width='200' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Lan&ccedil;amento</strong></div></td>
	  <td width='53' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Documento</strong></div></td>	  
	  <td width='100' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>NSU/Autoriza&ccedil;&atilde;o</strong></div></td>
	  <td width='73' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>Cr&eacute;dito</strong></div></td>
	  <td width='73' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>D&eacute;bito</strong></div></td>
	  <td width='76' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Data</strong></div></td>
	  <td width='50' bgcolor='#CCCCCC'><div align='center'><strong><font size='-1'>NSU/Autoriza&ccedil;&atilde;o</strong></div></td>
	  <td width='77' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Descri&ccedil;&atilde;o</strong></div></td>
	  <td width='69' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Valor</strong></div></td>
	  <td width='74' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Data</strong></div></td>
	  <td width='78' bgcolor='#CCCCCC'><strong><font size='-1'>Lan&ccedil;amento</strong></td>      
	  <td width='69' bgcolor='#CCCCCC'><div align='center'><font size='-1'><strong>Valor</strong></div></td>            
	  </tr>";
	  
	    $total_credito = 0;
	  	$total_debito = 0;
	    $total_pamcary = 0;
		$total_ecargo = 0;
		
		$total_adto_agreg_pamcary = 0;
		$total_adto_frota_pamcary = 0;
		$total_pedagio_pamcary = 0;
		
		$total_adto_agreg_ecargo = 0;
		$total_adto_frota_ecargo = 0;	
		
		$total_diferenca_bradesco = 0;
		$total_diferenca_pamcary = 0;
		$total_diferenca_ecargo = 0;
		
		$total_diversos_debitos = 0;

		$total_saldo_bradesco = 0;			
		
		$total_pendentes = 0;
				
	  
		 while(odbc_fetch_row($result))
		 {
			$data_bradesco 			= odbc_result($result, 1);
			$lancamento_bradesco 	= odbc_result($result, 2);
			$documento_bradesco		= odbc_result($result, 3);
			$nsu_bradesco 			= odbc_result($result, 4);
			$credito_bradesco		= odbc_result($result, 5);
			$debito_bradesco		= odbc_result($result, 6);

			$data_pamcary 			= odbc_result($result, 7);
			$nsu_pamcary 			= odbc_result($result, 8);
			$descricao_pamcary 		= odbc_result($result, 9);
			$valor_pamcary 			= odbc_result($result, 10);
			
			$data_ecargo 			= odbc_result($result, 11);
			$lancamento_ecargo 		= odbc_result($result, 12);
			$valor_ecargo 			= odbc_result($result, 13);

			$tipo_transacao			= odbc_result($result, 14);		
			$lancamento_id			= odbc_result($result, 15);						

			
			//verifica se o lancamento não é duplicado
			$query1 = "select lancamento_operacao_id
					  from verifica_lancamento_operacao
					  where lancamento_operacao_id = $lancamento_ecargo";
			//print $query;
			$result1 = odbc_exec($conSQL, $query1);
			$lancamento_operacao = odbc_result($result1, 1); 
			
			
			if ($lancamento_ecargo != '')
			{
				if ($lancamento_operacao == '')
				{
					$query2 = "insert into verifica_lancamento_operacao (lancamento_operacao_id) values ($lancamento_ecargo)";	
					//print $query2;
					odbc_exec($conSQL, $query2) or die(odbc_errormsg($conSQL)."<br>Erro1 a gravar o lancamento operacao<br>");				
				}
				else
					$lancamento_ecargo = 'duplicado';
			}


			if (($lancamento_bradesco != 'CARGA CARTAO TRANSPORTES') && ($lancamento_bradesco != 'TRANSPORT/PEDAGIO'))
				$nsu_bradesco_exibido = '&nbsp;';
			else
				$nsu_bradesco_exibido = $nsu_bradesco;


			if (($tipo_transacao == 2) && ($lancamento_ecargo == '') && ($lancamento_id == '') && ($credito_bradesco == '') && 
				(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES') || ($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))
				$cor = '#FF0000';
			else
				if (($nsu_pamcary == '') && ($nsu_bradesco != '') && ($lancamento_id == '') && ($credito_bradesco == '') && 
					(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES') || ($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))
					$cor = '#FF0000';
				else
					$cor = '#000000';

			
			print "
			
				<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>";
				
				if ($debito_bradesco != '')
				{
					print "<td bgcolor='#FFFFFF'>
						<a href=javascript:pagina('lancamento_divergente.php?nsu=".chr(34).$nsu_bradesco.chr(34)."&data=".chr(34).$data_bradesco.chr(34)."&valor=".chr(34).$debito_bradesco.chr(34)."','1200','300','lancamento')>
						<center><font size='-2' color='$cor'>".$data_bradesco."</font></a></center></td>";
				}
				else
				{
					print "<td bgcolor='#FFFFFF'>
						<a href=javascript:pagina('baixa_credito.php?doc=".chr(34).$documento_bradesco.chr(34)."&data=".chr(34).$data_bradesco.chr(34)."&valor=".chr(34).$credito_bradesco.chr(34)."','1200','500','documento')>
						<center><font size='-2' color='$cor'>".$data_bradesco."</font></a></center></td>";					
				}
				print "
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>
					<div align='center'>".$lancamento_bradesco."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".$documento_bradesco."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".$nsu_bradesco_exibido."</div></td>";
					
				  if ($credito_bradesco != '')
				  	print "<td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
						<div align='center'>".str_replace('.',',',$credito_bradesco)."</div></td>";
				  else
				  	print "<td bgcolor='#FFFFFF'><font size='-2' color='$cor'><div align='center'>&nbsp;</div></td>";
					
				  if ($debito_bradesco != '')
				  	print "<td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
						<div align='center'>".str_replace('.',',',$debito_bradesco)."</div></td>";
				  else
				  	print "<td bgcolor='#FFFFFF'><font size='-2' color='$cor'><div align='center'>&nbsp;</div></td>";
				  
				  print "
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".$data_pamcary."</div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2' color='$cor'>
					<div align='center'>".$nsu_pamcary."</div>
				  </div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".$descricao_pamcary."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".str_replace('.',',',$valor_pamcary)."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".$data_ecargo."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'><div align='center'>".$lancamento_ecargo."</div></td>
				  <td bgcolor='#FFFFFF'><font size='-2' color='$cor'>
					<div align='center'>".str_replace('.',',',$valor_ecargo)."</div></td>                
				  </tr>";

			$total_credito 	= $total_credito + $credito_bradesco;			
			$total_debito 	= $total_debito + $debito_bradesco;
			$total_pamcary 	= $total_pamcary + $valor_pamcary;
			$total_ecargo 	= $total_ecargo + $valor_ecargo;			
		
		
			if ($descricao_pamcary == 'ADTO FROTA')
			{
				if ($lancamento_ecargo != '')
				{	
					$total_adto_frota_pamcary = $total_adto_frota_pamcary + $valor_pamcary;
					$total_adto_frota_ecargo = $total_adto_frota_ecargo + $valor_ecargo;
				}
				else
					$total_adto_frota_pamcary = $total_adto_frota_pamcary + $valor_pamcary;
			}
			else
				if ($descricao_pamcary == 'ADTO AGREG')
				{
					if ($lancamento_ecargo != '') 
					{
						$total_adto_agreg_pamcary = $total_adto_agreg_pamcary + $valor_pamcary;
						$total_adto_agreg_ecargo = $total_adto_agreg_ecargo + $valor_ecargo;
					}
					else
						$total_adto_agreg_pamcary = $total_adto_agreg_pamcary + $valor_pamcary;
				}
				else
					if ($descricao_pamcary == 'PEDAGIO')
						$total_pedagio_pamcary = $total_pedagio_pamcary + $valor_pamcary;	
					else
						if ($descricao_pamcary == 'SALD AGREG')
							$total_saldo_bradesco = $total_saldo_bradesco + $debito_bradesco;
						
						
			if (($lancamento_id == '') && ($nsu_pamcary == '') && 
			(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES')||($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))		
			{	
				$total_diferenca_bradesco = $total_diferenca_bradesco + $debito_bradesco;
				$total_diferenca_ecargo = $total_diferenca_ecargo + $debito_bradesco;
			}

			if (($lancamento_id == '') && ($nsu_pamcary != '') && ($lancamento_ecargo == '') && ($tipo_transacao == '2'))			
				$total_diferenca_ecargo = $total_diferenca_ecargo + $debito_bradesco;
				
			if (($debito_bradesco != '') && 
				(($lancamento_bradesco != 'CARGA CARTAO TRANSPORTES') && ($lancamento_bradesco != 'TRANSPORT/PEDAGIO')))
				$total_diversos_debitos = $total_diversos_debitos + $debito_bradesco;			
			
			if ($lancamento_id != '')
				$total_pendentes = $total_pendentes + $debito_bradesco;
				
		 }//fim while
		 
		 	$total_diferenca_pamcary = $total_diferenca_bradesco;
			$total_saldo_pamcary 	 = abs($total_saldo_bradesco);
			$total_saldo_ecargo		 = abs($total_saldo_bradesco);			
		 
		print "	<tr>
				  <td colspan='4' >&nbsp;</td>
				  <td ><div align='center'><strong><font size='-2'>".number_format($total_credito, 2, ',', '.')."</strong></div></td>
				  <td '><div align='center'><strong><font size='-2'>".number_format($total_debito, 2, ',', '.')."</strong></div></td>
				  <td colspan='3'><div align='center'>&nbsp;</div></td>
				  <td ><div align='center'><strong><font size='-2'>".number_format($total_pamcary, 2, ',', '.')."</strong></div></td>
				  <td colspan='2' ><div align='center'>&nbsp;</div></td>
				  <td ><div align='center'><strong><font size='-2'>".number_format($total_ecargo, 2, ',', '.')."</strong></div></td>
				</tr>
			</table>";

}

?>
<table width="1242" border="0" align="center">
	<tr>
	  <td width="623" >&nbsp;</td>
	  <td width="401" >&nbsp;</td>
	  <td colspan="3" >&nbsp;</td>
	  <td width="58" >&nbsp;</td>
	  </tr>
</table>  
<!--<table width="1242" border="0" align="center">
	<tr>
	  <td width="623" ><a href="javascript:esconde_aparece('tabela','updown1')"><div id="updown1"><img src="botao_down_big.png" width="20" height="20" style='border:none'/></div></a></td>
	  <td width="401" >&nbsp;</td>
	  <td colspan="3" >&nbsp;</td>
	  <td width="58" >&nbsp;</td>
	  </tr>
</table>
<div id="tabela" style="display:none"> -->
<table width="1243" border="1" align="center" frame="box" rules="none">
  <tr>
    <td width="545"><font size="-2">
      <div align="right"><strong>Total Adto Agregado:</strong></div></td>
    <td width="70"><div align="right"><strong><font size="-2"> </strong> <strong>
      <? print number_format($total_adto_agreg_pamcary, 2, ',', '.') ?></strong></div></td>
    <td width="295"><div align="right"><strong><font size="-2">Total Adto Agreg:</strong></div></td>
    <td width="70"><div align="right"><strong><font size="-2">
      <? print number_format($total_adto_agreg_pamcary, 2, ',', '.') ?></strong>    </div></td>
    <td><div align="right"><strong><font size="-2">Total Adto Agreg:</strong></div></td>
    <td><div align="right"><strong><font size="-2"><? print number_format($total_adto_agreg_ecargo, 2, ',', '.') ?></strong></div></td>
  </tr>     
	<tr>
	  <td><font size="-2">
	    <div align="right"><strong>Total Adto Frota:</strong></div></td>
	  <td><div align="right"><strong><font size="-2"></strong><strong>
	    <? print number_format($total_adto_frota_pamcary, 2, ',', '.') ?></strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>Total Adto Frota:</strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>
	      <? print number_format($total_adto_frota_pamcary, 2, ',', '.') ?>
	      </strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>Total Adto Frota:</strong></div></td>
	  <td width="68" ><font size="-2">
	    <div align="right"><strong><? print number_format($total_adto_frota_ecargo, 2, ',', '.') ?></strong></div></td>
	  </tr>
	<tr>
	  <td><font size="-2">
	    <div align="right"><strong>Total Ped&aacute;gio:</strong> </div></td>
	  <td><div align="right"><strong><font size="-2"></strong><strong>
	    <? print number_format($total_pedagio_pamcary, 2, ',', '.') ?>
	    </strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>Total  Ped&aacute;gio:</strong> </div></td>
	  <td><div align="right"><strong><font size="-2"></strong> <strong>
	    <? print number_format($total_pedagio_pamcary, 2, ',', '.') ?></strong></div></td>
	  <td><div align="right"><strong><font size="-2">Total  Ped&aacute;gio:</strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong><font size="-2" color="#FF0000"></strong> <strong>
	      <? print number_format($total_pedagio_pamcary, 2, ',', '.') ?></strong>	      </div></td>
	  </tr>
	<tr>
	  <td><div align="right"><strong><font size="-2">Saldo Agregado:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
	    <? print number_format(abs($total_saldo_bradesco), 2, ',', '.') ?></strong></div></td>
	  <td><div align="right"><strong><font size="-2">Total Saldo Agregado:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
	    <? print number_format($total_saldo_pamcary, 2, ',', '.') ?></strong></div></td>
	  <td><div align="right"><strong><font size="-2">Total Saldo Agregado:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
	    <? print number_format($total_saldo_ecargo, 2, ',', '.') ?></strong></div></td>
	  </tr>
	<tr>
	  <td><font size="-2">
	    <div align="right"><strong>Total Diversos D&eacute;bitos:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#FF0000">
	    <? print number_format(abs($total_diversos_debitos), 2, ',', '.') ?></strong><strong></strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>Total Diversos D&eacute;bitos:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#FF0000">
	    <? print number_format(abs($total_diversos_debitos), 2, ',', '.') ?></strong><strong></strong></div></td>
	  <td><font size="-2">
	    <div align="right"><strong>Total Diversos D&eacute;bitos:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#FF0000">
	    <? print number_format(abs($total_diversos_debitos), 2, ',', '.') ?></strong><strong></strong></div></td>
	  </tr>
	<tr>
	  <td><div align="right"><strong><font size="-2">Total Pendentes:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
        <? print number_format(abs($total_pendentes), 2, ',', '.') ?></strong></div></td>
	  <td><div align="right"><strong><font size="-2">Total Pendentes:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
        <? print number_format(abs($total_pendentes), 2, ',', '.') ?></strong></div></td>
	  <td><div align="right"><strong><font size="-2">Total Pendentes:</strong></div></td>
	  <td><div align="right"><strong><font size="-2">
        <? print number_format(abs($total_pendentes), 2, ',', '.') ?></strong></div></td>
	  </tr>
	<tr>
	  <td><div align="right"></div></td>
	  <td><div align="right"></div></td>
	  <td><div align="right"></div></td>
	  <td><div align="right"></div></td>
	  <td><div align="right"></div></td>
	  <td><div align="right"></div></td>
	  </tr>
	<tr>
	  <td><font size="-22" color="#0000FF">
	    <div align="right"><strong>Diferen&ccedil;a:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#0000FF">
	    <? print number_format(abs($total_diferenca_bradesco), 2, ',', '.') ?></strong><strong></strong></div></td>
	  <td><font size="-2" color="#0000FF">
	    <div align="right"><strong>Diferen&ccedil;a:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#0000FF"> 
	    <? print number_format(abs($total_diferenca_pamcary), 2, ',', '.') ?></strong><strong></strong></div></td>
	  <td><font size="-2" color="#0000FF">
	    <div align="right"><strong>Diferen&ccedil;a:</strong></div></td>
	  <td><div align="right"><strong><font size="-2" color="#0000FF">
	    <? print number_format(abs($total_diferenca_ecargo), 2, ',', '.') ?></strong><strong></strong></div></td>
	  </tr>
     </table>
<!--</div> -->
<table width="619" border="0" align="center">        
        <tr>
          <td height="22" class="txt_home">&nbsp;</td>
          <td class="txt_home">&nbsp;</td>
          <td class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td width="273" class="txt_home">&nbsp;</td>
          <td width="70" class="txt_home"><input name="atualizar" type="submit" class="botao_site" value="Atualizar" id="atualizar" /></td>
<!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->
          <td width="287" class="txt_home"><input name="cancelar" type="submit" class="botao_site" value=" Fechar " id="cancelar" /></td>
        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
