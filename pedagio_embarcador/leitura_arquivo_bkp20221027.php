<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/leitura_arquivo.php";
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
	$data = date("Y-m-d_H:i:s");	
/*	

		$local_file = 'arquivo/MT140501.txt';
	
		$texto = file_get_contents($local_file);
		
		//linha 1		
		$tipo_registro 		=  substr($texto, 0, 1);
		$data_movimento 	=  substr($texto, 1, 4)."-".substr($texto, 5, 2)."-".substr($texto, 7, 2);
		$numero_sequencial 	=  substr($texto, 448, 6);
		
		//linha 2
		$tipo_registro_1 	=  substr($texto, 1002, 1);		
		$cnpj_contratante	= substr($texto, 1003, 14);
		$cnpj_ponto_emb		= substr($texto, 1017, 14);		
		$indicador_contrat	= substr($texto, 1031, 1);	
		$id_viagem			= substr($texto, 1032, 10);			
		$tipo_documento		= substr($texto, 1042, 2);
		$numero_documento	= substr($texto, 1044, 30);		
		$numero_contrato	= substr($texto, 1074, 30);		
		$numero_pamcard		= substr($texto, 1104, 10);									
					
		
		print "<br>$tipo_registro";	
		print "<br>$data_movimento";	
		print "<br>$numero_sequencial";	
		print "<br>$tipo_registro_1";	
		print "<br>$cnpj_contratante";		
		print "<br>$cnpj_ponto_emb";		
		print "<br>$indicador_contrat";		
		print "<br>$id_viagem";	
		print "<br>$tipo_documento";	
		print "<br>$numero_documento";
		print "<br>$numero_contrato";	
		print "<br>$numero_pamcard";																
		
*/							

if($fechar != ""){
		print"
		<script language='javascript'>
			open(location, '_self').close();
		</script>
	";
}



if($_POST['anexar'] <> "")
{

	//$nome_diretorio = 1;
	//$diretorio = "arquivos/$nome_diretorio";
	//mkdir($diretorio);
		
	/*	if(mkdir($diretorio))
    		echo "Diretório criado com sucesso.";
	    else
    		echo "Não foi possível criar o diretório.";
    */
		$dir = "arquivos";
			
		if (is_dir($dir)) {
		   if ($dh = opendir($dir)) {
			   while (($file = readdir($dh)) !== false) {
				   //unlink($dir."/".$file) ;
			   }//while
		   }//if
		}//if

		
		$workDir = "arquivos"; // define this as per local system
		
		// get temporary file name for the uploaded file
		
		$tmpName = basename($_FILES['file']['tmp_name']);
		$name 	 = basename($_FILES['file']['name']);
		$nome_arquivo = $name;
	
	
		if ($nome_arquivo != '')
		{
	
			$query  = "select nome_arquivo from anexo_leitura where rtrim(ltrim(nome_arquivo)) = rtrim(ltrim('$nome_arquivo'))";
			//print $query;
			$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao consultar se o arquivo já foi importado<br>");
			
			
			if(odbc_result($result, 1) == "")
			{
			
			// copy uploaded file into current directory
							
				move_uploaded_file($_FILES['file']['tmp_name'], $workDir."/".$tmpName) 
				or die("Cannot move uploaded file to working directory");
				
				copy ($workDir."/".$tmpName, $workDir."/".$nome_arquivo);
				
				
				unlink($workDir."/".$tmpName) or 
				die("Cannot delete uploaded file from working directory -- manual deletion recommended");
				
			
				//ABRE O ARQUIVO TXT
				$ponteiro = fopen("arquivos/$nome_arquivo", "r");
				$cont = 1;
				while (!feof ($ponteiro))
				{
					$linha = fgets($ponteiro, 4096);
			
					if ($cont == 1)
					{
						//monta o cabeçalho
						$tipo_registro 		=  substr($linha, 0, 1);
						//$data_movimento 	=  substr($linha, 1, 4)."-".substr($linha, 5, 2)."-".substr($linha, 7, 2);
						$data_movimento 	=  substr($linha, 1, 8);
						$numero_sequencial 	=  substr($linha, 447, 6);				
			
						$query = "insert into layout_cabecalho (data_inclusao, nome_arquivo, tipo_registro, data_movimento, numero_sequencial) 					  values (getdate(), '$nome_arquivo', $tipo_registro, $data_movimento, $numero_sequencial)";
						//print $query;
						odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao importar os dados do arquivo<br>");
			
						$query = "SELECT @@IDENTITY AS Ident";
						odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro2 ao selecionar o id do cabecalho<br>");
						$result = odbc_exec($conSQL, $query) ;
						$layout_cabecalho_id = odbc_result($result, 1);
			
						/*
						print "<br>$tipo_registro";	
						print "<br>$data_movimento";	
						print "<br>$numero_sequencial";				
						*/
					}
					else 
						//if ($cont == 11)
						{
							//monta o detalhe
							$tipo_registro_1 		= substr($linha, 0, 1);		
							$cnpj_contratante		= substr($linha, 1, 14);
							$cnpj_ponto_emb			= substr($linha, 15, 14);		
							$indicador_contrat		= substr($linha, 29, 1);	
							$id_viagem				= substr($linha, 30, 10);			
							$tipo_documento			= substr($linha, 40, 2);
							$numero_documento		= substr($linha, 42, 30);		
							$numero_contrato		= substr($linha, 72, 30);		
							$numero_pamcard			= substr($linha, 102, 10);		
							$data_cadastro_viagem	= substr($linha, 112, 8);							
							$status_viagem			= substr($linha, 120, 1);	
							$placa_veiculo			= substr($linha, 121, 8);					
							$categoria_veiculo		= substr($linha, 129, 2);	
							$pais_origem			= substr($linha, 131, 50);				
							$uf_cidade_origem		= substr($linha, 181, 2);				
							$cidade_origem			= substr($linha, 183, 50);	
							$pais_destino			= substr($linha, 233, 50);				
							$uf_cidade_destino		= substr($linha, 283, 2);				
							$cidade_destino			= substr($linha, 285, 50);	
							$data_embarque_viagem	= substr($linha, 335, 8);	
							$tipo_transacao			= substr($linha, 343, 1);	
							$status_transacao		= substr($linha, 344, 1);	
							$valor_transacao		= substr($linha, 345, 9);	
							$valor_taxa_transacao	= substr($linha, 354, 9);
							$usuario				= substr($linha, 363, 50);			
							$num_autorizacao		= substr($linha, 413, 10);	
							$data_transacao			= substr($linha, 423, 8);	
							$hora_transacao			= substr($linha, 431, 6);	
							$banco_autorizador		= substr($linha, 437, 3);		
							$documento_extrato		= substr($linha, 440, 7);	
							$transacao_sequencial	= substr($linha, 447, 6);	
							$valor_pedagio_solicit	= substr($linha, 453, 9);					
							$saldo_pedagio_cartao	= substr($linha, 462, 9);	
							$tipo_cartao			= substr($linha, 471, 2);	
							$cpf_favorecido			= substr($linha, 473, 11);	
							$nome_favorecido		= substr($linha, 484, 60);	
							$condicao_pagamento		= substr($linha, 544, 1);	
							$codigo_banco			= substr($linha, 545, 4);	
							$numero_agencia			= substr($linha, 549, 6);	
							$numero_conta_corrente	= substr($linha, 555, 10);		
							$sigla_erro				= substr($linha, 565, 4);																																																																																																																					
							$identificacao_parcela	= substr($linha, 569, 2);	
							$id_parcela_cliente		= substr($linha, 571, 20);					
							$cnpj_portador_pj		= substr($linha, 591, 14);	
							$numero_ciot			= substr($linha, 605, 12);	
							$protocolo_ciot			= substr($linha, 617, 4);	
							$cpf_cnpj_contratado	= substr($linha, 621, 14);																	
							$nome_contratado		= substr($linha, 635, 60);																	
							$num_autorizacao_2		= substr($linha, 695, 10);
							
							
							$tipo_registro_1 		= str_replace("'","",$tipo_registro_1);	
							$cnpj_contratante		= str_replace("'","",$cnpj_contratante);
							$cnpj_ponto_emb			= str_replace("'","",$cnpj_ponto_emb);		
							$indicador_contrat		= str_replace("'","",$indicador_contrat);	
							$id_viagem				= str_replace("'","",$id_viagem);			
							$tipo_documento			= str_replace("'","",$tipo_documento);
							$numero_documento		= str_replace("'","",$numero_documento);		
							$numero_contrato		= str_replace("'","",$numero_contrato);	
							$numero_pamcard			= str_replace("'","",$numero_pamcard);		
							$data_cadastro_viagem	= str_replace("'","",$data_cadastro_viagem);							
							$status_viagem			= str_replace("'","",$status_viagem);	
							$placa_veiculo			= str_replace("'","",$placa_veiculo);					
							$categoria_veiculo		= str_replace("'","",$categoria_veiculo);	
							$pais_origem			= str_replace("'","",$pais_origem);			
							$uf_cidade_origem		= str_replace("'","",$uf_cidade_origem);				
							$cidade_origem			= str_replace("'","",$cidade_origem);
							$pais_destino			= str_replace("'","",$pais_destino);				
							$uf_cidade_destino		= str_replace("'","",$uf_cidade_destino);				
							$cidade_destino			= str_replace("'","",$cidade_destino);
							$data_embarque_viagem	= str_replace("'","",$data_embarque_viagem);
							$tipo_transacao			= str_replace("'","",$tipo_transacao);	
							$status_transacao		= str_replace("'","",$status_transacao);
							$valor_transacao		= str_replace("'","",$valor_transacao);
							$valor_taxa_transacao	= str_replace("'","",$valor_taxa_transacao);
							$usuario				= str_replace("'","",$usuario);		
							$num_autorizacao		= str_replace("'","",$num_autorizacao);
							$data_transacao			= str_replace("'","",$data_transacao);	
							$hora_transacao			= str_replace("'","",$hora_transacao);	
							$banco_autorizador		= str_replace("'","",$banco_autorizador);		
							$documento_extrato		= str_replace("'","",$documento_extrato);	
							$transacao_sequencial	= str_replace("'","",$transacao_sequencial);	
							$valor_pedagio_solicit	= str_replace("'","",$valor_pedagio_solicit);				
							$saldo_pedagio_cartao	= str_replace("'","",$saldo_pedagio_cartao);	
							$tipo_cartao			= str_replace("'","",$tipo_cartao);
							$cpf_favorecido			= str_replace("'","",$cpf_favorecido);	
							$nome_favorecido		= str_replace("'","",$nome_favorecido);
							$condicao_pagamento		= str_replace("'","",$condicao_pagamento);	
							$codigo_banco			= str_replace("'","",$codigo_banco);
							$numero_agencia			= str_replace("'","",$numero_agencia);
							$numero_conta_corrente	= str_replace("'","",$numero_conta_corrente);		
							$sigla_erro				= str_replace("'","",$sigla_erro);																																																																																																																				
							$identificacao_parcela	= str_replace("'","",$identificacao_parcela);	
							$id_parcela_cliente		= str_replace("'","",$id_parcela_cliente);			
							$cnpj_portador_pj		= str_replace("'","",$cnpj_portador_pj);	
							$numero_ciot			= str_replace("'","",$numero_ciot);
							$protocolo_ciot			= str_replace("'","",$protocolo_ciot);	
							$cpf_cnpj_contratado	= str_replace("'","",$cpf_cnpj_contratado);																	
							$nome_contratado		= str_replace("'","",$nome_contratado);																	
							$num_autorizacao_2		= str_replace("'","",$num_autorizacao_2);							
							
							
							if (($tipo_registro_1 != 0) && ($data_transacao != 0))
							{
								$query = "insert into layout_arquivo (layout_cabecalho_id, tipo_registro_1, cnpj_contratante, 
								cnpj_ponto_emb, indicador_contrat, id_viagem, tipo_documento, numero_documento, numero_contrato, 
								numero_pamcard, data_cadastro_viagem, status_viagem, placa_veiculo, categoria_veiculo, pais_origem, 
								uf_cidade_origem, cidade_origem, pais_destino, uf_cidade_destino, cidade_destino, 
								data_embarque_viagem, tipo_transacao, status_transacao, valor_transacao, valor_taxa_transacao, 
								usuario, num_autorizacao, data_transacao, hora_transacao, banco_autorizador, 
								documento_extrato, transacao_sequencial, valor_pedagio_solicit, saldo_pedagio_cartao, tipo_cartao, 
								cpf_favorecido, nome_favorecido, condicao_pagamento, codigo_banco, numero_agencia, 
								numero_conta_corrente, sigla_erro, identificacao_parcela, id_parcela_cliente, cnpj_portador_pj, 
								numero_ciot, protocolo_ciot, cpf_cnpj_contratado, nome_contratado, num_autorizacao_2, status_id) 
								values 
								($layout_cabecalho_id, '$tipo_registro_1', '$cnpj_contratante', '$cnpj_ponto_emb', 
								'$indicador_contrat', '$id_viagem', '$tipo_documento', 
								'$numero_documento', '$numero_contrato', '$numero_pamcard', '$data_cadastro_viagem', 
								'$status_viagem', '$placa_veiculo', '$categoria_veiculo', '$pais_origem', '$uf_cidade_origem', 
								'$cidade_origem', '$pais_destino', '$uf_cidade_destino', '$cidade_destino', '$data_embarque_viagem', 
								'$tipo_transacao', '$status_transacao', 
								'$valor_transacao', '$valor_taxa_transacao', '$usuario', '$num_autorizacao', '$data_transacao', 
								'$hora_transacao', '$banco_autorizador', '$documento_extrato', '$transacao_sequencial', 
								'$valor_pedagio_solicit', '$saldo_pedagio_cartao', '$tipo_cartao', '$cpf_favorecido', 
								'$nome_favorecido', '$condicao_pagamento', '$codigo_banco', '$numero_agencia', 
								'$numero_conta_corrente', '$sigla_erro', '$identificacao_parcela', '$id_parcela_cliente', 
								'$cnpj_portador_pj', '$numero_ciot', '$protocolo_ciot', '$cpf_cnpj_contratado', '$nome_contratado', 
								'$num_autorizacao_2', 'p')";
								//print "<br>".$query;
								odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao inserir os dados na tabela<br>");
							
							}
															
	
						}
			
					$cont++;
				}
				
				fclose($ponteiro);	
			
				//salvando o nome do arquivo
				$query  = "insert into anexo_leitura (nome_arquivo, data_inclusao) 
							values 
						  ((rtrim(ltrim('$name'))),getdate())";
				//print $query;
				odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao salvar o nome do arquivo<br>");			
			
			
				print "
					<script language = 'JavaScript'>
					   alert('Arquivo incluido com sucesso!!!');
					</script>
				";
			
			}//if
			else
			{
				print "
					<script language = 'JavaScript'>
					   alert(unescape('Este arquivo j%E1 foi importado anteriormente. Por favor, selecione outro arquivo!'));
					</script>
				";
			}
		}
		else
			print "<script language = 'JavaScript'>alert('Favor selecionar o arquivo');</script>";		





	
}//FIM ANEXAR

//QUERY QUE VERIFICA DATA E HORA DO ULTIMO ARQUIVO LIDO E CONTA A QUANTIDADE DE ARQUIVOS DENTRO DO CABEÇALHO - EDUARDO
$query_ultimo_leitura = "SELECT
							(select top 1 convert(varchar(10), data_inclusao, 103)+ ' &agrave;s '+convert(varchar(5), data_inclusao, 108) from layout_cabecalho order by data_inclusao desc) Inclusao_ultimo_cabecalho,
							(select COUNT(*) from layout_arquivo where layout_cabecalho_id = (select top 1 layout_cabecalho_id from layout_cabecalho order by data_inclusao desc)) Total_arquivo_ultimo_cabecalho,
							(select top 1 nome_arquivo from anexo_leitura order by id desc) nome_ultimo_arquivo";

$result = odbc_exec($conSQL, $query_ultimo_leitura) or die("Erro ao pesquisar dados de leitura do ultimo cabecalho lido");
$data_ultima_leitura 	   = odbc_result($result,1);
$quantidade_ultimo_arquivo = odbc_result($result,2);
$ultimo_arquivo_importado  = odbc_result($result,3);


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
<legend>Upload - Arquivo Pamcary</legend> 

    <table width="519" border="0" align="center">
	<tr>
	  <td colspan="2">&nbsp;</td>
	</tr>
        <tr>
          <td width="18%" class="txt_home"><div align="right" class="txt_home">
            <p><strong>Arquivo:</strong>          </p>
          </div></td>
          <td width="82%" class="subtitulo"><input name="file" type="file" class="inp-text" id="file" size="40" /></td>
        </tr>
      <tr>
          <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
	      <td colspan="2"><div align="center"><strong><font color="#0000FF" size="-1">Arquivo <?php echo $ultimo_arquivo_importado; ?> importado em <?php echo $data_ultima_leitura; ?> hrs</strong></div></td>
      </tr>
      <tr>
	  <td colspan="2">&nbsp;</td>
	</tr>
     <tr>
	      <td colspan="2"><div align="center"><strong><font color="#0000FF" size="-1">Total de la&ccedil;amentos Pamcary importados:  <?php echo $quantidade_ultimo_arquivo; ?></strong></div></td>
      </tr>
      <tr>
	  <td colspan="2">&nbsp;</td>
	</tr>
  </table>


<table width="600" border="0" align="center">        
        <tr>
          <td class="txt_home"><div align="center">
            <input name="anexar" type="submit" class="botao_site" value=" Inserir " />            
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
