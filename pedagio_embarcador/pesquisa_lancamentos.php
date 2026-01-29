<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/pesquisa_lancamentos.php";
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
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1100px">
<fieldset>
<legend>Pesquisa - Lan&ccedil;amentos</legend> 


<table width="543" border="1" >        
        <tr>
          <td height="34" class="txt_home"><div align="left"><strong>&nbsp;Status:</strong>
			<?php
				$query = "Select 'p' sigla, 'Pendentes'
						  union
						  Select 'f' sigla, 'Concluidos'
						  order by sigla desc";

           		$result = odbc_exec($conSQL, $query);           
          
           		print "<select name='status_id' id='status_id' class='lista' onchange='javascript:habilita_campos(this)' >";
				
           		while(odbc_fetch_array($result))
           		{
					if (odbc_result($result, 1) == $status_id)
						$selected = "selected='selected'";
					else
						$selected = "";
					
            		 print "<option value='".odbc_result($result, 1)."'$selected>".odbc_result($result, 2)."</option>";
           		}     
          		print"</select>";
    		?>          
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Per&iacute;odo:&nbsp;</strong> 
            <input name="data_1" type="text"  id="data_1" onkeyup="mascaraData(this, 'data_1')" value="<?php print $data_1 ?>" size="7" maxlength="10" />&nbsp;<strong>&agrave;</strong>&nbsp;
<input type="text" name="data_10" id="data_10" size="7" maxlength="10" value="<?php print $data_10 ?>" onkeyup="mascaraData(this, 'data_10')" />
          </div>
<font color="#FF0000"></td>
      </tr>
        <tr>
          <td height="22" class="txt_home"><input name="pesquisar" type="submit" class="botao_site" value="Pesquisar" id="pesquisar" /></td>
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

	if ($status_id == 'p')
		$condicao_status = "and LD.status_id = 'p'";
	else
	{
		//convertendo a data
		$data_inicial = 
		implode(preg_match("~\/~", $data_1) == 0 ? "/" : "-", 
		array_reverse(explode(preg_match("~\/~", $data_1) == 0 ? "-" : "/", $data_1))); 
		 
		$data_final = 
		implode(preg_match("~\/~", $data_10) == 0 ? "/" : "-", 
		array_reverse(explode(preg_match("~\/~", $data_10) == 0 ? "-" : "/", $data_10))); 	
	
	
		$condicao_status = "and LD.status_id = 'f' 
							and cast(cast(data_conciliacao as varchar (11)) as datetime) between '$data_inicial' 
							and '$data_final'";
	}
	
	if (($status_id == 'f') && (($data_1 == '') || ($data_10 == '')))
			print "<script type='text/javascript'> alert(unescape('Favor preencher os campos datas'));</script>";
	else
	{
		$query = "select distinct 
					LD.id,
					isnull(convert(varchar(10), FXCX.data, 103),convert(varchar(10), data_gravacao, 103)),
					SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7),
					nsu,
					ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO),
					observacao,
					LD.valor,
					ABS(LD.valor) valor_positivo,
					convert(varchar(10), data_conciliacao, 103), 
					fornecedor.descricao
					from lancamento_divergente LD with (nolock)
					join fornecedor_pedagio fornecedor with (nolock) on
						fornecedor.id = LD.fornecedor_id
					LEFT join Corpore..FXCX with (nolock) on
						FXCX.NUMERODOCUMENTO = LD.nsu collate SQL_Latin1_General_CP1_CI_AI
						AND FXCX.COMPENSADO = 1

					LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
						FRECONCILRELAC.IDXCX = FXCX.IDXCX
					AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
					
					LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
						FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
					AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA								
						
					where LD.id is not null
					$condicao_status
					order by LD.id";
		//print "<pre>$query</pre>";
		$result = odbc_exec($conSQL, $query);
		
		print "<table width='1100' height='37' border='1'> 
					<tr>
						  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Data</strong></div></td>
						  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Fornecedor</strong></div></td>							
						  <td width='130' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>N&ordm; Documento</strong></div></td>
						  <td width='150' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>NSU / Autoriza&ccedil;&atilde;o</strong></div></td>							
						  <td width='300' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
						  <td width='300' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Observa&ccedil;&atilde;o</strong><strong></strong></div></td>
						  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Valor</strong></div></td>";
							
						  if ($status_id == 'f')
							print "<td width='120' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Data Concilia&ccedil;&atilde;o</strong></div></td>";						  							
					  print "</tr>";
		
		$total = 0;
					  
		 while(odbc_fetch_row($result))
		 {	
		 
			$lancamento_id	= odbc_result($result, 1);
			$data 			= odbc_result($result, 2);
			$documento	 	= odbc_result($result, 3);
			$nsu		 	= odbc_result($result, 4);
			$historico		= odbc_result($result, 5);
			$observacao		= odbc_result($result, 6);
			$valor			= odbc_result($result, 7);
			$valor_positivo	= odbc_result($result, 8);
			$data_concil	= odbc_result($result, 9);
			$fornececedor	= odbc_result($result, 10);
			
			print "		  
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$data</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$fornececedor</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$documento</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$nsu</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$historico</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$observacao</strong></font></div></td>				  				  	  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>R$ 
					  	".number_format($valor_positivo, 2, ',', '.')."</strong></font></div></td>";
					  
					  if ($status_id == 'f')
						print "<td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$data_concil</strong></font></div></td>";					  						
			$total = $total + $valor_positivo;
					  
				  print "</tr>";
		 }//fim while
		
		 print "</table>
		 <table width='1090' height='37' border='0'> 
		 	<tr>
				<td align='right'><font size='-1'><b>Total:&nbsp;&nbsp;&nbsp;R$ ".number_format($total, 2, ',', '.')."</b></td>
			</tr>
		 </table>";				
	}
}
?>


<table width="975" border="0" align="center">        
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
