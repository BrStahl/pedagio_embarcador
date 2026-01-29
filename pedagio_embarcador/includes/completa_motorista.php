<?php
include("../../SCA/includes/conect_sqlserver.php");

$q=strtolower ($_GET["q"]);

$query = "SELECT
			 P.NOME_FANTASIA
			
		 FROM
			  CARGOSOL..COLABORADOR as C (nolock)
		 join CARGOSOL..PESSOA as P (nolock)
				 on C.PESSOA_ID = P.PESSOA_ID
		
		 WHERE
		 	 P.NOME_FANTASIA like '%$q%'
			 AND C.TAB_STATUS_ID = 1
			 AND P.TAB_STATUS_ID = 1
			
		 ORDER BY
			 P.NOME_FANTASIA";

//print $query;
$result = odbc_exec($conSQL, $query) or die("erro");

while(odbc_fetch_row($result))
{
    //if (srtpos(strtolower($reg['nom_lista']),$q !== false){
	// echo $result["nome"]."|".$reg["nome"]."\n";
	print odbc_result($result,1)."|".odbc_result($result,1)."\n";
}
?>
