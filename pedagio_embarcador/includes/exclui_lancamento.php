<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");


$lancamento_id = $_POST["lancamento_id"];


$logado    = $_SESSION["usuario_logado"];

if ($logado != '')
{
	$query = "delete
			  from lancamento_divergente
			  Where id = $lancamento_id";
	//print $query;
	$result = odbc_exec($conSQL, $query) or die ('erro ao deletar') ;

    print "Lançamento excluído com sucesso";
}
else
	print "Sessao Explirada, Favor logar novamente";





?>

