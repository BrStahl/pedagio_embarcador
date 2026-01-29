<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/baixa_credito.php";
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


$documento 	= $_GET["doc"];
$data 		= $_GET["data"];
$valor 		= $_GET["valor"];
$sistema 	= $_POST["sistema"];


$documento 	= str_replace('"','',$documento);
$data 		= str_replace('"','',$data);
$valor 		= str_replace('"','',$valor);


//pegando o id do usuario
$query = "select id
		  from usuario
		  where usuario = '$logado'";
$result = odbc_exec($conSQL, $query) ;
$usuario_id = odbc_result($result,1);	


if($fechar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if ($baixar != "")
{

	for ($i=0;$i<count($sistema);$i++)
	{
		list($lancamento_id) = explode("|", $sistema[$i]);

		$query = "update lancamento_divergente
				  set status_id = 'f', data_conciliacao = getdate(), usuario_id_concil = $usuario_id
				  where id = $lancamento_id";
		//print $query;
		odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao atualizar o lancamento divergente<br>");


	}

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
function baixa_lancamento(elmnt, id, valor){
	//alert(elmnt.checked);
	//alert(valor);	
	var valores 		= parseFloat(document.form1.valor_somado.value.replace(".", "").replace(",", "."));;
	var valor_credito 	= parseFloat(document.form1.valor_credito.value.replace(".", "").replace(",", "."));;

	if(elmnt.checked == 1)
	{
		//alert('verdadeiro');
		var somado = valores + valor;
		somado = somado.toFixed(2);
		document.form1.valor_somado.value = somado.toString().replace(".", ",");
	}
	else
	{
		var somado = valores - valor;
		somado = somado.toFixed(2);			
		document.form1.valor_somado.value = somado.toString().replace(".", ",");
	}
		
		
	if (somado == valor_credito)
		document.form1.baixar.disabled = false;
	else
		document.form1.baixar.disabled = true;
		
		
}
</script>


</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1100px">
<fieldset>
<legend>Baixa - Cr&eacute;dito</legend> 

<table width="964" border="0" align="center">        
        <tr>
          <td height="22" class="txt_home"><strong><font color="#0000FF">Dados do Cr&eacute;dito</strong></td>
          <td class="txt_home">&nbsp;</td>
          <td class="txt_home">&nbsp;</td>
      </tr>
</table>

<div align="center">
  <?php


	//convertendo a data
	$data_bradesco = 
	implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data))); 


	$query = "select 
				convert(varchar(10), FXCX.data, 103),
				SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7),
				ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO),
				replace(cast(FXCX.valor as numeric(15,2)),'.',',')
				from Corpore..FXCX with (nolock)
				
				LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
					FRECONCILRELAC.IDXCX = FXCX.IDXCX
				AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
				
				LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
					FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
				AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA
						
				where FXCX.NUMERODOCUMENTO like '$documento%'
				AND FXCX.COMPENSADO = 1
				AND dateadd(dd,datediff(dd,0,FXCX.DATA),0)  = '$data_bradesco'
				AND FXCX.VALOR = '$valor'";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
	$data 			= odbc_result($result, 1);
	$documento	 	= odbc_result($result, 2);
	$historico		= odbc_result($result, 3);
	$valor			= odbc_result($result, 4);
	
	
	print "<table width='969' height='37' border='1'> 
				<tr>
					  <td width='150' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Data</strong></div></td>
					  <td width='200' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>N&ordm; Documento</strong></div></td>
					  <td width='500' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
					  <td width='119' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Valor</strong></div></td>

				  </tr>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$data</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$documento</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$historico</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$valor</strong></font></div></td>						
				  </tr>						 
		   </table>";	

?>
  
</div>
<table width="966" border="0" align="center">        
        <tr>
          <td width="266" height="22" class="txt_home">&nbsp;</td>
          <td width="474" class="txt_home">&nbsp;</td>
          <td width="212" class="txt_home"><input name="valor_credito" type="hidden" id="valor_credito" size="8" value="<?php print $valor ?>"/></td>
      </tr>
</table>
<table width="972" border="0" align="center">        
        <tr>
          <td height="22" class="txt_home"><strong><font color="#0000FF">Lan&ccedil;amentos Divergentes</strong></td>
          <td class="txt_home">&nbsp;</td>
          <td class="txt_home">&nbsp;</td>
      </tr>
</table>

<div align="center">
  <?php 

	$query = "select distinct 
				LD.id,
				convert(varchar(10), FXCX.data, 103),
				nsu,
				historico,
				observacao,
				LD.valor,
				ABS(LD.valor) valor_positivo
				from lancamento_divergente LD with (nolock)
				join Corpore..FXCX with (nolock) on
					FXCX.NUMERODOCUMENTO = LD.nsu collate SQL_Latin1_General_CP1_CI_AI
					AND FXCX.COMPENSADO = 1
				where LD.status_id = 'p'
				order by valor_positivo";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
	print "<table width='969' height='37' border='1'> 
				<tr>
					  <td width='50' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>&nbsp;</strong></div></td>
					  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>Data</strong></div></td>
					  <td width='150' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>N&ordm; Documento</strong></div></td>
					  <td width='300' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
					  <td width='450' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>Observa&ccedil;&atilde;o</strong><strong></strong></div></td>
					  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
						<div align='center'><strong>Valor</strong></div></td>
				  </tr>";
				  
	 while(odbc_fetch_row($result))
	 {	
	 
		$lancamento_id	= odbc_result($result, 1);
		$data 			= odbc_result($result, 2);
		$nsu		 	= odbc_result($result, 3);
		$historico		= odbc_result($result, 4);
		$observacao		= odbc_result($result, 5);
		$valor			= odbc_result($result, 6);
		$valor_positivo	= odbc_result($result, 7);
		
		print "		  
				  <td bgcolor='#FFFFFF'><div align='center'>
				  
				  
				  <input type='checkbox' name='sistema[]' id='sistema[]' value='$lancamento_id' 
				  onclick='javascript:baixa_lancamento(this,".$lancamento_id.",".$valor_positivo.")'/></center></td>	
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$data</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$nsu</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$historico</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$observacao</strong></font></div></td>				  				  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$valor</strong></font></div></td>						
				  
			  </tr>";
	 }//fim while
	
	 print "</table>";				

?>
  
  
  
</div>
<table width="967" border="0" align="center">        
        <tr>
          <td width="413" height="22" class="txt_home">&nbsp;</td>
          <td width="473" class="txt_home">&nbsp;</td>
          <td width="67" class="txt_home">
          		<input name="valor_somado" type="text" id="valor_somado" style="text-align:center" style="background-color:#ddd;color:#00f;" size="8" value="0,00" readonly="readonly"/>
          </td>
      </tr>
        <tr>
          <td colspan="3" class="txt_home"><div align="center">
            <input name="baixar" type="submit" class="botao_site" value="Baixar Cr&eacute;dito" id="baixar" disabled="disabled"/>            
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
          <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
