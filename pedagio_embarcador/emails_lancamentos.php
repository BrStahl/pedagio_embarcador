<?php
session_name("covre_ti");
session_start();

require("../SCA/includes/page_func.php");
include("../SCA/includes/conect_sqlserver.php");
require("../SCA/includes/phpmailer/class.phpmailer.php");

$localItem = "../conciliacao_pamcary/pesquisa_lancamentos.php";
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

	//$email_destino = 'waldineia.pereira@covre.com.br';
	// $email_destino = 'alerta.pamcary@covre.com.br';
	$email_destino = '3371.gustavo@bradesco.com.br;caio.carvalho@roadcard.com.br;operacoespamcard@roadcard.com.br';


	$query = "select distinct 
				LD.id,
				convert(varchar(10), FXCX.data, 103),
				SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7),
				nsu,
				ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO),
				observacao,
				ABS(LD.valor)
				from lancamento_divergente LD with (nolock)
				left join Corpore..FXCX with (nolock) on
					FXCX.NUMERODOCUMENTO = LD.nsu collate SQL_Latin1_General_CP1_CI_AI
					AND FXCX.COMPENSADO = 1

				LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
					FRECONCILRELAC.IDXCX = FXCX.IDXCX
				AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
				
				LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
					FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
				AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA					
					
				where LD.id is not null
				and data_conciliacao is null
				and cast(cast(year(ld.data_gravacao)as varchar)+'-'+cast(month(ld.data_gravacao)as varchar)+'-01' as datetime) 				< 
				cast(cast(year(GETDATE())as varchar)+'-'+cast(month(GETDATE()) as varchar)+'-01' as datetime)
				order by LD.id";
	//print $query;
	$result = odbc_exec($conSQL, $query);
	
	$corpo_email = "<table width='1100' height='37' border='1'> 
					<tr>
						  <td width='100' bgcolor='#CCCCCC'><font size='-1'>
							<div align='center'><strong>Data</strong></div></td>
						  <td width='100' bgcolor='#CCCCCC'><font size='-1'>
							<div align='center'><strong>N&ordm; Documento</strong></div></td>
						  <td width='170' bgcolor='#CCCCCC'><font size='-1'>
							<div align='center'><strong>NSU / Autorização</strong></div></td>
						  <td width='300' bgcolor='#CCCCCC'><font size='-1'>						  
							<div align='center'><strong>Descri&ccedil;&atilde;o</strong></div></td>
						  <td width='330' bgcolor='#CCCCCC'><font size='-1'>
							<div align='center'><strong>Observa&ccedil;&atilde;o</strong><strong></strong></div></td>
						  <td width='100' bgcolor='#CCCCCC'><font size='-1'>
							<div align='center'><strong>Valor</strong></div></td>
					</tr>";
	
	$total = 0;
				  
	 while(odbc_fetch_row($result))
	 {	
	 
		$lancamento_id	= odbc_result($result, 1);
		$data 			= odbc_result($result, 2);
		$documento	 	= odbc_result($result, 3);
		$nsu		 	= odbc_result($result, 4);
		$historico		= odbc_result($result, 5);
		$observacao		= odbc_result($result, 6);
		$valor			= odbc_result($result, 7);

		
		
		$corpo_email .= "	  
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$data</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$documento</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$nsu</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$historico</strong></font></div></td>
				  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>$observacao</strong></font></div></td>				  				  	  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>R$ 
					".number_format($valor, 2, ',', '.')."</strong></font></div></td>";
				  
					  						
		$total = $total + $valor;
				  
			  $corpo_email .= "</tr>";
	 }//fim while
	
	 $corpo_email .= "</table>
	 <table width='1090' height='37' border='0'> 
		<tr>
			<td align='right'><font size='-1'><b>Total:&nbsp;&nbsp;&nbsp;R$ ".number_format($total, 2, ',', '.')."</b></td>
		</tr>
	 </table>";				

	if ($total != '')
	{
		$conteudo_email = "<font color='#0000FF' size='+1'><b>Relação de lançamentos pendentes:</b></font>
							<br><br><br>
							$corpo_email
							<br><br><b>SISTEMA SCA - Conciliação Pamcary</b>";	

		$enviou = enviar_email("helpdesk@covre.com.br", "SCA - Conciliação Pamcary", "$email_destino", "Relação de Débitos pendentes Pamcary", "$conteudo_email");

	}






}//else
?>
