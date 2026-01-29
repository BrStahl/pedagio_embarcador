<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/rel_pedagio_digitado.php";
$logado    = $_SESSION["usuario_logado"];
//$acesso	   = valida_acesso($conSQL, $localItem, $logado);
$acesso = "permitido";

if($acesso <> "permitido"){
    grava_acesso($conSQL, $localItem, $logado, 2, $vObservacao);

    print "
        <script language = 'JavaScript'>
           alert('Acesso negado para esta p�gina');
           window.location='centro.php';
		</script>
    ";
}//elseif
else{
	 grava_acesso($conSQL, $localItem, $logado, 1, $vObservacao);


$selectOpertationSpot = $_POST["selectOpertationSpot"];

if($shut != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}

if($clean != ""){
	print "
        <script language = 'JavaScript'>
           window.location='rel_pedagio_digitado.php';
		</script>";
}

//OPERATION SPOT LIST
$query_list = "SELECT 
					P.NOME_FANTASIA
					
				FROM
					CARGOSOL..PONTO_OPERACAO AS PO WITH (NOLOCK)
				JOIN CARGOSOL..PESSOA AS P WITH (NOLOCK)
						ON P.PESSOA_ID = PO.PESSOA_ID
				
				WHERE
					PO.TAB_STATUS_ID = 1
			    ORDER BY P.NOME_FANTASIA";
$result_list = odbc_exec($conSQL, $query_list) or die ("Error - query  operation spot list");  
$listOperationSpot = '';
while(odbc_fetch_row($result_list)){
	$nameOperationSpot = odbc_result($result_list ,1);
	
	if($selectOpertationSpot == $nameOperationSpot){
		$selected = "selected='selected'";
	}else{
		$selected = "";
	}
	
	$listOperationSpot .=  "<option value='$nameOperationSpot' $selected>$nameOperationSpot</option>";
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

.title{
	background-color:#CCCCCC;
	text-align:center;
	font-size:0.9em;
	font-weight: bold;
}

.content{
	background-color:#FFFFFF;
	text-align:center;
	font-size:0.8em;
}


</style>

<script>
function dataInsert(value, date, driverID, freightID, type, counter){
	//alert(value);
	//alert(date);
	//alert(driverID);
	//alert(freightID);
	//alert(type);
	//alert(counter);
	/*
	console.log(value+' '+date+' '+driverID+' '+freightID+' '+type+' '+counter);
	return false;
	*/

	$.ajax({
		type:"POST",//define o met�do de passagem de parametros
		url: "includes/insere_pedagio.php", //chama uma pagina
		data: "value="+value+"&date="+date+"&driverID="+driverID+"&freightID="+freightID+"&type="+type, //passa os parametros, se necess�rio
		success:function(msg){  //pega o retorno da pagina chamada
					
					if(msg.indexOf("Erro ao atualizar pedagio") != -1 || msg.indexOf("Erro ao executar SP") != -1){
							if(type == 1){
								alert("Favor preencher um valor v\u00e1lido");
								document.getElementById("value"+counter).value = '';
								document.getElementById("value"+counter).focus();
							}else
								if(type == 2){
									alert("Favor preencher uma data e hora v\u00e1lida");
									document.getElementById("date"+counter).value = '';
									document.getElementById("date"+counter).focus();
								}
					}else
						var dados = msg.split("|");
						//alert(msg);						
						if(dados[0].indexOf("Pedagio ja existente") != -1){
							alert("Ped\u00e1gio j\u00e1 inserido anteriormente com o mesmo valor e data na viagem "+dados[1]);
							if(type == 1){
								document.getElementById("value"+counter).value = '';
								document.getElementById("value"+counter).focus();
							}else
								if(type == 2){
									document.getElementById("date"+counter).value = '';
									document.getElementById("date"+counter).focus();
								}
						}
				}
	});
}

function insere_id_pamcard(value,freightID){
	//alert(value);
	//alert(date);
	//alert(driverID);
	// alert(freightID);
	//alert(type);
	//alert(counter);
	$.ajax({
		type:"POST",//define o met�do de passagem de parametros
		url: "includes/insere_pedagio.php", //chama uma pagina
		data: "value="+value+"&freightID="+freightID+"&op=insere_id", //passa os parametros, se necess�rio
		success:function(msg){  //pega o retorno da pagina chamada
					
					if(msg.indexOf("Erro ao atualizar pedagio") != -1 || msg.indexOf("Erro ao executar SP") != -1){
							if(type == 1){
								alert("Favor preencher um valor v\u00e1lido");
								document.getElementById("value"+counter).value = '';
								document.getElementById("value"+counter).focus();
							}else
								if(type == 2){
									alert("Favor preencher uma data e hora v\u00e1lida");
									document.getElementById("date"+counter).value = '';
									document.getElementById("date"+counter).focus();
								}
					}else
						var dados = msg.split("|");
						//alert(msg);						
						if(dados[0].indexOf("Pedagio ja existente") != -1){
							alert("Ped\u00e1gio j\u00e1 inserido anteriormente com o mesmo valor e data na viagem "+dados[1]);
							if(type == 1){
								document.getElementById("value"+counter).value = '';
								document.getElementById("value"+counter).focus();
							}else
								if(type == 2){
									document.getElementById("date"+counter).value = '';
									document.getElementById("date"+counter).focus();
								}
						}
				}
	});
}

function maskIt(w,e,m,r,a){
	// Cancela se o evento for Backspace
	if (!e) var e = window.event
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	
	// Vari�veis da fun��o
	var txt  = (!r) ? w.value.replace(/[^\d]+/gi,'') : w.value.replace(/[^\d]+/gi,'').reverse();
	var mask = (!r) ? m : m.reverse();
	var pre  = (a ) ? a.pre : "";
	var pos  = (a ) ? a.pos : "";
	var ret  = "";

	if(code == 9 || code == 8 || txt.length == mask.replace(/[^#]+/g,'').length) return false;
		// Loop na m�scara para aplicar os caracteres
		for(var x=0,y=0, z=mask.length;x<z && y<txt.length;){
			if(mask.charAt(x)!='#'){
				ret += mask.charAt(x); x++; 
			}else {
				ret += txt.charAt(y); y++; x++; 
			} 
		}
				
		// Retorno da fun��o
		ret = (!r) ? ret : ret.reverse()	
		w.value = pre+ret+pos; }
		// Novo m�todo para o objeto 'String'
		String.prototype.reverse = function(){
		return this.split('').reverse().join(''); 
};

function onlyValue(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58) || tecla == 46 || tecla == 44 || tecla==8 || tecla==0){ 
		return true;
	}else{ 
		return false;
    }
}

function onlyDateTime(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58) || tecla == 46 || tecla==8){ 
		return true;
	}else{ 
		return false;
    }
}

$(document).ready(function(){
	$("#driver").autocomplete("includes/completa_motorista.php", {
		width:450,
		selectFirst: false
		//reverter: true
	});
});

</script>

</head>
<body>
<form action="" name="form" method="post" enctype="multipart/form-data" style="width:750px">
<fieldset>
<legend>Relat&oacute;rio - Ped&aacute;gios Digitados</legend> 

<?php
	if ($onlyWithoutToll == true){
		$check = "checked='checked'";
	}else{
		$check = "";
	}
?>

<table width="616" border="1" >
	<tr>
	  <td width="21%"><strong><font size="-1">Data:</strong></font><FONT color="#FF0000" size="-1">*</FONT></td>
	  <td width="79%">Inicial:<input type="text" name="data"    id="data"   size="6" value="<?php print $data; ?>"   />&nbsp;&nbsp;
      				  Final:  <input type="text" name="data_1"  id="data_1" size="6" value="<?php print $data_1; ?>" />
      </td>
	</tr>
	<tr>
	  <td width="21%"><strong><font size="-1">Manifesto: </strong></td>
	  <td width="79%"><input type="text" name="manifesto" id="manifesto" size="39" value="<?php print $manifesto; ?>" /></td>
	</tr>
	<tr>
	  <td width="21%"><strong><font size="-1">N&ordm; Viagem:</strong></td>
	  <td width="79%"><input type="text" name="trip" id="trip" size="39" value="<?php print $trip; ?>" /></td>
	</tr>
    <tr>
	  <td><strong><font size="-1">Motorista:</strong></td>
	  <td>
	    <input type="text" name="driver" id="driver" size="39" value="<?php print $driver; ?>" /></td>
	</tr>
    <tr>
	  <td><strong><font size="-1">P. Opera&ccedil;&atilde;o:</strong></td>
	  <td>
      	<select name="selectOpertationSpot" id="selectOpertationSpot">
	    	<option></option>
        	<?php 
				echo $listOperationSpot;
			?>
	    </select>
      </td>
	</tr>
    <tr>
	  <td><strong><font size="-1">Sem ped&aacute;gio:</strong></td>
	  <td>
	    <input type="checkbox" name="onlyWithoutToll" id="onlyWithoutToll" <?php print $check; ?> /></td>
	</tr>
	<tr>
	  <td colspan="2" align="left">
	    <input type="submit" name="search" id="search"  class="botao_site" value="Pesquisar"  />
        <input type="submit" name="clean"  id="clean"   class="botao_site" value="Limpar"     />
        <input type="submit" name="shut"   id="shut"    class="botao_site" value="Fechar"     />
	  </td>
	</tr>
</table>


    
    <?php
	
		if($search != ""){
			if(($data == "" || $data_1 == "") && $manifesto == ""){
				echo "<script language = 'javascript'> alert('Favor preencher a data');</script>";
			}else{
				
				echo "<table border='0'>
					  	<tr>
						   <td>&nbsp;</td>
						</tr>
					  </table>";

				$new_date = implode(preg_match("~\/~", $data) == 0 ? "/" : "-",
							array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
							
				$new_date1 = implode(preg_match("~\/~", $data_1) == 0 ? "/" : "-",
							 array_reverse(explode(preg_match("~\/~", $data_1) == 0 ? "-" : "/", $data_1)));
				$condition = "";

				if($data && $data_1){
					$condition = "AND V.DATA_INCL >= '$new_date' AND V.DATA_INCL < DATEADD(DD,1,'$new_date1')";
				}
				
				if($trip != ""){
					$condition .= " AND V.VIAGEM_ID IN ($trip)";
				}
				
				if($driver != ""){
					$condition .= " AND MOTORISTA.NOME_FANTASIA = '$driver'";
				}
				
				if($onlyWithoutToll == true){
					$condition .= " AND VF.VAL_PEDAGIO IS NULL";
				}
				
				if($selectOpertationSpot != ""){
					$condition .= " AND P_PO.NOME_FANTASIA = '$selectOpertationSpot'";
				}

				if($manifesto != ""){
					$condition .= "AND MR.num_manifesto = $manifesto";
				}
				
				$query="SELECT DISTINCT
							V.VIAGEM_ID VIAGEM, 
							CONVERT(VARCHAR(10), V.DATA_INCL, 103)+' '+CONVERT(VARCHAR(5), V.DATA_INCL, 108) 'EMISSAO VIAGEM',
							P_PO.NOME_FANTASIA 'PONTO OPERACAO',
							VF.COLABORADOR_ID 'COLABORADOR ID',
							MOTORISTA.NOME_FANTASIA MOTORISTA,
							ORIGEM.MUNICIPIO+' - '+ORIGEM.UF ORIGEM,
							OVA_DESOVA.MUNICIPIO+' - '+OVA_DESOVA.UF 'OVA/DESOVA',
							DESTINO.MUNICIPIO+' - '+DESTINO.UF DESTINO,
							CAST(VF.VAL_PEDAGIO AS MONEY) 'VALOR PEDAGIO',
							CASE WHEN VF.VAL_PEDAGIO IS NULL 
									THEN NULL
									ELSE CONVERT(VARCHAR(10), VF.DATA_EMISSAO, 103)+' '+CONVERT(VARCHAR(5), VF.DATA_EMISSAO, 108) 
							END 'DATA HORA PEDAGIO',
							CONVERT(VARCHAR(10), VF.DATA_ULT_ALT, 103)+' '+CONVERT(VARCHAR(5), VF.DATA_ULT_ALT, 108) 'DATA HORA ULT ALT',
							VF.VALE_FRETE_ID,
						  CASE WHEN VERIFCA_LOG.LOG_SISTEMA_TEXTO_ID != '' THEN 1 ELSE 0 END,
							CASE WHEN FCP.FECHAMENTO_CONCILIACAO_PAMCARY_ID != '' THEN 1 ELSE 0 END,
							--RPD.id_pamcard ,
							ISNULL(RPD.id_pamcard,VF.Num_Comprovante_Pedagio) id_pamcard,
							MR.num_manifesto,
							PO.PONTO_OPERACAO_ID,
							MR.MANIFESTO_ROD_ID
							
						FROM 
							CARGOSOL..VIAGEM AS V WITH (NOLOCK)
						JOIN CARGOSOL..MANIFESTO_ROD AS MR WITH (NOLOCK)
								ON MR.VIAGEM_ID = V.VIAGEM_ID
						JOIN CARGOSOL..VALE_FRETE AS VF WITH (NOLOCK)
								ON VF.VALE_FRETE_ID = MR.VALE_FRETE_ID
						JOIN CARGOSOL..PONTO_OPERACAO AS PO WITH (NOLOCK)
								ON PO.PONTO_OPERACAO_ID = V.PONTO_OPERACAO_ID
						JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK)
								ON P_PO.PESSOA_ID = PO.PESSOA_ID
						JOIN CARGOSOL..COLABORADOR AS COLAB WITH (NOLOCK)
								ON COLAB.COLABORADOR_ID = MR.COLABORADOR_ID
						JOIN CARGOSOL..PESSOA AS MOTORISTA WITH (NOLOCK)
								ON MOTORISTA.PESSOA_ID = COLAB.PESSOA_ID
						LEFT JOIN CARGOSOL..MUNICIPIO ORIGEM WITH (NOLOCK) 
								ON ORIGEM.MUNICIPIO_ID = MR.ORIGEM_ID	
						LEFT JOIN CARGOSOL..MUNICIPIO OVA_DESOVA WITH (NOLOCK) 
								ON OVA_DESOVA.MUNICIPIO_ID = MR.OVA_DESOVA_ID	
						LEFT JOIN CARGOSOL..MUNICIPIO DESTINO WITH (NOLOCK) 
								ON DESTINO.MUNICIPIO_ID = MR.DESTINO_ID
						LEFT JOIN 
							(SELECT
									LST.LOG_SISTEMA_TEXTO_ID,
									MR.VIAGEM_ID
							   FROM
								 CARGOSOL..MANIFESTO_ROD AS MR WITH (NOLOCK)
								 JOIN CARGOSOL..VALE_FRETE AS VF WITH (NOLOCK)
									ON VF.VALE_FRETE_ID = MR.VALE_FRETE_ID
								 JOIN CARGOSOL..LOG_SISTEMA AS LS WITH (NOLOCK)
									ON LS.REGISTRO_ID = VF.VALE_FRETE_ID
								 JOIN CARGOSOL..LOG_SISTEMA_TEXTO AS LST WITH (NOLOCK)
									ON LST.LOG_SISTEMA_ID = LS.LOG_SISTEMA_ID
								 WHERE 
									
									 LST.DESC_TEXTO = 'ADICIONADO PELO SCA - CONCILICAO PAMCARY') AS VERIFCA_LOG 
								ON VERIFCA_LOG.VIAGEM_ID = V.VIAGEM_ID
						LEFT JOIN FECHAMENTO_CONCILIACAO_PAMCARY AS FCP WITH (NOLOCK)
								ON FCP.MES_FECHAMENTO = SUBSTRING(CONVERT(VARCHAR(10), V.DATA_INCL, 103),4,2)
								   AND FCP.ANO_FECHAMENTO = YEAR(V.DATA_INCL)
								   AND FCP.STATUS_FECHAMENTO = 'a'
						LEFT JOIN REL_PEDAGIO_DIGITADO RPD WITH(NOLOCK)
							ON RPD.VALE_FRETE_ID = VF.Vale_Frete_Id
							AND RPD.STATUS_ID = 'a'
						WHERE
						    (COLAB.TAB_TIPO_VINCULO_ID != 1 OR MOTORISTA.PESSOA_ID IN (4106, 25328, 25819)) 
							AND V.TAB_STATUS_ID = 1
							AND MR.TAB_STATUS_ID = 1
							AND MR.MANIFESTO_ROD_ID = (SELECT TOP 1
															M_WHERE.MANIFESTO_ROD_ID
														FROM
															CARGOSOL..MANIFESTO_ROD AS M_WHERE WITH (NOLOCK)
														WHERE
															M_WHERE.VIAGEM_ID = MR.VIAGEM_ID
														ORDER BY
															M_WHERE.MANIFESTO_ROD_ID DESC)
							$condition
						
						ORDER BY
							V.VIAGEM_ID";
				// echo "<pre>$query</pre>";
				$verify_result = odbc_exec($conSQL, $query) or die('Error to verify query data');
				$verify_trip = odbc_result($verify_result, 1);				
							
				$result = odbc_exec($conSQL, $query) or die('Error to query data');
				
				if($verify_trip != ""){
					echo "<table width='1101' border='1'>
							<tr>
							  <td width='100' class='title'>Viagem</td>
							  <td width='100' class='title'>Manifesto</td>
							  <td width='100' class='title'>ST/Pedidos</td>
							  <td width='100' class='title'>Data Emiss&atilde;o</td>
							  <td width='120' class='title'>Ponto Op.</td>
							  <td width='240' class='title'>Motorista</td>
							  <td width='120' class='title'>Origem</td>
							  <td width='120' class='title'>Ova/Desova</td>
							  <td width='120' class='title'>Destino</td>
							  <td width='71' class='title'>Id Pamcard</td>
							  <td width='71'  class='title'>Valor</td>
							  <td width='110'  class='title'>Data/Hora Recarga</td>
							</tr>";
				}else{
					echo "<font size='1' color='#FF0000'>Nenhum resultado encontrado</font>";
				}
				
				$counter = 1;
				
				while(odbc_fetch_row($result)){
					
					$tripNumber          = odbc_result($result, 1);
					$dateIssue           = odbc_result($result, 2);
					$operationSpot       = odbc_result($result, 3);
					$driverID            = odbc_result($result, 4);
					$driverName          = odbc_result($result, 5);
					$origin              = odbc_result($result, 6);
					$fillEmpty           = odbc_result($result, 7);
					$destination         = odbc_result($result, 8);
					$valueToll       	 	 = odbc_result($result, 9);
					$dateToll            = odbc_result($result, 10);
					$lastChangeDate      = odbc_result($result, 11);
					$freightID           = odbc_result($result, 12);
					$verifySystemLog     = odbc_result($result, 13);
					$verifyPeriodClosure = odbc_result($result, 14);
					$id_pamcard			     = odbc_result($result, 15);
					$manifesto			     = odbc_result($result, 16);
					$po_id		  				 = odbc_result($result, 17);
					$manifesto_id				 = odbc_result($result, 18);
					
					// if($fillEmpty == ""){
					// 	$fillEmpty = "&nbsp;";
					// }
					
					$backgroundColorInput = "#ffffff";
					if($verifyPeriodClosure == 0){//VERIFICA SE O PERIODO EST� ABERTO
						/*if($valueToll != ""){
							if($verifySystemLog == 1){   
								$disabledToll = "";
							}else{
								$disabledToll = "disabled='disabled'";
							}
						
						}else{
							$disabledToll = "";
						}*/
						$disabledToll = "";
					}else{
						$disabledToll = "disabled='disabled'";
						$backgroundColorInput = "#d3d3d3";
					}
					
					if($verifySystemLog == 1){
						$trueDateToll = $lastChangeDate;
					}else{
						$trueDateToll = $dateToll;
					}
					
					if($valueToll != ""){
						$valueToll = number_format($valueToll, 2, ',', '.');
					}
					if(($fillEmpty)||(!$fillEmpty && $origin != $destination)){
						echo "<tr>
									<td width='100' class='content'>$tripNumber</td>
									<td width='100' class='content'>$manifesto</td>";
									if($po_id == 15){
										echo "<td width='100' class='content'>
											<a href=javascript:pagina('detalha_manifesto.php?manifesto_id=$manifesto_id','600','200','dados')><font size='-2'><u>Link</u></a>
										</td>";
									}else{
										echo "<td width='100' class='content'>&nbsp;</td>";
									}
									echo "
									<td width='100' class='content'>$dateIssue</td>
									<td width='120' class='content'>$operationSpot</td>
									<td width='240' class='content'>$driverName</td>
									<td width='120' class='content'>$origin</td>
									<td width='120' class='content'>$fillEmpty</td>
									<td width='120' class='content'>$destination</td>
									<td width='71' class='content'>
										<input type='text' name='pancard_id$counter' id='pancard_id$counter' size='6' $disabledToll value='$id_pamcard' 
											style='text-align:right; background-color:$backgroundColorInput;'
											onfocus='this.style.backgroundColor=".chr(34)."#dcdcdc".chr(34).";' 
											onblur='this.style.backgroundColor=".chr(34)."#ffffff".chr(34)."'
											onchange='insere_id_pamcard(this.value, $freightID)'	
										/>
									</td>
									<td width='71' class='content'>
										<input type='text' name='value$counter' id='value$counter' size='6' $disabledToll value='$valueToll' 
											style='text-align:right; background-color:$backgroundColorInput;'
											onfocus='this.style.backgroundColor=".chr(34)."#dcdcdc".chr(34).";' 
											onblur='this.style.backgroundColor=".chr(34)."#ffffff".chr(34)."'
											onKeyPress='maskIt(this,event,".chr(34)."###.###,#".chr(34).",true)' 
											onKeyPress='return onlyValue(event)'
											onchange='dataInsert(this.value, document.form.date$counter.value, $driverID, $freightID, 1, $counter)'	
										/>
									</td>
									<td width='110' class='content'>
										<input type='text' name='date$counter' id='date$counter' size='12' maxlength='16' $disabledToll value='$trueDateToll'
												style='text-align:center; background-color:$backgroundColorInput;'
											onfocus='this.style.backgroundColor=".chr(34)."#dcdcdc".chr(34).";' 
											onblur='this.style.backgroundColor=".chr(34)."#ffffff".chr(34)."; 
														dataInsert(document.form.value$counter.value, this.value, $driverID, $freightID, 2, $counter)'
											onKeyPress='formatar(this,".chr(34)."##/##/#### ##:##".chr(34)."); return onlyDateTime(event)'
										/>
									</td>
								</tr>";
					}
						  					
					$counter++;
				}
				
			}//CLOSE 'IF DATE'
		}//CLOSE 'IF SEARCH'
	
		
	
	?>
    
    
    </table>

 </fieldset>
</form>

</body>
</html>
<?php

}//else
?>
