<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";
require("../../SCA/includes/phpmailer/class.phpmailer.php");
require_once "../../SCI/includes/page_func_sci.php";
require_once "../../SCI/class/GravaLog.php";

class GravaDados extends ConexaoSCI{



	public function gravaConferenciaSaldoPedagio($fornecedor_id, $data, $valor, $tarifa, $id_item, $id_sistema){
		
		$con = new ConexaoSCI;
		
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		
		if ($logado != '')
		{
			//verifica se o saldo já foi gravado
			$query = "select id
						from saldo_pedagio with (nolock)
						where status_id = 'a'
						and data_conciliacao = '$data'
						and fornecedor_id = $fornecedor_id";
			//print $query;
			$result = $con->executar($query);
			$id_encontrado = odbc_result($result, 1);
			
			if ($id_encontrado == '')
			{
				$query = "insert into saldo_pedagio (fornecedor_id, valor, tarifa, data_conciliacao, data_hora_gravacao, user_gravacao, 
						  status_id) values ($fornecedor_id, '$valor', 
						  case when '$tarifa' = '' then null else replace('$tarifa',',','.') end, '$data', getdate(), 
						  (select id from usuario where usuario = '$logado' and status = 'a'), 'a')";
				//print $query;
				$con->executar($query);
				
				$query = "SELECT top 1 id
							FROM saldo_pedagio WITH (NOLOCK)
							where user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a')
							and fornecedor_id = $fornecedor_id
							and datediff(mi,data_hora_gravacao, getdate()) < 1
							order by data_hora_gravacao desc";
				//print $query;
				$result = $con->executar($query);
				$saldo_id = odbc_result($result, 1);
				
				$acao_id = 1;
			}
			else
			{
				$saldo_id = $id_encontrado;
				
				$query = "update saldo_pedagio 
						  set valor = '$valor',
                                                  data_hora_gravacao = getdate(),
                                                  user_gravacao = (select id from usuario where usuario = '$logado' and status = 'a'),
						  tarifa = case when '$tarifa' = '' then null else replace('$tarifa',',','.') end
						  where status_id = 'a'
						  and id = $saldo_id";
				//print $query;
				$con->executar($query);	
				
				$acao_id = 2;			
			}
		
			$ret_gravacao = "1|$saldo_id|ok|";
			
			$id = $saldo_id;

			
			//BUSCA O USUARIO_ID
			$query = "select id from usuario where usuario = '$logado' and status = 'a'";
			//print $query;
			$result = $con->executar($query);	
			$usuario_id	= odbc_result($result,1);				
			
			//BUSCA O TABELA_SISTEMA_ID
			$query = "select tabela_sistema_id from tabela_sistema where nome_tabela = 'saldo_pedagio'";
			//print $query;
			$result = $con->executar($query);	
			$id_tabela	= odbc_result($result,1);				
			
			//INSERE O LOG
			$grava_log->gravaLogSci($id_tabela, $id_sistema, $id_item, $acao_id, $usuario_id, $id);			
			
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;
		
		print $ret_gravacao;

		return $retorno;
	}	
	
	
	public function gravaObservacaoDivergente($fornecedor_id, $id, $nsu, $valor, $observacao, $id_item, $id_sistema){
		
		$con = new ConexaoSCI;
		
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		$observacao = utf8_decode($observacao);
		
		if ($logado != '')
		{

			$query = "insert into lancamento_divergente (fornecedor_id, nsu, observacao, valor, usuario_id, data_gravacao, status_id) 
					  values ($fornecedor_id, '$nsu', '$observacao', '$valor', 
					  (select id from usuario where usuario = '$logado' and status = 'a'), getdate(), 'p')";	
			//print $query;
			$con->executar($query);
			
			$query = "SELECT top 1 id
						FROM lancamento_divergente WITH (NOLOCK)
						where usuario_id = (select id from usuario where usuario = '$logado' and status = 'a')
						and datediff(mi,data_gravacao, getdate()) < 1
						order by data_gravacao desc";
			//print $query;
			$result = $con->executar($query);
			$lancamento_id = odbc_result($result, 1);

			if ($fornecedor_id == 2)
			{
				$query = "UPDATE DADOS_CONCILIACAO_AILOG
							SET TAG = '$nsu' 
							WHERE ID = $id
							AND STATUS_ID = 'a'";
				//print $query;
				$result = $con->executar($query);
			}
			else
				if ($fornecedor_id == 3)
				{
					$query = "UPDATE DADOS_CONCILIACAO_BPP
								SET assunto = '$nsu' 
								WHERE ID = $id
								AND STATUS_ID = 'a'";
					//print $query;
					$result = $con->executar($query);
				}
		
			$acao_id = 1;
		
			$ret_gravacao = "1|$saldo_id|ok|";
			
			$id = $lancamento_id;

			
			//BUSCA O USUARIO_ID
			$query = "select id from usuario where usuario = '$logado' and status = 'a'";
			//print $query;
			$result = $con->executar($query);	
			$usuario_id	= odbc_result($result,1);				
			
			//BUSCA O TABELA_SISTEMA_ID
			$query = "select tabela_sistema_id from tabela_sistema where nome_tabela = 'lancamento_divergente'";
			//print $query;
			$result = $con->executar($query);	
			$id_tabela	= odbc_result($result,1);				
			
			//INSERE O LOG
			$grava_log->gravaLogSci($id_tabela, $id_sistema, $id_item, $acao_id, $usuario_id, $id);			
			
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;
		
		print $ret_gravacao;

		return $retorno;
	}	
	
	public function cancelaFinalizaDivergente($acao,$lancamento_id, $id_item, $id_sistema){
		
		$con = new ConexaoSCI;
		
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		

		
		if ($logado != '')
		{

			if ($acao == 2)
			{
				$query = "UPDATE lancamento_divergente
							SET status_id = 'f',
							data_conciliacao = getdate(),
							usuario_id_concil = (select id from usuario where usuario = '$logado' and status = 'a')
							WHERE ID = $lancamento_id
							AND STATUS_ID = 'p'";
				//print $query;
				$result = $con->executar($query);
			}
			else
			{
				$query = "UPDATE lancamento_divergente
							SET status_id = 'i',
							data_inativacao = getdate(),
							user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
							WHERE ID = $lancamento_id
							AND STATUS_ID = 'p'";
				//print $query;
				$result = $con->executar($query);							
			}
		
			$acao_id = $acao;
		
			$ret_gravacao = "1|$lancamento_id|ok|";
			
			$id = $lancamento_id;

			
			//BUSCA O USUARIO_ID
			$query = "select id from usuario where usuario = '$logado' and status = 'a'";
			//print $query;
			$result = $con->executar($query);	
			$usuario_id	= odbc_result($result,1);				
			
			//BUSCA O TABELA_SISTEMA_ID
			$query = "select tabela_sistema_id from tabela_sistema where nome_tabela = 'lancamento_divergente'";
			//print $query;
			$result = $con->executar($query);	
			$id_tabela	= odbc_result($result,1);				
			
			//INSERE O LOG
			$grava_log->gravaLogSci($id_tabela, $id_sistema, $id_item, $acao_id, $usuario_id, $id);			
			
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;
		
		print $ret_gravacao;

		return $retorno;
	}		


	public function gravaTarifa($fornecedor_id, $data, $valor, $id_item, $id_sistema){
		
		$con = new ConexaoSCI;
		
		$grava_log = new GravaLog;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		
		if ($logado != '')
		{
				
			$query = "update saldo_pedagio 
					  set tarifa = replace('$valor',',','.')
					  where status_id = 'a'
					  and fornecedor_id = $fornecedor_id
					  and data_conciliacao = DATEADD(DD,-1,'$data')";
			//print $query;
			$con->executar($query);	
			
			$acao_id = 2;			
			
		
			$ret_gravacao = "1|$saldo_id|ok|";
			
			$id = $saldo_id;

			
			//BUSCA O USUARIO_ID
			$query = "select id from usuario where usuario = '$logado' and status = 'a'";
			//print $query;
			$result = $con->executar($query);	
			$usuario_id	= odbc_result($result,1);				
			
			//BUSCA O TABELA_SISTEMA_ID
			$query = "select tabela_sistema_id from tabela_sistema where nome_tabela = 'saldo_pedagio'";
			//print $query;
			$result = $con->executar($query);	
			$id_tabela	= odbc_result($result,1);				
			
			//INSERE O LOG
			$grava_log->gravaLogSci($id_tabela, $id_sistema, $id_item, $acao_id, $usuario_id, $id);			
			
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		//$retorno->ret_gravacao = $ret_gravacao;
		
		print $ret_gravacao;

		return $retorno;
	}
	
}
	


?>