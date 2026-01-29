

/** Função Página */
function pagina(janela, largura, altura, descricao) {
    tamanho = "height=" + altura + ",width=" + largura + ",scrollbars=yes,resizable=yes";
    window.open(janela, descricao, tamanho);
}

function fechar(){
    open(location, '_self').close();
}

function fechar_modal(modal){
    $(modal).modal('hide');;
}


/** PARAMETROS DATEPICKER  */


var todayDate = new Date().getDate();
var dia_inicial = new Date(new Date().setDate(todayDate - 5));
//var dia_final = new Date(new Date().setDate(todayDate + 1));

$('.form_datetime').datetimepicker({
	language: 'pt-BR',
    weekStart: 1,
	maxDate: "+1m",
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2    ,
    forceParse: 1,
    showMeridian: 1,
     maxView: 4,
     minView: 2
	 //startDate: dia_inicial,
     //endDate: dia_final
});

/*SOMENTE NUMERO*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58)
      || (key == 46 && acceptDot)
      || (key == 44 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});

/*SOMENTE NUMERO VIRGULA*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number_virgula', function(e) {
    var $this = $(this);
	var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58) || (key == 44)
      || (key == 46 && acceptDot)
      || (key == 44 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});


/*AUTOCOMPLETE RESPONSAVEL PERMISSAO*/
	function passaParametro(param){
	 if ($("#responsavel_permissao").val().length >= 2) {		
			$.ajax({
			type: "POST",
			url: "includes/ajax_responsavel_permissao.php",
			data:'keyword='+param.value,
			beforeSend: function(){
				$("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
			},
			success: function(data){
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);
				$("#busca-box").css("background","#FFF");
			}
			});
      } else {
          $("#suggesstion-box").hide();
      }				
	}
	
	function selectList(val) {
		//alert(1);
	//$("#busca-box").val(val);
		document.getElementById('responsavel_permissao').value = val;
		document.getElementById('responsavel_permissao').focus();
		$("#suggesstion-box").hide();
	}
/*FIM AUTOCOMPLETE*/



/*AUTOCOMPLETE RESPONSAVEL PERMISSAO*/
	function passaParametro(param){
	 if ($("#responsavel_permissao").val().length >= 2) {		
			$.ajax({
			type: "POST",
			url: "includes/ajax_responsavel_permissao.php",
			data:'keyword='+param.value,
			beforeSend: function(){
				$("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
			},
			success: function(data){
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);
				$("#busca-box").css("background","#FFF");
			}
			});
      } else {
          $("#suggesstion-box").hide();
      }				
	}
	
	function selectList(val) {
		//alert(1);
	//$("#busca-box").val(val);
		document.getElementById('responsavel_permissao').value = val;
		document.getElementById('responsavel_permissao').focus();
		$("#suggesstion-box").hide();
	}
/*FIM AUTOCOMPLETE*/


 $(document).ready(function () {
       $('#num_celular').mask('(00)00000-0000');
	   $('#data_ini').mask('00/00/0000');
	   $('#imei').mask('00000000000000000000');

});



function charLimit(limitField, limitNum) 
{
  if (limitField.value.length > limitNum) 
  {
	$('#msg').html("O Valor máximo do campo são "+limitNum+" caracteres.");
	$('#myModal_alert').modal('show');	
	
    limitField.value = limitField.value.substring(0, limitNum);
  }
}

/** FUNÇÃO DATA TABLES */
/*
$(document).ready(function () {
    $('#myTable3').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'excel',
                text: '<i class="fa fa-files-o">Excel</i>',
                title: function () {
                    var d = new Date();
                    var n = d.getSeconds();
                    return "Relatório de Histórico" + n
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row:first c', sheet).attr('s', '2');
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-files-o">PDF</i>',
                orientation: 'landscape',
                pageSize: 'TABLOID'
            },
        ],
        // "order": [
        //     [2, 'asc']
        // ],
        "orderFixed":[[2, 'asc'],[2, 'asc'], [3, 'asc'], [4, 'asc']],
        
        columnDefs: [{
            type: 'date-br'
        }],
    });
});
*/

function logout(ip) 
{

	$.ajax({
		type: "POST", //define o metуdo de passagem de parametros
		url: "includes/logout.php", //chama uma pagina
		success: function (msg) { //pega o retorno da pagina chamada
			//alert(msg);
				
			window.location='http://'+ip+'/SCI/login_portal.php';
		}
	});

	
 
}

function imprimi_div(id)
{
	if (id == 1)
	{
	    var divContents = document.getElementById("procura0").innerHTML;
		var fornecedor = 'BRADESCO';
	}
	else
		if (id == 2)
		{
			var divContents = document.getElementById("procura2").innerHTML;
			var fornecedor = 'AILOG';	
		}
		else
			if (id == 3)
			{
				var divContents = document.getElementById("procura3").innerHTML;
				var fornecedor = 'BRASIL PRÉ-PAGO';	
			}
			else
				if (id == 4)
				{
					var divContents = document.getElementById("procura1").innerHTML;		
					var fornecedor = 'TAG';	
				}
		
			var data_ini	= document.getElementById('data_ini').value;
			var data_fim	= document.getElementById('data_fim').value;
		
			var titulo = 'CONCILIAÇÃO '+fornecedor+' - '+data_ini+' à '+data_fim;
		
            var a = window.open('', '', 'height=1000, width=1000');
            a.document.write('<html>');
            a.document.write(titulo);
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
}


function gerar_excel1(nome_arquivo, id_tabela){ 
	
	$("#escondido").show();   
    $("#"+id_tabela).table2excel({
        exclude: ".excludeThisClass",
        name: nome_arquivo,
        filename: nome_arquivo+".xls", // do include extension
        preserveColors: true, // set to true if you want background colors and font colors preserved
        exclude_img:false
    });
	$("#escondido").hide(); 
}


function seleciona_grupo1(elmnt){

	var aba						= elmnt;
	var lancamento_ecargo		= document.getElementById('lancamento_ecargo').value;
	var status_id 				= document.getElementById('status_id').value;
	var ponto_operacao 			= document.getElementById('ponto_operacao').value;	
	var data_ini				= document.getElementById('data_ini').value;
	var data_fim				= document.getElementById('data_fim').value;


	if ((data_ini == '') || (data_fim == ''))
	{
		$("#aba0").removeClass("inactive").addClass("inactive");
		$("#aba1").removeClass("inactive").addClass("inactive");
		$("#aba2").removeClass("inactive").addClass("inactive");
		$("#aba3").removeClass("inactive").addClass("inactive");		
	}
	else
	{

		switch (elmnt) {
			case 0:
				$("#aba1").removeClass("active").addClass("inactive");
				$("#aba2").removeClass("active").addClass("inactive");
				$("#aba3").removeClass("active").addClass("inactive");			
				$("#aba0").removeClass("inactive").addClass("active");		
				$("#tabela1").hide();
				$("#tabela2").hide();
				$("#tabela3").hide();
				$("#tabela0").show();
				$("#procura0").html("<center><img src='../SCI/images/loading-forever.gif' width='150' height='150' alt='Espere'/></center>");
				$("#procura0").show();			
				break;	
			case 1:
				$("#aba0").removeClass("active").addClass("inactive");
				$("#aba2").removeClass("active").addClass("inactive");
				$("#aba3").removeClass("active").addClass("inactive");				
				$("#aba1").removeClass("inactive").addClass("active");		
				$("#tabela0").hide();
				$("#tabela2").hide();
				$("#tabela3").hide();			
				$("#tabela1").show();
				$("#procura1").html("<center><img src='../SCI/images/loading-forever.gif' width='150' height='150' alt='Espere'/></center>");
				$("#procura1").show();			
				break;	
			case 2:
				$("#aba0").removeClass("active").addClass("inactive");
				$("#aba1").removeClass("active").addClass("inactive");
				$("#aba3").removeClass("active").addClass("inactive");				
				$("#aba2").removeClass("inactive").addClass("active");		
				$("#tabela0").hide();
				$("#tabela1").hide();
				$("#tabela3").hide();			
				$("#tabela2").show();
				$("#procura2").html("<center><img src='../SCI/images/loading-forever.gif' width='150' height='150' alt='Espere'/></center>");
				$("#procura2").show();			
				break;	
			case 3:
				$("#aba0").removeClass("active").addClass("inactive");
				$("#aba1").removeClass("active").addClass("inactive");
				$("#aba2").removeClass("active").addClass("inactive");				
				$("#aba3").removeClass("inactive").addClass("active");		
				$("#tabela0").hide();
				$("#tabela1").hide();
				$("#tabela2").hide();
				$("#tabela3").show();	
				$("#procura3").html("<center><img src='../SCI/images/loading-forever.gif' width='150' height='150' alt='Espere'/></center>");
				$("#procura3").show();			
				break;				
		}

		$("#imprimi0").hide();
		$("#imprimi1").hide();
		$("#imprimi2").hide();
		$("#imprimi3").hide();									
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/filtra_pesquisa.php", //chama uma pagina
			data: "lancamento_ecargo="+lancamento_ecargo + "&status_id=" + status_id + "&data_ini=" +  data_ini + "&data_fim=" + data_fim + "&ponto_operacao=" + ponto_operacao + "&aba=" + aba, 					
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);

				if(msg.indexOf("Data inicial maior que data final") == -1)
				{
					
					  var dados = msg.split("|");
	 
					
					
					if (aba == 0)
					{
						//$("#procura0").hide();
						//$("#tabela0").html(dados[0]);
						
						$("#procura0").html(dados[0]);								
						$("#imprimi0").html(dados[1]);	
						$("#imprimi0").show();								
						
					}
					else
						if (aba == 1)
						{
							$("#procura1").html(dados[0]);								
							$("#imprimi1").html(dados[1]);	
							$("#imprimi1").show();									
							
						}
						else
							if (aba == 2)
							{
								$("#procura2").html(dados[0]);								
								$("#imprimi2").html(dados[1]);	
								$("#imprimi2").show();											
							}
							else
								if (aba == 3)
								{
									$("#procura3").html(dados[0]);								
									$("#imprimi3").html(dados[1]);	
									$("#imprimi3").show();																			
								}
				}
				else
				{
					$('#msg').html(msg);
					$('#myModal_alert').modal('show');
				}			
					
			}
		});	

	}

}


function filtra_pesquisa_btn(){
	
	//var lancamento_ecargo	= document.getElementById('lancamento_ecargo').value;
	//var status_id 			= document.getElementById('status_id').value;
	//var ponto_operacao 		= document.getElementById('ponto_operacao').value;	
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;


	if  ((data_ini == '') && (data_fim == ''))
	{
		$('#msg').html('Favor informar o Período');
		$('#myModal_alert').modal('show');		
	}
	else
	if  ((data_ini == '') && (data_fim != ''))
	{
		$('#msg').html('Favor informar a Data Inicial');
		$('#myModal_alert').modal('show');		
	}
	else
	if  ((data_ini != '') && (data_fim == ''))
	{
		$('#msg').html('Favor informar a Data Final');
		$('#myModal_alert').modal('show');		
	}	
	else	
	{
		seleciona_grupo1(0);
		
		/*
		$("#procura").html("<center><img src='../SCI/images/loading-forever.gif' width='150' height='150' alt='Espere'/></center>");
		$("#procura").show();			
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/filtra_pesquisa.php", //chama uma pagina
			data: "lancamento_ecargo="+lancamento_ecargo + "&status_id=" + status_id + "&data_ini=" +  data_ini + "&data_fim=" + data_fim + "&ponto_operacao=" + ponto_operacao, 					
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				
				
				$("#procura").hide();

				if(msg.indexOf("Data inicial maior que data final") == -1)
				{
					$("#exibe_resultado").html(msg);
				}
				else
				{
					$('#msg').html(msg);
					$('#myModal_alert').modal('show');
				}			
					
			}
		});	
		*/
	}

}

/*
function confere_tag(data,valor){
 
  //var id_item			= document.getElementById('id_item').value;
  //var id_sistema		= document.getElementById('id_sistema').value; 
  //var id				= document.getElementById('id') ? document.getElementById('id').value : "";

  if (data == '')
  {

	$('#msg').html('Favor selecionar a Data');
    $('#myModal_alert').modal('show');

  }
  else
  {

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_conferencia_tag.php", //chama uma pagina
      data: "data="+data + "&valor=" + valor,
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
		  document.getElementById('id').value = dados[1];          
		   document.getElementById('excluir').disabled = false;
        }

      }
    });

  }

}
*/
function grava_tarifa(fornecedor_id,data,valor){
 
  var id_item			= document.getElementById('id_item').value;
  var id_sistema		= document.getElementById('id_sistema').value; 
  //var id				= document.getElementById('id') ? document.getElementById('id').value : "";


  if (valor == '')
  {

	$('#msg').html('Favor informar o valor');
    $('#myModal_alert').modal('show');

  }
  else
  {

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_tarifa.php", //chama uma pagina
      data: "data="+data + "&valor=" + valor + "&fornecedor_id=" + fornecedor_id + "&id_sistema=" + id_sistema + "&id_item=" + id_item,
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

	  	  seleciona_grupo1(1);
        }

      }
    });

  }

}


function confere_saldo_pedagio(fornecedor_id,data,valor){
 
  var id_item			= document.getElementById('id_item').value;
  var id_sistema		= document.getElementById('id_sistema').value; 
  //var id				= document.getElementById('id') ? document.getElementById('id').value : "";

  if (fornecedor_id == 4) //TAG
  	var tarifa = document.getElementById('tarifa_tag').value; 
  else
  	var tarifa = '';  
  

  if (data == '')
  {

	$('#msg').html('Favor selecionar a Data');
    $('#myModal_alert').modal('show');

  }
  else
  {

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_conferencia_saldo_pedagio.php", //chama uma pagina
      data: "data="+data + "&valor=" + valor + "&fornecedor_id=" + fornecedor_id + "&id_sistema=" + id_sistema + "&id_item=" + id_item + "&tarifa=" + tarifa,
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

		
			if (fornecedor_id == 2)
				var aba = 2;
			else
				if (fornecedor_id == 3)
					var aba = 3;
				else
					if (fornecedor_id == 4)
						var aba = 2;

		 	seleciona_grupo1(aba);
        }

      }
    });

  }

}

function exibe_ultimos() {
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_ultimos_upload.php", //chama uma pagina
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			var dados = msg.split("|");
			
			
			$("#ultimos").html(msg);
			
		}
	});		
}


function insere_arquivo() {
    var file_data = $('#file').prop('files')[0];
    var form_data = new FormData();
	form_data.append('file', file_data);
   // alert(form_data);
   
   var fornecedor_id	= document.getElementById('fornecedor_id').value;


   
	if (fornecedor_id == '')
	{
		$('#msg').html('Favor selecionar o Fornecedor');
		$('#myModal_alert').modal('show');
	}	
	else	
	{   

		$.ajax({
				url: 'includes/anexa_planilha.php?fornecedor_id='+fornecedor_id, 
				dataType: 'text', // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,
				type: 'post',
				success: function(php_script_response){
				console.log(php_script_response); // display response from the PHP script, if any
				//alert(php_script_response);
				
				if (php_script_response == -1)
				{
					$('#msg').html('Favor selecionar um arquivo csv');
					$('#myModal_alert').modal('show');						
				}
				else				
				if (php_script_response == -2)
				{
					$('#msg').html('Sessão Expirada, favor logar novament');
					$('#myModal_alert').modal('show');						
				}	
				else				
				if (php_script_response == -3)
				{
					$('#msg').html('Favor selecionar o arquivo');
					$('#myModal_alert').modal('show');						
				}				
				else
				if (php_script_response < 1)
				{

					{
						$('#msg').html('Nenhum registro importado');
						$('#myModal_alert').modal('show');
					}
		
				}
				else	
				{
					$('#msg_success').html('Foram importados '+php_script_response+' registros');
					$('#myModal_alert_sucess').modal('show');
					
					exibe_ultimos();
					//document.getElementById('fornecedor_id').value = '';
					//document.getElementById('file').value = '';
				}
				
				//exibe_lote_voucher();						
				
			}
		});
	
	}
	
}

function lancamento_divergente(fornecedor_id, id, nsu,data,valor){


	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/dados_lancamento_divergente.php", //chama uma pagina
		data: "nsu="+nsu + "&data=" + data + "&valor=" + valor + "&fornecedor_id=" + fornecedor_id + "&id=" + id, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			var dados = msg.split("|");
			
			
			$("#resultado_modal").html(dados[1]);
			$('#myModal_informacoes').modal('show');	
			
		}
	});	
	
}




function grava_observacao_divergente(fornecedor_id, id, nsu, valor)
{	
	
	if ((fornecedor_id == 1) || (fornecedor_id == 4))
		var documento = nsu;
	else
		var documento	= document.getElementById('documento_duplicado').value;
	
	
	
	var observacao_divergente	= document.getElementById('observacao_divergente').value;
	var id_sistema 				= document.getElementById('id_sistema').value;
	var id_item 				= document.getElementById('id_item').value;	
	


	if ((observacao_divergente == ''))
	{
		$('#msg').html('Favor informar a observação');
		$('#myModal_alert').modal('show');
	} 
	else
	{
	
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/grava_observacao_divergente.php", //chama uma pagina
			data: "nsu="+documento + "&valor="+valor + "&observacao=" + observacao_divergente + "&id_item=" + id_item + "&id_sistema=" + id_sistema + "&fornecedor_id=" + fornecedor_id + "&id=" + id, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				
				if(msg.indexOf("ok") == -1)
				{
					$('#msg').html(msg);
					$('#myModal_alert').modal('show');		
				}
				else
				{
					
					$('#msg_success').html('Gravação efetuada com sucesso')	
						
					$('#myModal_alert_sucess').modal('show');	

					$('#myModal_informacoes').modal('hide');
			
					if (fornecedor_id == 1)
						var aba = 0;
					else
						if (fornecedor_id == 2)	
							var aba = 2;	
						else
							if (fornecedor_id == 3)	
								var aba = 3;	
							else
								if (fornecedor_id == 4)	
									var aba = 1;														
					
					seleciona_grupo1(aba);
			
				}	
			}
		});	
	}

}

function cancela_finaliza_divergente(acao,lancamento_id,fornecedor_id)
{	
	
	var id_sistema 				= document.getElementById('id_sistema').value;
	var id_item 				= document.getElementById('id_item').value;	

	//alert(lancamento_id);
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/cancela_finaliza_divergente.php", //chama uma pagina
		data: "lancamento_id="+lancamento_id + "&id_item=" + id_item + "&id_sistema=" + id_sistema + "&acao=" + acao, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			if(msg.indexOf("ok") == -1)
			{
				$('#msg').html(msg);
				$('#myModal_alert').modal('show');		
			}
			else
			{
				$('#msg_success').html('Gravação efetuada com sucesso')	
					
				$('#myModal_alert_sucess').modal('show');	

				document.getElementById('observacao_divergente').value = '';
				
				$('#myModal_informacoes').modal('hide');					
			
				if (fornecedor_id == 1)
					var aba = 0;
				else
					if (fornecedor_id == 2)	
						var aba = 2;	
					else
						if (fornecedor_id == 3)	
							var aba = 4;	
						else
							if (fornecedor_id == 4)	
								var aba = 1;														
				
				seleciona_grupo1(aba);				
		
			}	
		}
	});	
	

}