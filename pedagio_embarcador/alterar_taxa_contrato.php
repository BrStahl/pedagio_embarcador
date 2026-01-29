<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/alterar_taxa_contrato.php";
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


$taxa = $_POST["taxa"];
$nova_taxa	 = $_POST["taxanova"];

$nova_taxa = str_replace(',','.',$nova_taxa);

$taxa_calculo= $nova_taxa/100;


if($limpar != ""){
	print"<script language='javascript'>
			window.location.reload();
		  </script>";
}


if($cancelar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}
if($gravar != "" )
{
	$query= "UPDATE taxa_contratos
    SET taxa_contrato = '$taxa_calculo'";
	//print $query;
	odbc_exec($conSQL, $query) or die ('Erro3 ao atualizar');
	
	print "<script> alert('Alterado com sucesso!');
	</script>";
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
<form name="form" method="post" action="">
<fieldset>
    <legend>Alterar Taxa de Contrato</legend>
        <table width="400" border="1" > 
        
         <? $query="select replace(taxa_contrato*100, '.' ,',') from taxa_contratos";
			$result= odbc_exec($conSQL, $query);
			$taxa= odbc_result($result, 1)
		 
		 ?>
          <tr>
            	<td width="118"><font size="-1"><strong>Taxa Atual: </strong></font></td>
                <td width="266"><input type="text" name="taxa" id="taxa" size="6" maxlength="10" disabled="disabled" value="<?php print $taxa ?>"
                />%</td>
          </tr>
             <tr>
            	<td width="118"><font size="-1"><strong>Nova Taxa: </strong></font></td>
               <td width="266"><input type="text" name="taxanova" id="taxanova" size="6" maxlength="10" /> %</td>
          </tr>
          
    </table>
          
           
          <p>&nbsp;</p>
    <b><p>&nbsp;</p></b>
          <table width="534" border="0">
        
    </table>

    <table width="700" border="0" align="center">
          <tr>
            	<td align="center" colspan="3">
                	<input type="submit" name="gravar" id="pesquisar" class="botao_site" value=" Cadastrar" />
                	<input type="button" name="limpar" id="limpar" class="botao_site" value=" Limpar " onclick="limpa()"/>  
                    <input type="submit" name="cancelar" id="fechar" class="botao_site" value=" Fechar " />
                </td>
      </tr>
    </table>
</fieldset>
</form>

</body>
</html>
<?php

}//else

?>
