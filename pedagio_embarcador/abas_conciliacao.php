<?php 

session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";

$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$dados = new Dados;	

$localItem = "../conciliacao_pamcary/abas_conciliacao.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$id_sistema  = $dados_autenticacao->id_sistema;
$usuario_id  = $dados_autenticacao->usuario_id;

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




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Conciliação Pamcary</title>
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

<style>
.barra{
  width: 1150px;
  height: 200px;
  overflow-y: scroll;
}

.cor_aba{
  background-color:#E8E8E8;
}

.active{
    background-color: #A4A4A4;
    font-size: 12px;
    font-weight: bold;
    color: black !important;
}

.inactive{
    background-color: #E8E8E8;
    font-size: 12px;
    font-weight: inherit;
}

table {
	border:none!important;
}

tr {
	border:none!important;
}

td {
	border:none!important;
}

</style>

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


</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Conciliação Pamcary</b></div>
    </div>
    <br>
<div class="col-xs-12">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-12 panel-padrao">
          <div class="panel-body col-xs-12">
                <div class="table-responsive covre">
                 <table width="200%" class="table table-bordered tb-padrao">
                  <tr>
                    <td width="6" class="no-border left altura">&nbsp;</td>
                    <td width="100" class="no-border left altura"><font size="-1"><b>Período: </b></font></td>
                    <td width="434" class="no-border left altura">
						<div class="col-xs-3 left-inline ">	
                          <div class="input-group date form_datetime col-md-11"  data-date-format="dd/mm/yyyy">
                            <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_ini' id='data_ini' size='9' maxlength='10' value=''>  
                            <span class="input-group-addon altura-campo">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>                                                         
                          </div>
                        </div>  
                        <div class="col-xs-1 left-inline">	
                            <font size="-1"><b>&nbsp;&nbsp;à&nbsp;&nbsp;</b></font> 
                        </div>                        
                        <div class="col-xs-3 left-inline ">	
                          <div class="input-group date form_datetime col-md-11"  data-date-format="dd/mm/yyyy">
                            <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='data_fim' id='data_fim' size='9' maxlength='10' value='' >  
                            <span class="input-group-addon altura-campo">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>                                                         
                           </div>
                        </div>                     
                    </td>
                    <td width="591" class="no-border left altura">
                                        <input name="id_sistema" type="hidden" id="id_sistema" value="<?php print $id_sistema ?>">
					<input name="id_item" type="hidden" id="id_item" value="<?php print $id_item ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura"><font size="-1"><b>Lanç. E-Cargo: </b></font></td>
                    <td class="no-border left altura"><div class="col-xs-3 left-inline"> <span class="col-xs-11 left-inline"> 
                      <?php
                        $query = "Select 'v' sigla, 'Vazio'
								  union
								  Select 't' sigla, 'Todos'
								  order by sigla";
                        //print $query;
                        $result = $con->executar($query);

                        print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='lancamento_ecargo' id='lancamento_ecargo' value=''>";

                        while (odbc_fetch_row($result))
                        {

                          print "<option value='" . odbc_result($result, 1) . "'$selected>" .odbc_result($result, 2). "</option>";
                        }
                        print "</select>";
                        ?>
                    </span></div></td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr> 
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura"><font size="-1"><b>Status: </b></font></td>
                    <td class="no-border left altura"><div class="col-xs-3 left-inline"> <span class="col-xs-11 left-inline"> 
                      <?php
                        $query = "Select 'd' sigla, 'Diferenca'
								  union
								  Select 't' sigla, 'Todos'
								  order by sigla desc";
                        //print $query;
                        $result = $con->executar($query);

                        print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='status_id' id='status_id' value=''>";

                        while (odbc_fetch_row($result))
                        {

                          print "<option value='" . odbc_result($result, 1) . "'$selected>" .odbc_result($result, 2). "</option>";
                        }
                        print "</select>";
                        ?>
                    </span></div></td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr> 
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td class="no-border left altura"><font size="-1"><b>P.Operação: </b></font></td>
                    <td class="no-border left altura"><div class="col-xs-6 left-inline"> <span class="col-xs-11 left-inline"> 
                      <?php
                        $query = "SELECT 
									P.PJ_CGC,
									P.NOME_FANTASIA
								FROM CARGOSOL..PONTO_OPERACAO AS PO WITH (NOLOCK)
								JOIN CARGOSOL..PESSOA AS P WITH (NOLOCK) ON
									PO.PESSOA_ID = P.PESSOA_ID
								WHERE PO.TAB_STATUS_ID = 1
									AND P.PJ_CGC IS NOT NULL";
                        //print $query;
                        $result = $con->executar($query);

                        print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='ponto_operacao' id='ponto_operacao' value=''>";

                        while (odbc_fetch_row($result))
                        {

                          print "<option value='" . odbc_result($result, 1) . "'$selected>" .odbc_result($result, 2). "</option>";
                        }
                        print "</select>";
                        ?>
                    </span></div></td>
                    <td class="no-border left altura">&nbsp;</td>
                  </tr>                                                      
                  <tr>
                    <td class="no-border left altura">&nbsp;</td>
                    <td colspan="3" class="no-border left altura">
                    <button type='button' class='btn btn-theme altura-campo' id='pesquisar' name='pesquisar' style=' color:#FFF; background-color:#095D92' onClick='javascript:filtra_pesquisa_btn()'>Pesquisar</button>
                    <div id="exibe_resultado"></div></td>
                  </tr>                  
                  
                </table>
         
                    <div class="col-xs-12 tabela2" <? echo $tabela2 ?>>
                        <ul class="nav nav-tabs ">
                          <li><a id="aba0" href="javascript:void(0)" onclick="seleciona_grupo1(0)" class="inactive" >Bradesco</a></li>
                            <li><a id="aba1" href="javascript:void(0)" onclick="seleciona_grupo1(1)" class="inactive" >TAG</a></li>
                            <li><a id="aba2" href="javascript:void(0)" onclick="seleciona_grupo1(2)" class="inactive" >AiLog</a></li>
                            <li><a id="aba3" href="javascript:void(0)" onclick="seleciona_grupo1(3)" class="inactive" >Brasil Pré-Pago</a></li>                            
                        </ul>
                    </div>         
				<div id="tabela0">
                    <p>                
                         <div id="procura0" style="display:none"></div>   
                         <div id="imprimi0" style="display:none"></div>                                          
				</div>
				<div id="tabela1" style="display:none">
        			<p> 
                         <div id="procura1" style="display:none"></div>	
                         <div id="imprimi1" style="display:none"></div>				
				</div>     

				<div id="tabela2" style="display:none">
					<p> 
                         <div id="procura2" style="display:none"></div>
                         <div id="imprimi2" style="display:none"></div>
				</div>   
                
				<div id="tabela3" style="display:none">
					<p> 
                         <div id="procura3" style="display:none"></div>
                         <div id="imprimi3" style="display:none"></div>
				</div>   
		                            
         
              </div>
            </div>             
            
             <div class="panel-body col-lg-12">
               <div class="row">
                    <div class="col-xs-12">
                        <div class="table-responsive radius-border">
<input type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p" value="Fechar">
                        </div>
                    </div>
                </div>
                                 
                 
            </div>  
        </div>  

</form>

 	<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal_informacoes" class="modal fade">
          <div class="modal-dialog">
              <div class="modal-content" style="width:1150px !important;">
                  <div class="modal-header" style="background-color:#095D92">
                      <h4 class="modal-title"><font color="#FFFFFF">LANÇAMENTO DIVERGENTE</font></h4>
                  </div>

                  <div class="modal-body">
                    <div id="resultado_modal"></div>  
                                  
          
                     </div>                                                                                                             
                      <div class="modal-footer">
                        <button data-dismiss='modal' class='btn btn-default' type='button' style=' color:#FFF; background-color:#FF0000' onclick="limpa_campos_modal()">Fechar</button>
    
                      </div>                 
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
<script type="text/javascript">
if(typeof tableScroll == 'function')
{
	tableScroll('TabelaPrincipal');
}
</script>
<?php
	
	include("../SCI/includes/modal.php");



}
?>