<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/relatorio_eventos.php";
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


$data_1 = $_POST["data_1"];



if($fechar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if ($gravar != "")
{

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
function habilita_campos(elmnt)
{
	//alert(elmnt.value);	
	if (elmnt.value == 'f')
	{
		document.form1.data_1.style.background="#FFFFFF";	
		document.form1.data_10.style.background="#FFFFFF";			
		document.form1.data_1.disabled = false;
		document.form1.data_10.disabled = false;		
	}
	else
	{
		document.form1.data_1.style.background="#F5F5F5";	
		document.form1.data_10.style.background="#F5F5F5";			
		document.form1.data_1.disabled = true;
		document.form1.data_10.disabled = true;		
	}
}
</script>

</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:950px">
<fieldset>
<legend>Relat&oacute;rio  de Eventos Integra&ccedil;&atilde;o/ Atualiza&ccedil;&atilde;o</legend> 


<table width="543" border="1" >        
        <tr>
          <td width="56" height="32" class="txt_home"><strong>&nbsp;Data:</strong></td>
          <td width="471" class="txt_home"><input name="data_1" type="text"  id="data_1" onkeyup="mascaraData(this, 'data_1')" value="<?php print $data_1 ?>" size="7" maxlength="10" /></td>
        </tr>
        <tr>
          <td height="30" class="txt_home"><div align="left"><strong>&nbsp;Evento:</strong></div>
<font color="#FF0000"></td>
          <td height="30" class="txt_home"><?php
				$query = "Select 'i' sigla, 'Integracao'
						  union
						  Select 'a' sigla, 'Atualizacao'
						  order by sigla desc";
           		$result = odbc_exec($conSQL, $query);           
          
           		print "<select name='evento_id' id='evento_id' class='lista' >";
				
           		while(odbc_fetch_array($result))
           		{
					if (odbc_result($result, 1) == $evento_id)
						$selected = "selected='selected'";
					else
						$selected = "";
					
            		 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
           		}     
          		print"</select>";
    		?></td>
      </tr>
        <tr>
          <td height="37" colspan="2" class="txt_home"><input name="pesquisar" type="submit" class="botao_site" value="Pesquisar" id="pesquisar" /></td>
        </tr>
</table>
<table border="0">
	<tr>
    	<td>&nbsp;</td>
    </tr>
</table>
<?php 

if ($pesquisar != '')
{

	//convertendo a data
	$data_pesquisa = 
	implode(preg_match("~\/~", $data_1) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data_1) == 0 ? "-" : "/", $data_1))); 
		 
	$data_alterada = str_replace('-','',$data_pesquisa);
	
	if ($evento_id == 'i')
	{
		$query = "select 
				SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+
				'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
				RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
				case when data_transacao IS not null	
						then case when tipo_transacao = 2
									then case when TAB_TIPO_VINCULO_ID = 1
												then 'ADTO FROTA'
												else 'ADTO AGREG'
										 end
									else 'PEDAGIO'
							 end
						else NULL
				END DESCRICAO_PAMCARY,
				cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+
				'.'+SUBSTRING(cast(valor_transacao as varchar), 
				len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_PAMCARY,
				NULL
				from layout_arquivo
				left join CARGOSOL..PESSOA PES with (nolock) on
					PES.pf_cpf = layout_arquivo.cpf_favorecido collate SQL_Latin1_General_CP1_CI_AS
				left join CARGOSOL..COLABORADOR co  with (nolock)on
					co.PESSOA_ID = pes.pessoa_id
				where data_transacao = '$data_alterada'
				order by DESCRICAO_PAMCARY";
		//print $query;
		$result = odbc_exec($conSQL, $query);
	}
	else
	{
		$query = "select 
				SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+
				'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
				RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
				case when data_transacao IS not null	
						then case when tipo_transacao = 2
									then case when TAB_TIPO_VINCULO_ID = 1
												then 'ADTO FROTA'
												else 'ADTO AGREG'
										 end
									else 'PEDAGIO'
							 end
						else NULL
				END DESCRICAO_PAMCARY,
				cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+
				'.'+SUBSTRING(cast(valor_transacao as varchar), 
				len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_PAMCARY
				, FXCX.NUMERODOCUMENTO NSU_BRADESCO
				, SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7)
				from layout_arquivo
				join Corpore..FXCX FXCX with (nolock) on
					FXCX.NUMERODOCUMENTO = 
					RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) collate 
					SQL_Latin1_General_CP1_CI_AI
					AND FXCX.CODCXA = '00014'
					AND FXCX.CODCOLIGADA = 1	
					AND FXCX.COMPENSADO = 1
				left join CARGOSOL..PESSOA PES with (nolock) on
					PES.pf_cpf = layout_arquivo.cpf_favorecido collate SQL_Latin1_General_CP1_CI_AS
				left join CARGOSOL..COLABORADOR co  with (nolock)on
					co.PESSOA_ID = pes.pessoa_id
				where data_transacao = '$data_alterada'
				and data_atualizacao is not null";
		//print $query;
		$result = odbc_exec($conSQL, $query);
	}	
		
		print "<table width='900' height='37' border='1'> 
					<tr>
						  <td width='200' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Data</strong></div></td>
						  <td width='200' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>NSU / Autoriza&ccedil;&atilde;o</strong></div></td>
						  <td width='300' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
						  <td width='200' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Valor</strong></div></td>";
							
						  if ($evento_id == 'a')
							print "
						   <td width='200' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Doc. Original</strong></div></td>							
							<td width='200' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Docto Atualizado</strong></div></td>";						  							
					  print "</tr>";
		
		 $contador = 0;
					  
		 while(odbc_fetch_row($result))
		 {	
		 
			$data 				= odbc_result($result, 1);
			$nsu		 		= odbc_result($result, 2);
			$historico			= odbc_result($result, 3);
			$valor				= odbc_result($result, 4);
			$docto_atualizado	= odbc_result($result, 5);
			$documento			= odbc_result($result, 6);			
			
			
			print "		  
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$data</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$nsu</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$historico</strong></font></div></td>
  				  	  <td bgcolor='#FFFFFF'><div align='center'>
					  	<font size='-2'>R$ ".number_format($valor, 2, ',', '.')."</strong></font></div></td>";
					  
					  if ($evento_id == 'a')
						print "
								<td bgcolor='#FFFFFF'><div align='center'><font size='-2'>
								$documento</strong></font></div></td>						
								<td bgcolor='#FFFFFF'><div align='center'><font size='-2'>
								$docto_atualizado</strong></font></div></td>";					  						
					  
				  print "</tr>";
				  
			$contador =	$contador + 1;
				  
		 }//fim while
		
		 print "</table>
		 <table width='900' height='37' border='0'> 
		 	<tr>
				<td><font size='-1'><b>Total de Lan&ccedil;amentos: <font color='#0000FF'>".$contador."</b></td>
			</tr>
		 </table>";				
	
}
?>


<table width="900" border="0" align="center">        
        <tr>
          <td height="22" class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td class="txt_home"><div align="center">
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
          <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php

	if ($status_id == '')
	    print "<script language='javascript'>habilita_campos('p')</script>";

}//else
?>
