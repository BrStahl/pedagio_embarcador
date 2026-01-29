<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/dias_parametro.php";
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

	$dia_parametro = $_POST["dia_parametro"];

	//pegando o usuario
	$query = "Select id
			  From usuario
			  Where usuario = '$logado'";
	odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao pegar o usuario<br>");	 
	$result = odbc_exec($conSQL, $query) or die("Erro ao cadastrar novo serviço<br>");
	$usuario_id = odbc_result($result, 1);	 
	
	//verifica se tem permissoes
	$query = "Select responsavel_id, nome, permite_bloqueio, administrador
			  From responsavel_mensagem
			  Where usuario_id = '$usuario_id'";
			  //print $query;
	$result = odbc_exec($conSQL, $query);
	$responsavel_id = odbc_result($result, 1);
	$nome_responsavel = odbc_result($result, 2);
	$permite_bloqueio = odbc_result($result, 3);
	$administrador = odbc_result($result, 4);
	
	
	if ($administrador != 1)
	{
		print "<script language = 'JavaScript'>alert(unescape('Necess%E1rio ser Administrador do Sistema'));open(location, '_self').close();</script>";
	}


if($cancelar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if ($alterar != "")
{
 		$query = "UPDATE dias_parametro 
				  SET dia_parametro = '$dia_parametro'
				  where parametro_id = 2";	
		//print $query;
 		odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao alterar o valor<br>");
}
	
?>


<html>
<head>
<link href="../SCA/includes/estilo.css" rel="stylesheet" type="text/css">
<style type="text/css">
fieldset { padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0; }

body { font: normal 11px tahoma,arial,serif; }

table{margin: 0px;}
table,th,td{border-collapse: collapse;}
th{border-top: 0px solid #000000;}
th,td{border-bottom: 0px solid #000000;border-right: 0px solid #000000;padding: 0px;}
th span,td span{display: block; padding: 0px}
#lista table {width: 413px;}
#lista th{color: #000000;background-color: #CCCCCC;text-align: left}
#lista.tabContainer {width: 367px;border: 1px solid #000000;border-right: 0px;border-top: 0px;}
#lista .scrollContainer {width: 430px;height: 100px;overflow: auto;}
#lista .tabela-coluna0{width: 99px;}
#lista .tabela-coluna1{width: 149px;}
#lista .tabela-coluna2{width: 99px;}


</style>


</head>
<body>
<form action="" method="post" enctype="multipart/form-data" >
<fieldset>
<legend>Dias de corte para atualização pamcary</legend> 

    <table width="400" border="0" align="center">
	<tr>
	  <td colspan="2">&nbsp;</td>
	</tr>
        <tr>
          <td width="50%" class="txt_home"><b>Alterar quantidade de dias:</b></td>
          <td width="50%" class="subtitulo"><input name="dia_parametro" type="text" id="dia_parametro" size="3"></td>
        </tr>
	<tr>
	  <td colspan="2">&nbsp;</td>
	  </tr>
  </table>
<!-- </fieldset>
</form> -->


<?php

		$query = "select dia_parametro
				  From dias_parametro
				  where parametro_id = 2";
		//print $query;
        $result = odbc_exec($conSQL, $query);           

		print"<table width='400' border='1'>
	           <tr>
			     <td bgcolor='#CCCCCC'><b><center>Dias de corte para atualização</center></b></td>
			   </tr>";

	         while(odbc_fetch_row($result))
	           {
	            print"
	               <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
	                 <td><b><center>".odbc_result($result,1)." dias</center></b></td>
	               </tr>";            
	           } 
		print"</table>";	





?>

<table width="200" border="0" align="center">
        <tr>
           <td>&nbsp;</td>
      
        </tr>        
        <tr>
          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td>
          <td class="txt_home"><input name="cancelar" type="submit" class="botao_site" value="Cancelar" id="cancelar" /></td>
        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
