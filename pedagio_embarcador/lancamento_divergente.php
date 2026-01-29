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


$nsu 		= $_GET["nsu"];
$data 		= $_GET["data"];
$valor 		= $_GET["valor"];
$tipo 		= $_GET["tipo"];

if ($tipo == '')
	$tipo = 1;


$observacao 	= $_POST["observacao"];
$lancamento_id 	= $_POST["lancamento_id"];


$nsu	 	= str_replace('"','',$nsu);
$data 		= str_replace('"','',$data);
$valor 		= str_replace('"','',$valor);


	//pegando o id do usuario
	$query = "select id
			  from usuario
			  where usuario = '$logado'";
	$result = odbc_exec($conSQL, $query) ;
	$usuario_id = odbc_result($result,1);	



if($fechar != "")
{
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if ($gravar != "")
{


	if ($observacao == '')
		print "<script type='text/javascript'> alert(unescape('Favor preencher o campo observa%E7%E3o'));</script>";
	else	
	{
		if ($lancamento_id == '')
		{

			$query = "insert into lancamento_divergente (nsu, observacao, valor, usuario_id, data_gravacao, status_id) 
					  values ('$nsu', '$observacao', '$valor', '$usuario_id', getdate(), 'p')";	
			//print $query;
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro1 a gravar o lancamento divergente<br>");
			
			$query = "SELECT @@IDENTITY AS Ident";
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro2 ao selecionar o id<br>");
			$result = odbc_exec($conSQL, $query) ;
			$lancamento_id = odbc_result($result, 1);		
		}
		else
		{
			$query = "update lancamento_divergente 
					  set observacao = '$observacao'
					  where id = $lancamento_id";	
			//print $query;
			odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro2 ao atualizar o lancamento divergente<br>");			
			
		}
		
		//fecha a tela
		print"<script language='javascript'>open(location, '_self').close();</script>";		
		
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
function exclui_lancamento(elmnt){	
	if (confirm(unescape("Deseja realmente excluir o lan%E7amento?")))
	{
			//alert("entrou na exclusão");
			if (elmnt.value != ""){
				$.ajax({type: "POST",//define o metódo de passagem de parametros
					url: "includes/exclui_lancamento.php", //chama uma pagina
					data: "lancamento_id="+elmnt, //passa os parametros, se necessário
					success: function(msg){  //pega o retorno da pagina chamada
						alert(msg);
						
						open(location, '_self').close();
				
					}
				});
			}
		
	}
	
}
</script>

</head>
<body>
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:1100px">
<fieldset>
<legend>Lan&ccedil;amento Divergente</legend> 


<?php 

	//convertendo a data
	$data_bradesco = 
	implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data))); 
	
	if ($tipo != 2)
	{
		$query = "select distinct 
					convert(varchar(10), FXCX.data, 103),
					FXCX.NUMERODOCUMENTO,
					ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO),
					cast(ABS(FXCX.valor) as numeric(15,2)),
					ld.id,
					ld.observacao
					from Corpore..FXCX with (nolock)
					left join lancamento_divergente ld with (nolock) on
						ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS
						
					LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
						FRECONCILRELAC.IDXCX = FXCX.IDXCX
					AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
					
					LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
						FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
					AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA
								
					where FXCX.NUMERODOCUMENTO like '%$nsu%'
					AND FXCX.COMPENSADO = 1
					AND dateadd(dd,datediff(dd,0,FXCX.DATA),0)  = '$data_bradesco'
					AND FXCX.VALOR = '-$valor'";
	}
	else
	{
		$query = "select '$data', nsu, 'REGISTRO NAO ENCONTRADO DO BRADESCO', valor, id, observacao
					from lancamento_divergente
					where nsu = '$nsu'	
					and 2 = '$tipo'
					
union					
select '$data', '$nsu', 'REGISTRO NAO ENCONTRADO DO BRADESCO', '$valor', null, null					
					";
	}
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
	$data 			= odbc_result($result, 1);
	$nsu_bradesco 	= odbc_result($result, 2);
	$historico		= odbc_result($result, 3);
	$valor			= odbc_result($result, 4);
	$lancamento_id	= odbc_result($result, 5);	
	$observacao		= odbc_result($result, 6);
	
	
	print "<table width='1100' height='37' border='1'> 
				<tr>
					  <td width='100' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Data</strong></div></td>
					  <td width='150' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>N&ordm; Documento</strong></div></td>
					  <td width='300' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
					  <td width='100' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Valor</strong></div></td>
					  <td width='450' bgcolor='#CCCCCC'><font size='-1'>
						<div align='center'><strong>Observa&ccedil;&atilde;o</strong><strong></strong></div></td>
				  </tr>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$data."</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$nsu_bradesco."</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$historico."</strong></font></div></td>
					  <td bgcolor='#FFFFFF'><div align='center'>
					  	<font size='-1'>".str_replace('.',',',$valor)."</strong></font></div></td>						
					  
					  <td bgcolor='#FFFFFF'><font size='-1' >
						<font size='-1' color='#FF0000'>
						<label for='observacao'></label>
						<div align='center'>
						  <input name='observacao' type='text' id='observacao' size='60' value='$observacao' />
						  <font size='-1' >
						</div></td>
					  </tr>						 
		   </table>";				

?>



<table width="619" border="0" align="center">        
        <tr>
          <td height="22" class="txt_home"><input name="lancamento_id" type="hidden" id="lancamento_id" value="<?php print $lancamento_id ?>" size="3" />
          <input name="valor" type="hidden" id="valor" value="<?php print $valor ?>" size="3" /></td>
          <td class="txt_home">&nbsp;</td>
          <td class="txt_home">&nbsp;</td>
      </tr>
</table>


<table width='619' border='0' align='center'> 
    <tr>
      <td class='txt_home'>
        <div align="center">
          <input name='gravar' type='submit' class='botao_site' value=' Gravar ' id='gravar' />      
          <input name='excluir' type='button' class='botao_site' value=' Excluir ' id='excluir' 
          onclick="javascript:exclui_lancamento(<?php print $lancamento_id ?>)" />
          <input name='fechar' type='submit' class='botao_site' value=' Fechar ' id='fechar' />
        </div></td>
      </tr>
</table>	
	




 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
