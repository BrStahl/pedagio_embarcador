<?php
	
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/UploadPlanilha.php";
require_once "class/Dados.php";
//require_once "class/GravaLog.php";

$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$upload = new UploadPlanilha;	
$dados = new Dados;	
//$logs = new GravaLog;	

$localItem = "../conciliacao_pamcary/importar_dados_conciliacao.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$id_sistema  = $dados_autenticacao->id_sistema;
$usuario_id  = $dados_autenticacao->usuario_id;
$nome_usuario  = $dados_autenticacao->nome_usuario;


//$acesso = "permitido";


if($acesso <> "permitido")
{
   print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";
   
   $dados_gravacao = $autenticacao_sci->redirecionaLogin();
   $local  = $dados_gravacao->local;
   
   print "<script language = 'JavaScript'>window.location='$local';</script/>";
}
else
{
	 $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item,$usuario_id);

    /** Buttons */
    if($fechar_p != ""){ print"<script language='javascript'>open(location, '_self').close();</script>";}
    if($buscar){}
	

	$id			 	 	= $_POST['id'];


	
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Importar Dados</title>
    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
    <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link rel="stylesheet" href="css/relsm.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/buttons.dataTables.min_novo.css"/> 
    <script src="../SCI/js/jquery.js"></script>
    <script src="../SCI/js/bootstrap.js"></script>
    <script src="../SCI/js/jquery.ajaxQueue.js"></script>
    <script src="../SCI/js/jquery.mask.js"></script>
    <script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

    <script type="text/javascript" language="javascript" src="../SCI/js/datatables/dataTables.buttons.min_novo.js"></script>
    <!--<script type="text/javascript" language="javascript" src="../SCI/js/datatables/buttons.dataTables.min_novo.css"></script>  -->
    
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="../SCI/js/datatables/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript" src="../SCI/js/datatables/buttons.print.min.js"></script>  
    
 	<script src="../SCI/js/table_to_excel.js"></script>
    <script src="../SCI/js/export_to_xls.js"></script>   
</script>


<script>    

function order() {

    $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss');    //Formatação com Hora
    $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
	
	
	 $('#myTable3').DataTable({
	     //scrollY: 300,
	     autoWidth : true,
	     dom       : 'Blfrtip',
	     info      : true,
	     retrieve: true,
		 paging    : true,
		 responsive : true,
	     buttons: [
	         {
	             extend: 'excel',
	             text: '<i class="fa fa-files-o">Excel</i>',
	             title:  function () {
	                 return document.getElementsByTagName("title")[0].innerHTML;
	             }, customize: function (xlsx) {
	                 var sheet = xlsx.xl.worksheets['sheet1.xml'];
	                 $('row:first c', sheet).attr('s', '2');
	             }
	         }           
	     ],
	     "order": [
	         [0, 'asc']
	     ],
	     columnDefs: [{
	         type  : 'date-br'
	     }],
	 });

}

</script> 




<script language="javascript">
/*
function insere_arquivo(){
	//alert();
		
		var item_id		 	 	= document.getElementById('item_id').value;


		if (item_id == '')
		{
			$('#msg').html('Favor selecionar o Item');
			$('#myModal_alert').modal('show');
		}	
		else	
		{

			let dados = $("form").serialize();

			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/anexa_planilha.php", //chama uma pagina
				data: dados, //passa os parametros, se necessбrio
				success: function (msg) {
					//alert(msg);
												
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						
						$('#msg_success').html('Gravação efetuada com sucesso');
						$('#myModal_alert_sucess').modal('show');						
						
						var dados = msg.split("|");
						//document.getElementById('codigo').value = dados[2];
						
						//exibe_pergunta();
						//novo_registro();
					}						
					
					
				}
			});
			
		}
	
}
*/
</script>


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Importar Dados - Conciliação</b></div>
    </div>
    <br>
<div class="col-xs-8">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-8 panel-padrao">
          <div class="panel-body col-xs-10">
                <div class="table-responsive covre">
                
                    <table width="83%" class="table table-bordered tb-padrao">
                        <tbody>
                           	<tr>
	                            <td width="1%" class="no-border left altura">&nbsp;</td>
	                            <td  class="no-border left altura" ><font size="-1"><b>Fornecedor:</b></font></td>                   
								<td width="41%"  class="no-border left altura" ><font size="-1"><b>Selecione a planilha (<font color="#0000FF">arquivo .csv</font>):</b></font></td>
                            </tr>
                           	<tr>
	                            <td class="no-border left altura">&nbsp;</td>
	                            <td  class="no-border left altura" ><div class="col-xs-8 left-inline">
	                              <?php
                                            $query = "SELECT id, descricao
														from fornecedor_pedagio with (nolock)
														where status_id in ('a')
														and id in (2)
														order by id";
                                            //print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='fornecedor_id' id='fornecedor_id' value=''><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                        ?>
                                </div></td>                   
								<td  class="no-border left altura" >
                                 <div class="col-xs-12 left-inline">
                                   <input type='file' class='form-control-file altura-campo' aria-describedby='basic-addon1' 
                                   name='file' id='file' size='50' maxlength='100' >
                                 </div>
                                </td>
                            </tr>    
                           	<tr>
	                            <td class="no-border left altura">&nbsp;</td>
	                            <td class="no-border left altura">&nbsp;</td>                   
                                <td class="no-border left altura">&nbsp;</td>   
                            </tr>   
                           	<tr>
	                            <td class="no-border left altura">&nbsp;</td>                   
                                <td colspan="2" class="no-border left altura"><div align="center">
                                  <input type='button' class='btn-sm btn-primary' value=' Inserir ' id='inserir' name='inserir' onClick='javascript:insere_arquivo()'>
                                  <button type="button" class="btn-sm btn-danger" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button> 
                                </div></td>        
                                </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="2" class="no-border left altura">&nbsp;</td>
                       	  </tr>
                           	<tr>
                           	  <td class="no-border left altura">&nbsp;</td>
                           	  <td colspan="2" class="no-border left altura">
                              	<div id="ultimos">
                              	<?php
								
									$ultima_informacao = $dados->ultimaInformacao();
									$informacao  = $ultima_informacao->informacao;
									
									print $informacao;
									
                              	?>
                                </div>
                              </td>
                       	  </tr>
                       	</tbody>
                    </table>

                </div>
            </div>             
            
             <div class="panel-body col-lg-12">
               <div class="row">
                    <div class="col-xs-4">
                        <div class="table-responsive radius-border">
<div class="col-xs-12" id="msg-erro-edit"></div>
                        </div>
                    </div>
                </div>
                                 
                 
            </div>  
        </div>  
    </div>
</form>

        <!-- Modal -->
 	<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal_gerar" class="modal fade">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header" style="background-color:#095D92">
                      <h4 class="modal-title"><font color="#FFFFFF">GERAR DADOS</font></h4>
                  </div>

                  <div class="modal-body">
                    <div id="resultado_modal">
                          
                    </div>     	   		
                      
                                   
                 </div>                                                                                                             
                  <div class="modal-footer">
                    <button data-dismiss='modal' class='btn btn-default' type='button' style=' color:#FFF; background-color:#FF0000' onclick="limpa_campos_modal()">Fechar</button>

                  </div>
              </div>
          </div>
      </div>   



</div>
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main.js"></script>

</body>
</html>
<? 

	
	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
	include("modal/modal_historico.php");
	
	print "<script language='javascript'>exibe_lote_voucher()</script>";
	
	
} 
?>