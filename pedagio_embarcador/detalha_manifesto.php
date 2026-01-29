<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");

$localItem = "../conciliacao_pamcary/detalha_manifesto.php";
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

	$manifesto_id = $_GET["manifesto_id"];
    
    $query = "SELECT DISTINCT SEC.Solicit_Embarque_Id,
                    SEC.Solicit_Embarque_Container_Id,
                    C.Num_Container
                FROM CARGOSOL..MANIFESTO_ROD MR WITH(NOLOCK)
                JOIN CARGOSOL..Docto_Transp_Manifesto_Rod DTMR WITH(NOLOCK)
                    ON DTMR.Manifesto_Rod_Id = MR.Manifesto_Rod_Id
                    AND DTMR.Tab_Status_Id = 1
                JOIN CARGOSOL..Docto_Transporte DT WITH(NOLOCK)
                    ON DT.Docto_Transporte_Id = DTMR.Docto_Transporte_Id
                    AND DT.Tab_Status_Id = 1
                JOIN CARGOSOL..DOCTO_TRANSP_SOLICIT_CONTAINER DTSC WITH(NOLOCK)
                    ON DTSC.Docto_Transporte_Id = DT.Docto_Transporte_Id
                    AND DTSC.Tab_Status_Id = 1
                JOIN CARGOSOL..Solicit_Embarque_Container SEC WITH(NOLOCK)
                    ON SEC.SOLICIT_EMBARQUE_CONTAINER_ID = DTSC.Solicit_Embarque_Container_Id
                    AND SEC.Tab_Status_Id = 1
                JOIN CARGOSOL..CONTAINER C WITH(NOLOCK)
					ON C.Container_Id = SEC.Container_Id
					AND C.Tab_Status_Id = 1
                WHERE MR.Tab_Status_Id = 1
                    AND MR.Manifesto_Rod_Id = $manifesto_id";
    $result = odbc_exec($conSQL,$query);


	if($fechar != "")
	{
		print"
			<script language='javascript'>
				open(location, '_self').close();
			</script>
		";
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
fieldset {padding: 22px 17px 12px 17px; position: relative; margin: 12px 0 34px 0;}
</style>


</head>
<div id="fundo" style="display:none">&nbsp;</div>
<body>
  <form action="" name="form1" method="post" enctype="multipart/form-data" style="width:500px" display >
  <fieldset> 
    <legend>ST/Pedido</legend>
            
    <p>
      <?php        

		print"<table width='450' border='1'>
			   <tr>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>ST</center></b></td>
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>PEDIDO</center></b></td>				 
				 <td bgcolor='#CCCCCC'><b><center><font size='-1'>CONTAINER</center></b></td>				 
			   </tr>";

			 while(odbc_fetch_row($result))
			 {
				   $st		= odbc_result($result,1);
				   $pedido  = odbc_result($result,2);
				   $cntr    = odbc_result($result,3);
				   
				   print"
				   <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				   		<td bgcolor='#FFFFFF'><center><font size='-1'>".$st."</center></b></td>
				   		<td bgcolor='#FFFFFF'><center><font size='-1'>".$pedido."</center></b></td>			
				   		<td bgcolor='#FFFFFF'><center><font size='-1'>".$cntr."</center></b></td>			
				   </tr>";            
			 } 
		print"</table>";	

	?>
    </p>
    <p>&nbsp;</p>
  </fieldset>
  </form>

</body>
</html>  
<?php

	

}
	
?>