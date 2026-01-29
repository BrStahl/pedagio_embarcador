<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
include('InsereDadosPlanilha.php');


class UploadPlanilha extends ConexaoSCI{



	public function insereUpload($fornecedor_id, $tmpName, $name, $embarcador_id = null){

		$con = new ConexaoSCI;

		$insere_dados = new InsereDadosPlanilha;

		$logado = $_SESSION['usuario_logado'];

		$data = date("Y-m-d_H:i:s");
		$nome_arquivo = $name;
		$nome_fantasia = $fornecedor_id.'.csv';


		if (($nome_arquivo != '') && ($logado != ''))
		{

			$diretorio = "../upload";


			$workDir = $diretorio;

			move_uploaded_file($_FILES['file']['tmp_name'], $workDir."/".$tmpName) or die("Cannot move uploaded file to working directory");

			copy ($workDir."/".$tmpName, $workDir."/".$nome_fantasia);


			unlink($workDir."/".$tmpName) or die("Cannot delete uploaded file from working directory -- manual deletion recommended");

			fclose($ponteiro);

			$nome_arquivo = utf8_decode($nome_arquivo);

			$path = '../upload/'.$fornecedor_id.'.csv';

			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = base64_encode($data);

			$valida = strstr($nome_arquivo, '.csv');


			if ((strstr($nome_arquivo, '.csv') == '') && (strstr($nome_arquivo, '.CSV') == ''))
				return "-1";
			else
			{

				$inserir = $insere_dados->dadosConciliacao($fornecedor_id, $embarcador_id);

				$rel_inserir = $inserir->upload;


			}

			return $rel_inserir;

		}
		else
			if ($logado == '')
				return "-2";
			else
				if ($nome_arquivo == '')
					return "-3";

	}



}


?>