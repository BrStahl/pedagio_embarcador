<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/conferencia_nota_cobranca.php";
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


$data_inicio = $_POST["data"];
$data_fim 	 = $_POST["data_1"];

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
function mascaraData(campoData,nome_campo){
	//alert(nome_campo);
   var data = campoData.value;  
   if (data.length == 2){  
   		data = data + '/';   
		$("#"+nome_campo).val(data);   
		return true;                     
   }
   if (data.length == 5){
	    data = data + '/'; 
		$("#"+nome_campo).val(data);
		return true;
   }
}
</script>

<script language="javascript">

 //funçao para limpar campos especificos sem dar reflesh na pagina
function limpa(){

	javascript:document.form.data.value='';
	javascript:document.form.data_1.value='';
	javascript:document.form.descricao.value='';
	javascript:document.form.tipo_dispositivo.value='';
} 


</script>

<script>
function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>46 && tecla<58) || tecla==8 || tecla==0){ 
		return true;
	}else{ 
		return false;
    }
}
</script>
<script>
function verifica_data_novo (elmnt) {

	if(elmnt.value != '')
	{
		dia = (elmnt.value.substring(0,2));
		mes = (elmnt.value.substring(3,5));
		ano = (elmnt.value.substring(6,10));
	
		var now   = new Date();
		var year  = now.getYear();
		var month = now.getMonth();
		var day   = now.getDate() ;
		var mes_  = [];
		
		
		mes_[0] = "January";
		mes_[1] = "February";
		mes_[2] = "March";
		mes_[3] = "April";
		mes_[4] = "May";
		mes_[5] = "June";
		mes_[6] = "July";
		mes_[7] = "August";
		mes_[8] = "September";
		mes_[9] = "October";
		mes_[10] = "November";
		mes_[11] = "December";
	
	
	
		situacao = "";
		// verifica o dia valido para cada mes
		if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31 ) {
		   situacao = "falsa";
		}
	
		else
		if (mes < 01 || mes > 12 ) {// verifica se o mes e valido
			situacao = "falsa";
		}
	
		else
		if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {// verifica se e ano bissexto
		   situacao = "falsa";
		}
		else
		if (elmnt.value == "") {
		   situacao = "falsa";
		}
		else
		if (elmnt.value.length != 10){
		 	alert('Data fora do padr\xE3o DD/MM/AAAA');
        	return false;
		}
		if (situacao == "falsa") {
		   alert(unescape("Data inv%E1lida!"));
		   elmnt.select();
		}
	}
    //dataAtual = mes[data.getMonth()] + ' ' + data.getDate() + ' ' + data.getFullYear();
   //alert( (((Date.parse("November"+" "+dia+" "+ano))-(Date.parse("November"+" "+day+" "+year)))/(24*60*60*1000)).toFixed(0) );
   //alert(day+"-"+month+"-"+year);
}


</script>


</head>
<body>
<form name="form" method="post" action="">
<fieldset>
        <legend>Confer&ecirc;ncia da Nota de Cobran&ccedil;a</legend>
        <table width="400" border="1" > 
        
         <? $query="select replace(taxa_contrato*100, '.' ,',') from taxa_contratos";
			$result= odbc_exec($conSQL, $query);
			$taxa= odbc_result($result, 1)
		 
		 ?>
          <tr>
            	<td width="118"><font size="-1"><strong>Data Transa&ccedil;&atilde;o: </strong></font></td>
                <td width="266"><font size="-1">Inicial: </font><input type="text" name="data" id="data" size="6" maxlength="10" OnKeyUp="mascaraData(this,'data');"  onblur="javascript:verifica_data_novo(data)"  value="<?php print $data_inicio ?>"  onkeypress="return SomenteNumero(event)" />
            <font size="-1">Final: </font><input type="text" name="data_1" id="data_1"  size="6" maxlength="10" OnKeyUp="mascaraData(this,'data_1');" onblur="javascript:verifica_data_novo(data_1)"  value="<?php print $data_fim ?>" onkeypress="return SomenteNumero(event)" /></td>
          </tr>
             <tr>
            	<td width="118"><font size="-1"><strong>Taxa Contrato: </strong></font></td>
               <td width="266"><input type="text" name="taxa" id="taxa" size="6" maxlength="10" disabled="disabled" value=<?php print $taxa ?>
                />%</td>
          </tr>
          
          </table>
          
           
          <p>&nbsp;</p>
          <b><p>Importante</p></b>
          <table border="1">
          <tr>
          <td>
          <p align="center"><font size="-1"> Os fechamentos s&atilde;o dos dias 21 &agrave; 20 do m&ecirc;s seguinte.</font></p>
          <font size="-1"> Exemplo:
            21/01/2015 at&eacute; 20/02/2015.</font></td>
          </tr>
          </table>
          <table width="534" border="0">
          <tr>
          <td>&nbsp;</td>
       
            </tr>
    </table>

          <table width="700" border="0" align="center">
          <tr>
            	<td align="center" colspan="3">
                	<input type="submit" name="pesquisar" id="pesquisar" class="botao_site" value=" Pesquisar " onclick="javascript:checardatas()" />
                	<input type="button" name="limpar" id="limpar" class="botao_site" value=" Limpar " onclick="limpa()"/>  
                    <input type="submit" name="cancelar" id="fechar" class="botao_site" value=" Fechar " />
                </td>
            </tr>
    </table>
<?php
			
	 if ($pesquisar !=""){	
		
	$nova_dt_inicial =
	implode(preg_match("~\/~", $data_inicio) == 0 ? "/" : "",
	array_reverse(explode(preg_match("~\/~", $data_inicio) == 0 ? "-" : "/", $data_inicio)));
				
	$nova_dt_final =
	implode(preg_match("~\/~", $data_fim) == 0 ? "/" : "",
	array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim)));
	
		$query="select taxa_contrato from taxa_contratos";
			$result= odbc_exec($conSQL, $query);
			$taxa2= odbc_result($result, 1);

/*			
$query = "SELECT 
			'R$ '+dbGerenciamento.dbo.formatDinheiro (
			CAST( SUM(CAST(VALOR_TRANSACAO AS DECIMAL (10,2))/100) AS DECIMAL (10, 2))),
			'R$ '+dbGerenciamento.dbo.formatDinheiro (CAST( SUM(CAST(VALOR_TRANSACAO AS DECIMAL (10,2))/100)*$taxa2 AS DECIMAL(10, 2))) VALOR_TARIFA
			from layout_arquivo
			WHERE DATA_TRANSACAO BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'";
//print $query;
$result= odbc_exec($conSQL, $query) or die ('Erro ao montar tabela');
*/
$query = "SELECT
			'R$ '+dbGerenciamento.dbo.formatDinheiro (
			CAST( SUM(CAST(VALOR_TRANSACAO AS DECIMAL (10,2))/100) AS DECIMAL (10, 2))),
			'R$ '+dbGerenciamento.dbo.formatDinheiro (CAST( SUM(CAST(VALOR_TRANSACAO AS DECIMAL (10,2))/100)*0.008 AS DECIMAL(10, 2))) VALOR_TARIFA	
			from (	
					select distinct documento_extrato, num_autorizacao_2, valor_transacao, data_transacao, cpf_cnpj_contratado
					from layout_arquivo
					WHERE DATA_TRANSACAO BETWEEN '$nova_dt_inicial' AND '$nova_dt_final'
				 )dados";
//print $query;
$result= odbc_exec($conSQL, $query) or die ('Erro ao montar tabela');



print"<table border='0' width='100'>
		<tr>
			<td>&nbsp;</td> 
		</tr>
		</table>";
print"<table width='500' border='1'>
	<tr>
		<td bgcolor='#CCCCCC' width='6%'><font color='black' size='-1'><b><center>Valor Transacao</center></b></font></td>
		<td bgcolor='#CCCCCC' width='10%'><font color='black' size='-1'><b><center>Valor Tarifa</center></b></font></td></tr>";

	while(odbc_fetch_row($result))
{              				                   
									
	$VALOR_TRANSACAO= odbc_result($result, 1);
	$VALOR_TARIFA= odbc_result($result, 2);
;
	
		    
				print"
								<tr>
									<td bgcolor='#FFFFFF' ><center><font size='-1'>".$VALOR_TRANSACAO."</font></a></center>
									</td>
								<td bgcolor='#FFFFFF' ><center><font size='-1'>".$VALOR_TARIFA."</font></a></center>
								</td>
								</tr>
								</table>";
}
					
			?>
            								
		
  
  </fieldset>
</form>

</body>
</html>
<?php

}//else
}
?>
