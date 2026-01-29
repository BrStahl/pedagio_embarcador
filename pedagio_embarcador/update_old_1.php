<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/update.php";
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
	$data = date("d/m/Y H:i:s");	

	//pegando dados da ultima atualização
	$query = "select top 1 
			  CONVERT(varchar(10), data_atualizacao, 103),
			  CONVERT(varchar(10), data_atualizacao, 108),
			  qtde_arquivos
			  from log_atualizacao_conciliacao
			  order by id desc";
	$result = odbc_exec($conSQL, $query) ;	
	$data_ultima_atualizacao = odbc_result($result, 1);	
	$hora_ultima_atualizacao = odbc_result($result, 2);	
	$qtde_ultima_atualizacao = odbc_result($result, 3);		
						

if($fechar != "")
{
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}


if($atualizar != "")
{
	if ($logado != '')
	{
		//pegando o usuario
		$query = "Select id, convert(varchar(10), getdate(), 103), convert(varchar(5), getdate(), 108), getdate()
				  From usuario
				  Where usuario = '$logado'";
		$result = odbc_exec($conSQL, $query);
		$usuario_id = odbc_result($result, 1);
		$data_atual = odbc_result($result, 2);		
		$hora_atual = odbc_result($result, 3);
		$data_verificacao = odbc_result($result, 4);		
			
	
		$query = "select distinct data_transacao
					from layout_arquivo
					where status_id = 'p'
					and tipo_transacao <> 1
					and data_transacao <> 0";
		$result = odbc_exec($conSQL, $query);
	
		$contador = 0;
	
		 while(odbc_fetch_row($result))
		 {
			$data_layout = odbc_result($result, 1);
	
			//ATUALIZANDO Corpore(ADTO)
			$query1 = "UPDATE FXCX
						SET FXCX.NUMERODOCUMENTO = 
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8)
						FROM
							Corpore..FXCX FXCX
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2    
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'		  
						WHERE
							LAYOUT_ARQUIVO.data_transacao = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query1;
			$result1 = odbc_exec($conSQL, $query1) or die('Erro ao atualizar o Corpore');


			//ATUALIZANDO Corpore (saldo)
			$query4 = "UPDATE FXCX
						SET FXCX.NUMERODOCUMENTO = 
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8)
						FROM
							Corpore..FXCX FXCX
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2    
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.DATA_TRANSACAO = CONVERT(VARCHAR,FLAN.DATABAIXA,112)
						AND LAYOUT_ARQUIVO.VALOR_TRANSACAO = REPLACE(LANCAMENTO_OPERACAO.VALOR_LANC_RS,'.','')
						AND cpf_cnpj_contratado COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS = CASE WHEN PESSOA.INDICATIVO_PF_PJ = 'PF'
																					   THEN PESSOA.PF_CPF
																					   ELSE PESSOA.PJ_CGC
																				  END		
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query1;
			$result4 = odbc_exec($conSQL, $query4) or die('Erro2 ao atualizar o Corpore');

	
			//ATUALIZANDO TABELA LAYOUT ARQUIVO (ADTO)
			$query2 = "UPDATE LAYOUT_ARQUIVO
						SET USER_RESP_ATUALIZACAO = $usuario_id, DATA_ATUALIZACAO = GETDATE(), LAYOUT_ARQUIVO.STATUS_ID = 'a'
						FROM
							Corpore..FXCX FXCX
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.LANCAMENTO_OPERACAO_ID = FLAN.CAMPOALFAOP2    
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = FLAN.CAMPOALFAOP2		
							AND LAYOUT_ARQUIVO.STATUS_ID = 'P'		  
						WHERE
							LAYOUT_ARQUIVO.data_transacao = $data_layout
							AND FLAN.CODTDO = 'ADT'	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query2;						
			$result2 = odbc_exec($conSQL, $query2) or die('Erro ao atualizar a tabela layout_arquivo - ADTO');

	
			//ATUALIZANDO TABELA LAYOUT ARQUIVO (SALDO)
			$query5 = "UPDATE LAYOUT_ARQUIVO
						SET USER_RESP_ATUALIZACAO = $usuario_id, DATA_ATUALIZACAO = GETDATE(), LAYOUT_ARQUIVO.STATUS_ID = 'a'
						FROM
							Corpore..FXCX FXCX
						JOIN Corpore..FLANBAIXA FLANBAIXA WITH(NOLOCK) ON
							FLANBAIXA.IDXCX = FXCX.IDXCX
							AND FLANBAIXA.STATUS = 0
						AND FLANBAIXA.CODCOLIGADA = FXCX.CODCOLIGADA
						JOIN Corpore..FLAN FLAN WITH(NOLOCK) ON
							FLAN.IDLAN = FLANBAIXA.IDLAN
						AND FLAN.CODCOLIGADA = FLANBAIXA.CODCOLIGADA	
						
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LANCAMENTO_OPERACAO WITH(NOLOCK) ON
							LANCAMENTO_OPERACAO.BOLETIM_SERVICO_ID = FLAN.CAMPOALFAOP2    
						JOIN CARGOSOL..PESSOA PESSOA WITH (NOLOCK) ON
							PESSOA.PESSOA_ID = ISNULL(LANCAMENTO_OPERACAO.COLABORADOR_ID,LANCAMENTO_OPERACAO.FORNECEDOR_ID)
							
						JOIN LAYOUT_ARQUIVO WITH(NOLOCK) ON 
							LAYOUT_ARQUIVO.DATA_TRANSACAO = CONVERT(VARCHAR,FLAN.DATABAIXA,112)
						AND LAYOUT_ARQUIVO.VALOR_TRANSACAO = REPLACE(LANCAMENTO_OPERACAO.VALOR_LANC_RS,'.','')
						AND cpf_cnpj_contratado COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS = CASE WHEN PESSOA.INDICATIVO_PF_PJ = 'PF'
																					   THEN PESSOA.PF_CPF
																					   ELSE PESSOA.PJ_CGC
																				  END		
						AND LAYOUT_ARQUIVO.STATUS_ID = 'P'																				  					  
						WHERE
						LAYOUT_ARQUIVO.data_transacao = $data_layout
						AND FLAN.CODTDO IN ('TRPA', 'TRPJ', 'DESPVIAG')	
						AND FXCX.CODCOLIGADA = 1
						--AND FXCX.COMPENSADO = 1
						";
			//print "<br>".$query2;						
			$result5 = odbc_exec($conSQL, $query5) or die('Erro ao atualizar a tabela layout_arquivo - SALDO');
		
	
			//$contador = $contador + $somatoria;

	
		 }//fim while 


			//pegando a quantidade de atualizados
			$query3 = "select COUNT(*)
					  from layout_arquivo
					  where status_id = 'a'
					  and data_atualizacao >= '$data_verificacao'";
			$result3 = odbc_exec($conSQL, $query3) ;	
			$contador = odbc_result($result3, 1);		


		 
			//INSERINDO NA TABELA DE LOG
			$query = "insert into log_atualizacao_conciliacao (data_atualizacao, qtde_arquivos) 
						values (getdate(), $contador)";
			//print "<br>".$query2;						
			$result = odbc_exec($conSQL, $query) or die('Erro ao atualizar a tabela de log');

	
		 if ($data_layout != '')
			print "<script type='text/javascript'> alert(unescape('Atualiza%e7%e3o feita com sucesso!'));</script>";
		 else
			print "<script type='text/javascript'> alert(unescape('Nenhum registro foi atualizado!'));</script>";
			
			
			//atualiza o PHP com as novas atualizações
			print"<script language='javascript'>window.location.href='update.php';</script>";
			
	}
	else
		print "<script type='text/javascript'> alert(unescape('Sess%e3o Expirada, favor logar novamente!'));</script>";
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
<form action="" name="form1" method="post" enctype="multipart/form-data" style="width:600px">
<fieldset>
<legend>Atualiza&ccedil;&atilde;o</legend> 

    <table width="502" border="0" align="center">
	<tr>
	  <td width="421">&nbsp;</td>
	</tr>
      <tr>
	      <td><div align="center">
	        <input name="atualizar" type="submit" class="botao_site" value=" Atualizar Dados" id="atualizar" />
          </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
	  <td><div align="center"><?php 
				print "<font color='#0000FF' size='-1'><b>&Uacute;ltima Atualiza&ccedil;&atilde;o realizada em ".$data_ultima_atualizacao." &agrave;s ".$hora_ultima_atualizacao;
	  
	  	?>
	  </div></td>
	</tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>


<table width="491" border="0" align="center">        
        <tr>
          <td class="txt_home"><div align="center">
          <div align="center"><font color="#0000FF" size="-1"><b><?php print "Total de Lan&ccedil;amentos Financeiros RM Atualizados: ".$qtde_ultima_atualizacao ?></div></td>
      </tr>
        <tr>
          <td class="txt_home">&nbsp;</td>
      </tr>
        <tr>
          <td class="txt_home"><div align="center">
            <input name="fechar" type="submit" class="botao_site" value=" Fechar " id="fechar" />
          </div></td>
          <!--          <td class="txt_home"><input name="alterar" type="submit" class="botao_site" value=" Alterar " id="alterar" /></td> -->
        </tr>
</table>


 </fieldset>
</form>

</body>
</html>
<?php
}//else
?>
