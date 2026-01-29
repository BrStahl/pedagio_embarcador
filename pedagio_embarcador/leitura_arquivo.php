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
           alert('Acesso negado para esta p&aacute;gina');
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
		echo "Diret&oacute;rio criado com sucesso.";
	    else
		echo "N&atilde;o foi poss&iacute;vel criar o diret&oacute;rio.";
    */
		$dir = "arquivos";

		if (is_dir($dir)) {
		   if ($dh = opendir($dir)) {
			   while (($file = readdir($dh)) !== false) {
				   //unlink($dir."/".$file) ;
			   }//while
		   }//if
		}//if

		$embarcador_selecionado = $_POST['embarcador'];

		if($embarcador_selecionado == "") {
			echo "<script>alert('Por favor, selecione um Embarcador antes de prosseguir.'); history.back();</script>";
			exit;
		}

		$workDir = "arquivos"; // define this as per local system

		// get temporary file name for the uploaded file

		$tmpName = basename($_FILES['file']['tmp_name']);
		$name 	 = basename($_FILES['file']['name']);
		$nome_arquivo = $name;


		if ($nome_arquivo != '')
		{
            $nome_arquivo_esc = str_replace("'", "''", $nome_arquivo);
			$query  = "select nome_arquivo from anexo_leitura where rtrim(ltrim(nome_arquivo)) = rtrim(ltrim('$nome_arquivo_esc'))";
			//print $query;
			$result = odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL)."<br>Erro ao consultar se o arquivo j&aacute; foi importado<br>");


			if(odbc_result($result, 1) == "")
			{

			// copy uploaded file into current directory

				move_uploaded_file($_FILES['file']['tmp_name'], $workDir."/".$tmpName)
				or die("Cannot move uploaded file to working directory");

				copy ($workDir."/".$tmpName, $workDir."/".$nome_arquivo);


				unlink($workDir."/".$tmpName) or
				die("Cannot delete uploaded file from working directory -- manual deletion recommended");

                // Criar registro no cabeçalho
                $query_cab = "INSERT INTO layout_cabecalho (data_inclusao, nome_arquivo, usuario_id)
                              VALUES (GETDATE(), '$nome_arquivo_esc', (select top 1 id from usuario where usuario = '$logado' and status = 'a'))";
                odbc_exec($conSQL, $query_cab) or die(odbc_errormsg($conSQL)."<br>Erro ao criar cabe&ccedil;alho<br>");

                $res_id = odbc_exec($conSQL, "SELECT @@IDENTITY AS id");
                $layout_cabecalho_id = odbc_result($res_id, 'id');

				//ABRE O ARQUIVO
				$ponteiro = fopen("arquivos/$nome_arquivo", "r");
				$cont = 1;

                if ($embarcador_selecionado == 'Bravo') {
                    // Lógica BRAVO (CSV)
					$mapa_layout = [
						'cnpj_contratante'  => 0, // Ajuste para 0 se for a 1ª coluna
						'placa_veiculo'     => 8,
						'categoria_veiculo' => 9,
						'origem'            => 6,
						'destino'           => 7,
						'id_viagem'         => 5,
						'data_embarque'     => 10,
						'valor_transacao'   => 14,
						'valor_pedagio'     => 16,
						'favorecido'        => 19,
						'documento_fav'     => 21,
						'IDVPO'             => 24,
						'numero_documento'  => 4,
						'pagamento'         => 11
					];

					while (($dados = fgetcsv($ponteiro, 4096, ",")) !== FALSE) {
						if ($cont == 1) {
							$cont++;
							continue;
						}

						// Função rápida para evitar erro de índice inexistente e aspas simples
						$get = function($idx) use ($dados) {
							$val = isset($dados[$idx]) ? trim($dados[$idx]) : '';
							return str_replace("'", "''", $val); // Escapa aspas para SQL Server/ODBC
						};

						// Tratamento de Origem e Destino com segurança
						$origem_raw    = explode('/', $get($mapa_layout['origem']));
						$uf_origem     = trim($origem_raw[0] ?? '');
						$cidade_origem = trim($origem_raw[1] ?? '');

						$destino_raw    = explode('/', $get($mapa_layout['destino']));
						$uf_destino     = trim($destino_raw[0] ?? '');
						$cidade_destino = trim($destino_raw[1] ?? '');

						// Captura de dados usando a função segura
						$cnpj            = $get($mapa_layout['cnpj_contratante']);
						$placa_vei       = $get($mapa_layout['placa_veiculo']);
						$categoria_vei   = $get($mapa_layout['categoria_veiculo']);
						$embarque        = $get($mapa_layout['data_embarque']);
						$valor_carregado = $get($mapa_layout['valor_transacao']);
						$id_viagem       = $get($mapa_layout['id_viagem']);
						$favorecido      = $get($mapa_layout['favorecido']);
						$doc_favorecido  = $get($mapa_layout['documento_fav']);
						$idvpo           = $get($mapa_layout['IDVPO']);
						$data_pag        = $get($mapa_layout['pagamento']);

						// Limpeza do Valor
						$valor_raw = $get($mapa_layout['valor_pedagio']);
						$valor = str_replace(['.', ','], ['', '.'], $valor_raw);
						if(!is_numeric($valor)) $valor = 0;

						// Limpeza do Número do Documento
						$num_doc = trim(str_replace(['ATCR', 'CON', ':', ' '], '', $get($mapa_layout['numero_documento'])));

						$query = "INSERT INTO layout_arquivo (
									layout_cabecalho_id, cnpj_contratante, id_viagem, placa_veiculo,
									categoria_veiculo, pais_origem, uf_cidade_origem, cidade_origem,
									pais_destino, uf_cidade_destino, cidade_destino, data_embarque_viagem,
									valor_transacao, numero_documento, nome_favorecido, cpf_favorecido,
									data_transacao, numero_pamcard, valor_pedagio_solicit, embarcador, status_id
								) VALUES (
									$layout_cabecalho_id, '$cnpj', '$id_viagem', '$placa_vei',
									'$categoria_vei', 'BRASIL', '$uf_origem', '$cidade_origem',
									'BRASIL', '$uf_destino', '$cidade_destino', '$embarque',
									'$valor_carregado', '$num_doc', '$favorecido', '$doc_favorecido',
									'$data_pag', '$idvpo', '$valor', '$embarcador_selecionado', 'p'
								)";

						odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL) . "<br>Erro na linha $cont");

						$cont++;
					}
                } elseif ($embarcador_selecionado == 'pamcary') {
                    // Lógica PAMCARY (Posicional)
                    while (($linha = fgets($ponteiro)) !== FALSE) {
                        $tipo_registro = substr($linha, 0, 1);

                        // Layout baseado na lógica anterior (Header vs Detalhe)
                        // Assumindo que tipo 1 é detalhe.
                        if ($tipo_registro != '1') {
                            continue;
                        }

                        // Função para escapar valores
                        $esc = function($val) {
                            return str_replace("'", "''", trim($val));
                        };

                        $cnpj_contratante = $esc(substr($linha, 1, 14));
                        $cnpj_ponto_emb   = $esc(substr($linha, 15, 14));
                        $indicador_contrat= $esc(substr($linha, 29, 1));
                        $id_viagem        = $esc(substr($linha, 30, 10));
                        $tipo_documento   = $esc(substr($linha, 40, 2));
                        $numero_documento = $esc(substr($linha, 42, 30));
                        $numero_contrato  = $esc(substr($linha, 72, 30));
                        $numero_pamcard   = $esc(substr($linha, 102, 10));

                        $query = "INSERT INTO layout_arquivo (
                            layout_cabecalho_id, embarcador, status_id,
                            tipo_registro_1, cnpj_contratante, cnpj_ponto_emb, indicador_contrat,
                            id_viagem, tipo_documento, numero_documento, numero_contrato, numero_pamcard
                        ) VALUES (
                            $layout_cabecalho_id, '$embarcador_selecionado', 'p',
                            '$tipo_registro', '$cnpj_contratante', '$cnpj_ponto_emb', '$indicador_contrat',
                            '$id_viagem', '$tipo_documento', '$numero_documento', '$numero_contrato', '$numero_pamcard'
                        )";

                         odbc_exec($conSQL, $query) or die(odbc_errormsg($conSQL) . "<br>Erro na linha $cont");
                         $cont++;
                    }

                } elseif ($embarcador_selecionado == 'sem_parar') {
                    // Lógica SEM PARAR (CSV - Placeholder)
                    print "<script>alert('Funcionalidade n&atilde;o implementada para Sem Parar.');</script>";
                    // Não faz nada, sai do loop ou deixa finalizar
                }

				fclose($ponteiro);

				//salvando o nome do arquivo
				$query  = "insert into anexo_leitura (nome_arquivo, data_inclusao)
							values
						  ((rtrim(ltrim('$nome_arquivo_esc'))),getdate())";
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

//QUERY QUE VERIFICA DATA E HORA DO ULTIMO ARQUIVO LIDO E CONTA A QUANTIDADE DE ARQUIVOS DENTRO DO CABEALHO - EDUARDO
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
			<td width ="12%" class="txt_home">
				<div align="right" class="txt_home">
					<p><strong>Embarcador:</strong></p>
				</div>
			</td>
		<td width="82%" class="subtitulo">
		<select name="embarcador" id="embarcador" class="inp-text" style="width:250px">
            <option value="">Selecione o Embarcador...</option>
            <option value="pamcary">Pamcary</option>
            <option value="Bravo">Bravo</option>
            <option value="sem_parar">Sem Parar</option>
        </select>
      </td>
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
