<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../formulario_padrao/formulario.php";
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


$viagem = $_POST["viagem"];



if($cancelar != ""){
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


</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:750px">
<fieldset>
<legend>Lan&ccedil;amento - Ped&aacute;gio</legend> 

<table width="616" border="0" >
	<tr>
	  <td width="16%"><font size="-1"><b>N&ordm; Viagem:</b></td>
	  <td width="13%"><input name="viagem" type="text" id="viagem" value="<?php print $viagem ?>" size="6" /></td>
	  <td width="71%"><span class="txt_home">
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

if ($pesquisar != '')
{


		$query = "SELECT 
					num_vale_frete, CONVERT(VARCHAR(10), VALE_FRETE.data_emissao, 103) data_emissao, 
					CONVERT(VARCHAR(5), VALE_FRETE.data_emissao, 108) hora_emissao,
					replace(val_pedagio,'.',',') pedagio, PESSOA.nome_fantasia motorista, ROTA.nome_rota rota
					FROM CARGOSOL..VALE_FRETE WITH (NOLOCK) 
					JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
						PESSOA.PESSOA_ID = VALE_FRETE.COLABORADOR_ID
					JOIN CARGOSOL..ROTA WITH (NOLOCK) ON
						ROTA.ROTA_ID = VALE_FRETE.ROTA_ID	
					JOIN CARGOSOL..MANIFESTO_ROD MR WITH (NOLOCK) ON
						MR.VALE_FRETE_ID = VALE_FRETE.VALE_FRETE_ID
						AND MR.TAB_STATUS_ID = 1
					WHERE VALE_FRETE.TAB_STATUS_ID = 1
					AND MR.VIAGEM_ID = $viagem";
		//print $query;
		$result = odbc_exec($conSQL, $query);
		
		print "<table width='730' height='37' border='1'> 
					<tr>
						  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Vale Frete</strong></div></td>
						  <td width='130' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Data Emiss&atilde;o</strong></div></td>
						  <td width='100' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Valor Ped&aacute;gio</strong></div></td>							
						  <td width='250' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Motorista</strong></div></td>
						  <td width='150' bgcolor='#CCCCCC'><font size='-2'>
							<div align='center'><strong>Rota</strong><strong></strong></div></td>
					</tr>";
		
					  
		// while(odbc_fetch_row($result))
		 //{	
		 
			$num_vale_frete	= odbc_result($result, 1);
			$data_emissao	= odbc_result($result, 2);
			$hora_emissao 	= odbc_result($result, 3);
			$valor_pedagio 	= odbc_result($result, 4);
			$motorista	 	= odbc_result($result, 5);
			$rota			= odbc_result($result, 6);
			
			
			print "		  
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$num_vale_frete</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>
					  	$data_emissao - $hora_emissao</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$valor_pedagio</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$motorista</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-2'>$rota</strong></font></div></td>				
					  
				 </tr>";
		// }//fim while
		
		 print "</table>";				
	
}
?>
<table width="479" border="0" align="center">
	<tr>
	  <td width="473">&nbsp;</td>
	  </tr>
</table>
<table width="400" border="0" align="center">
	<tr>
	  <td><font color="#0000FF" size="-2"><b>Dados do carregamento do cart&atilde;o</b></font></td>
	  </tr>
</table>

<table width="400" border="1" frame="box" rules="none" align="center">
	<tr>
	  <td width="3">&nbsp;</td>
	  <td width="125"><font size="-1"><b>Valor Recarga:</td>
	  <td width="95"><font size="-1"><b>Data:</td>
	  <td><font size="-1"><b>Hora:</td>
	  <td width="89" rowspan="2"><div align="center"><span class="txt_home">
	    <input name="gravar" type="submit" class="botao_site_2" value=" Gravar " id="gravar" />
	    </span></div></td>
	  </tr>
	<tr>
	  <td height="24">&nbsp;</td>
	  <td><input name="viagem2" type="text" id="viagem2" value="<?php print $valor_pedagio ?>" size="8" /></td>
	  <td><input name="data" type="text" id="data" value="<?php print $data_emissao ?>" size="8" /></td>
	  <td width="54"><input name="hora" type="text" id="hora" value="<?php print $hora_emissao ?>" size="2" /></td>
	  </tr>
	</table>
<table width="479" border="0" align="center">
	<tr>
	  <td width="473">&nbsp;</td>
	  </tr>
</table>
<table width="619" border="0" align="center">        
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

}//else
?>
