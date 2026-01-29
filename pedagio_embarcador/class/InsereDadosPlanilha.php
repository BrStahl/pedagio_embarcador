<?php
session_name("covre_ti");
session_start();

require_once $_SERVER["DOCUMENT_ROOT"]."/SCI/controller/ConexaoSCI.php";


class InsereDadosPlanilha extends ConexaoSCI{



	public function dadosConciliacao($fornecedor_id, $embarcador_id = null){
		$con = new ConexaoSCI;

			$logado = $_SESSION["usuario_logado"];

            $layout = null;
            if ($fornecedor_id == 2 && $embarcador_id) {
                $embarcador_id = (int) $embarcador_id; // Sanitize input
                $query_layout = "SELECT layout FROM embarcador WHERE id = $embarcador_id";
                $result_layout = $con->executar($query_layout);
                if ($result_layout) {
                     $layout_json = odbc_result($result_layout, 1);
                     if ($layout_json) {
                         $layout = json_decode($layout_json, true);
                     }
                }
            }


			//ABRE O ARQUIVO XLS
			$arquivo = fopen("../upload/".$fornecedor_id.".csv", "r");

			$cont = 1;
			$contador = 0;
			$retorno = "";
			$lote_id = "";

			$data_transacao_anterior = "";

			while (!feof ($arquivo))
			{

				// Pega os dados da linha
				$linha = fgets($arquivo, 1024);

				// Divide as Informações das celular para poder salvar
				$dados = explode(";", $linha);


				if ($fornecedor_id == 2) //AILOG
				{
                    if ($layout && $cont > 1) { // Assuming header row exists
                        $data = isset($dados[$layout["data"]]) ? $dados[$layout["data"]] : null;
                        $descricao = isset($dados[$layout["descricao"]]) ? $dados[$layout["descricao"]] : null;
                        $tag = isset($dados[$layout["tag"]]) ? $dados[$layout["tag"]] : null;
                        $valor = isset($dados[$layout["valor"]]) ? $dados[$layout["valor"]] : null;
                    } else {
                        // Fallback or skip if layout not found
                        $data = isset($dados[0]) ? $dados[0] : null;
                        $descricao = isset($dados[1]) ? $dados[1] : null;
                        $tag = isset($dados[2]) ? $dados[2] : null;
                        $valor = isset($dados[3]) ? $dados[3] : null;
                    }

					if ($cont > 1)
					{

						$data =  substr($data, 0, 10);

						$data_transacao =
						implode(preg_match("~\/~", $data) == 0 ? "/" : "-",
						array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));


						if (($cont == 2) || ($data_transacao_anterior != $data_transacao))
						{

							//VERIFICA SE JA TEM REGISTRO NESTA DATA
							$query1 = "select top 1 id
										from dados_conciliacao_ailog WITH (NOLOCK)
										where data_transacao = '$data_transacao'
										and status_id = 'a'";
							//print "<pre>$query1</pre>";
							$result1 = $con->executar($query1);
							$id_encontrado = odbc_result($result1, 1);

							if ($id_encontrado != '')
							{
								//INATIVA OS REGISTROS ANTIGOS
								$query1 = "UPDATE dados_conciliacao_ailog
											SET status_id = 'i',
											data_inativacao = getdate(),
											user_inativacao = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
											where data_transacao = '$data_transacao'
											and status_id = 'a'";
								//print "<pre>$query1</pre>";
								$result1 = $con->executar($query1);
							}

						}

						$descricao = utf8_decode($descricao);

						$valor = preg_replace( "/\r|\n/", "", $valor);

						if ($descricao != '')
						{

							$tag_r = '';
                                                    preg_match_all('!\d+!', $tag, $matches);

                                                    /*if(strpos($tag, "Adiantamento Viagem") !== false){

                                                        $tag_r = preg_replace('/[^0-9]/', '', $tag);

                                                    }*/

													$tag_r = $matches[0][0];


							$query1 = "INSERT INTO dados_conciliacao_ailog (data_transacao, descricao, tag, valor, data_hora_gravacao,
										user_gravacao, status_id) VALUES ('$data_transacao', '$descricao',
										'$tag_r',
										REPLACE('$valor',',','.'), getdate(),
									   (select top 1 id from usuario where usuario = '$logado' and status = 'a'), 'a')";
							//print "<pre>$query1</pre>";
							$con->executar($query1);

							$query1 = "SELECT @@IDENTITY AS Ident";
							$result1 = $con->executar($query1);
							$insert = odbc_result($result1, 1);

							if ($insert > 0)
								$contador++;

						}

						$data_transacao_anterior = $data_transacao;

					}

				}
				else
				if ($fornecedor_id == 3) //BRASIL PRE-PAGO
				{
					if ($cont > 3)
					{
						$data		= $dados[0];
						$acao		= $dados[1];
						$assunto	= $dados[2];
						$motorista	= $dados[6];
						$valor		= $dados[7];


						$data_transacao =
						implode(preg_match("~\/~", $data) == 0 ? "/" : "-",
						array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));


						if ($cont == 4)
						{
							//VERIFICA SE JA TEM REGISTRO NESTA DATA
							$query1 = "select top 1 id
										from dados_conciliacao_bpp WITH (NOLOCK)
										where data_transacao = '$data_transacao'
										and status_id = 'a'";
							//print "<pre>$query1</pre>";
							$result1 = $con->executar($query1);
							$id_encontrado = odbc_result($result1, 1);

							if ($id_encontrado != '')
							{
								//INATIVA OS REGISTROS ANTIGOS
								$query1 = "UPDATE dados_conciliacao_bpp
											SET status_id = 'i',
											data_inativacao = getdate(),
											user_inativacao = (select top 1 id from usuario where usuario = '$logado' and status = 'a')
											where data_transacao = '$data_transacao'
											and status_id = 'a'";
								//print "<pre>$query1</pre>";
								$result1 = $con->executar($query1);
							}

						}


						$acao = utf8_decode($acao);

						if ($acao != '')
						{

							$query1 = "INSERT INTO dados_conciliacao_bpp (data_transacao, acao, assunto, motorista, valor, data_hora_gravacao,
										user_gravacao, status_id) VALUES ('$data_transacao', '$acao',
										CASE WHEN '$acao' LIKE '%CARGA%'
												THEN LTRIM(REPLACE(REPLACE(SUBSTRING('$assunto',PATINDEX('%-%','$assunto')+1,30),'.',''),' ',''))
												ELSE '$assunto'
										END,
										'$motorista',
										REPLACE(REPLACE(REPLACE('$valor','BRL',''),'-',''),',','.'), getdate(),
									   (select top 1 id from usuario where usuario = '$logado' and status = 'a'), 'a')";
							//print "<pre>$query1</pre>";
							$con->executar($query1);

							$query1 = "SELECT @@IDENTITY AS Ident";
							$result1 = $con->executar($query1);
							$insert = odbc_result($result1, 1);

							if ($insert > 0)
								$contador++;

						}

					}

				}

				$cont++;

			}//fim while



			fclose($ponteiro);

			$retorno->upload = $contador;

			return $retorno;

	}


}


?>