<?php

session_name("covre_ti");
session_start();
ini_set('memory_limit','2048M');


require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";

class Dados extends ConexaoSCI{



	public function exibeDados($aba, $lancamento_ecargo, $status_id, $ponto_operacao, $data_inicial, $data_final, $tipo_relatorio_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$ip = $_SERVER['HTTP_HOST'];
	
		
		if ($lancamento_ecargo == 't')
		{
			$condicao_lancamento_ecargo0 = '';	
			$condicao_lancamento_ecargo1 = '';
			$condicao_lancamento_ecargo2 = '';
			$condicao_lancamento_ecargo3 = '';			
		}
		else
		{
			$condicao_lancamento_ecargo0 = "AND LANCAMENTO_ECARGO IS NULL";			
			$condicao_lancamento_ecargo1 = "AND VALE_FRETE.NUM_COMPROVANTE_PEDAGIO IS NULL";
			$condicao_lancamento_ecargo2 = "AND FXCX.NUMERODOCUMENTO IS NULL";
			$condicao_lancamento_ecargo3 = "AND FXCX.NUMERODOCUMENTO IS NULL";
		}
		
		if ($status_id == 't')
		{
			$condicao_status0 = '';	
			$condicao_status1 = '';
			$condicao_status2 = '';
			$condicao_status3 = '';			
		}
		else
		{
			$condicao_status0 = "AND ((((LANCAMENTO_ID is null) and (LANCAMENTO_ECARGO is null) and (CREDITO_BRADESCO is null) and 
								(LANCAMENTO_BRADESCO LIKE '%CARGA CARTAO%')) or ((NSU_PAMCARY is null) and (CREDITO_BRADESCO is null) and 
								(LANCAMENTO_BRADESCO LIKE '%CARGA CARTAO%'))) OR (DEBITO_BRADESCO <> VALOR_PAMCARY) OR 
								(DEBITO_BRADESCO <> VALOR_ECARGO) OR (VALOR_PAMCARY <> VALOR_ECARGO))";			
			$condicao_status1 = "AND CAST(SUBSTRING(cast(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) != VAL_PEDAGIO";
			$condicao_status2 = "AND ISNULL(ABS(DADOS.VALOR),0) <> ISNULL(ABS(FXCX.VALOR),0)";
			$condicao_status3 = "AND ISNULL(ABS(DADOS.VALOR),0) <> ISNULL(ABS(FXCX.VALOR),0)";
		}
		
			
		if (($data_inicial != '') && ($data_final != ''))
		{
			$query = "select 
						case when datediff(dd,'$data_inicial','$data_final') < 0
								then 'menor'
								else null
						end";
			//print "<pre>$query</pre>";
			$result = $con->executar($query);			
			$verifica_datas		= odbc_result($result,1);			

		}

		if ($verifica_datas == 'menor')
			print 'Data inicial maior que data final';
		else
		{

			
			if ($aba == 0) //BRADESCO
			{			  
				$table = "<br><br><br>
					<table class='table table-bordered table-hover' id='myTable3'>
						<thead>
							<th style='border:solid 1px #708090!important;' colspan='6' bgcolor='#d3d3d3'><b><center>
								<font size='-2' color='blue'>BRADESCO</font></th>
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>
							<th style='border:solid 1px #708090!important;' colspan='5' bgcolor='#d3d3d3'><b><center>
								<font size='-2' color='blue'>PAMCARY</font></th>
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>				
							<th style='border:solid 1px #708090!important;' colspan='3' bgcolor='#d3d3d3'><b><center>
								<font size='-2' color='blue'>E-CARGO</font></th>																
						</thead>
						<thead>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DOCUMENTO</font></th>	
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>NSU/AUTORIZAÇÃO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>CRÉDITO</font></th>	
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DÉBITO</font></th>	
							
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>		
							
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>NSU/AUTORIZAÇÃO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>P.OPERAÇÃO</font></th>	
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DESCRIÇÃO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
							
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>		
							
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>																	
						</thead><tbody>";	
						
				$query = "
				SELECT *
				FROM (
						--ADTO BRADESCO
						select distinct
						CONVERT(varchar(10), FXCX.DATACOMPENSACAO, 103) DATA_BRADESCO,
						ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
						SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
						FXCX.NUMERODOCUMENTO NSU_BRADESCO,
						CASE WHEN FXCX.VALOR >= 0  
								THEN cast(FXCX.VALOR  as numeric(15,2))
						end CREDITO_BRADESCO,
						CASE WHEN FXCX.VALOR < 0  
								THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
						end DEBITO_BRADESCO,
						SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
						case when (LO.TAB_STATUS_ID = 1103 AND LO.SUB_GRUPO_CONTABIL_ID = 5060)
								then 'TAXAS'
								ELSE case when TAB_TIPO_VINCULO_ID = 1
											then 'ADTO FROTA'
											else 'ADTO AGREG'
									 END
						END DESCRICAO_PAMCARY,
						cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_PAMCARY,
						
						convert(varchar(10), isnull(LO.data_efet,LO.data_vencto), 103) DATA_ECARGO,
						LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
						LO.valor_lanc_rs VALOR_ECARGO,
						
						tipo_transacao TIPO_TRANSACAO,
						ld.id LANCAMENTO_ID,
						LAYOUT_ARQUIVO.id_parcela_cliente,
						P_PO.NOME_FANTASIA
						FROM Corpore..FXCX FXCX with (nolock)
						JOIN layout_arquivo LAYOUT_ARQUIVO with (nolock) on
							RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
							AND LAYOUT_ARQUIVO.tipo_transacao = 2
							AND dateadd(dd,datediff(dd,0,CAST(LAYOUT_ARQUIVO.data_transacao AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$data_inicial') and '$data_final'
							AND LAYOUT_ARQUIVO.identificacao_parcela = 1	
						LEFT JOIN CARGOSOL..PESSOA PES with (nolock) on
							isnull(PES.pj_cgc, pes.pf_cpf) = layout_arquivo.cpf_cnpj_contratado collate SQL_Latin1_General_CP1_CI_AS
							and PES.TAB_STATUS_ID <> 2
							
						LEFT JOIN CARGOSOL..COLABORADOR co  with (nolock)on
							co.PESSOA_ID = pes.pessoa_id
						
						LEFT JOIN CARGOSOL..LANCAMENTO_OPERACAO LO with (nolock) on
							LO.LANCAMENTO_OPERACAO_ID = LAYOUT_ARQUIVO.id_parcela_cliente
							AND LO.TAB_STATUS_ID IN (1,1103)
							AND LO.SUB_GRUPO_CONTABIL_ID IN (2081,5060)
							AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535)	
							AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
							
						LEFT JOIN lancamento_divergente ld with (nolock) on
							ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS	
							and ld.status_id not in ('i')
							
						LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
							FRECONCILRELAC.IDXCX = FXCX.IDXCX
						AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
						
						LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
							FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
						AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
						
						LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
							P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
							AND P_PO.TAB_STATUS_ID = 1
							
						WHERE dateadd(dd,datediff(dd,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
						AND FXCX.CODCXA = '00014'
						AND FXCX.CODCOLIGADA = 1
						AND FXCX.COMPENSADO = 1
						
						UNION
						
						--SALDO 1
						select distinct
						CONVERT(varchar(10), FXCX.DATACOMPENSACAO, 103) DATA_BRADESCO,
						ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
						SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
						FXCX.NUMERODOCUMENTO NSU_BRADESCO,
						CASE WHEN FXCX.VALOR >= 0  
								THEN cast(FXCX.VALOR  as numeric(15,2))
						end CREDITO_BRADESCO,
						CASE WHEN FXCX.VALOR < 0  
								THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
						end DEBITO_BRADESCO,
						SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
						'SALD AGREG' DESCRICAO_PAMCARY,
						CASE WHEN LEN(VALOR_TRANSACAO) = 1
								THEN CAST('0.0'+cast(LAYOUT_ARQUIVO.valor_transacao as varchar) AS NUMERIC(15,2))
								ELSE cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2))
						END VALOR_PAMCARY,
						convert(varchar(10), isnull(LO.data_efet,LO.data_vencto), 103) DATA_ECARGO,
						LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
						LO.valor_lanc_rs VALOR_ECARGO,
						tipo_transacao TIPO_TRANSACAO,
						ld.id LANCAMENTO_ID,
						LAYOUT_ARQUIVO.id_parcela_cliente,
						P_PO.NOME_FANTASIA
						FROM CORPORE..FXCX FXCX with (nolock)
						JOIN layout_arquivo LAYOUT_ARQUIVO with (nolock) on
							RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
						AND dateadd(dd,datediff(dd,0,CAST(LAYOUT_ARQUIVO.data_transacao AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$data_inicial') and '$data_final'
						LEFT JOIN CARGOSOL..PESSOA PES with (nolock) on
							isnull(PES.pj_cgc, pes.pf_cpf) = layout_arquivo.cpf_cnpj_contratado collate SQL_Latin1_General_CP1_CI_AS
							and PES.TAB_STATUS_ID <> 2
						LEFT JOIN CARGOSOL..COLABORADOR co  with (nolock)on
							co.PESSOA_ID = pes.pessoa_id
						LEFT JOIN CARGOSOL..LANCAMENTO_OPERACAO LO with (nolock) on
							LAYOUT_ARQUIVO.id_parcela_cliente = LO.LANCAMENTO_OPERACAO_ID
							AND LO.TAB_STATUS_ID IN (1, 1086)
							AND LO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535,1086)	
							AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
						LEFT JOIN lancamento_divergente ld with (nolock) on
							ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS	
							and ld.status_id not in ('i')
						LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
							FRECONCILRELAC.IDXCX = FXCX.IDXCX
						AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
						LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH (NOLOCK) ON
							FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
						AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
						LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
							P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
							AND P_PO.TAB_STATUS_ID = 1
						WHERE dateadd(dd,datediff(dd,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
						AND FXCX.CODCXA = '00014'
						AND FXCX.CODCOLIGADA = 1
						AND FXCX.COMPENSADO = 1
						AND LAYOUT_ARQUIVO.tipo_transacao = 2
						AND LAYOUT_ARQUIVO.identificacao_parcela = 3
						
						UNION
						
						--SALDO 2
						select distinct 
						CONVERT(varchar(10), FXCX.DATACOMPENSACAO, 103) DATA_BRADESCO,
						ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
						SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
						FXCX.NUMERODOCUMENTO NSU_BRADESCO,
						CASE WHEN FXCX.VALOR >= 0  
								THEN cast(FXCX.VALOR  as numeric(15,2))
						end CREDITO_BRADESCO,
						CASE WHEN FXCX.VALOR < 0  
								THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
						end DEBITO_BRADESCO,
						SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
						'SALD AGREG' DESCRICAO_PAMCARY,
						CASE WHEN LEN(VALOR_TRANSACAO) = 1
								THEN CAST('0.0'+cast(LAYOUT_ARQUIVO.valor_transacao as varchar) AS NUMERIC(15,2))
								ELSE cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2))
						END VALOR_PAMCARY,
						convert(varchar(10), isnull(LO.data_efet,LO.data_vencto), 103) DATA_ECARGO,
						LO.LANCAMENTO_OPERACAO_ID LANCAMENTO_ECARGO,
						LO.valor_lanc_rs VALOR_ECARGO,
						tipo_transacao TIPO_TRANSACAO,
						ld.id LANCAMENTO_ID,
						LAYOUT_ARQUIVO.id_parcela_cliente,
						P_PO.NOME_FANTASIA
						FROM Corpore..FXCX FXCX with (nolock)
						JOIN layout_arquivo LAYOUT_ARQUIVO with (nolock) on
							RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
						AND dateadd(dd,datediff(dd,0,CAST(LAYOUT_ARQUIVO.data_transacao AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$data_inicial') and '$data_final'
						LEFT JOIN CARGOSOL..PESSOA PES with (nolock) on
							isnull(PES.pj_cgc, pes.pf_cpf) = layout_arquivo.cpf_cnpj_contratado collate SQL_Latin1_General_CP1_CI_AS
							and PES.TAB_STATUS_ID <> 2
						LEFT JOIN CARGOSOL..COLABORADOR co  with (nolock)on
							co.PESSOA_ID = pes.pessoa_id
						JOIN CARGOSOL..LANCAMENTO_OPERACAO LO with (nolock) on
							ISNULL(LO.FORNECEDOR_ID, LO.COLABORADOR_ID) = PES.PESSOA_ID
							AND replace(lo.valor_lanc_rs,'.',',') = CASE WHEN LEN(VALOR_TRANSACAO) = 1
																			THEN '0,0'+cast(LAYOUT_ARQUIVO.valor_transacao as varchar)
																			ELSE SUBSTRING(cast(LAYOUT_ARQUIVO.valor_transacao as varchar), 1, len(cast(LAYOUT_ARQUIVO.valor_transacao as varchar))-2)+','+SUBSTRING(cast(LAYOUT_ARQUIVO.valor_transacao as varchar), len(cast(LAYOUT_ARQUIVO.valor_transacao as varchar))-1, 2)
																	END
							AND (LAYOUT_ARQUIVO.DATA_TRANSACAO BETWEEN CONVERT(VARCHAR,ISNULL(LO.DATA_EFET, FXCX.DATACOMPENSACAO),112) AND CONVERT(VARCHAR,dateadd(hh,3,ISNULL(LO.DATA_EFET, LO.DATA_VENCTO)),112)) 
							AND ((LAYOUT_ARQUIVO.id_parcela_cliente = 0) OR (LAYOUT_ARQUIVO.id_parcela_cliente IS NULL))
							AND LO.TAB_STATUS_ID IN (1, 1086)
							AND LO.SUB_GRUPO_CONTABIL_ID IN (5015)
							AND LO.TAB_STATUS_PAGTO_ID IN (1,135,525,535,1086)
							AND ((LO.TAB_FORMA_PAGTO_ID = 17) OR (LO.TAB_FORMA_PAGTO_ID IS NULL))
						LEFT JOIN lancamento_divergente ld with (nolock) on
							ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS	
							and ld.status_id not in ('i')
						LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
							FRECONCILRELAC.IDXCX = FXCX.IDXCX
						AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
						LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH (NOLOCK) ON
							FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
						AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
						LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
							P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
							AND P_PO.TAB_STATUS_ID = 1
						WHERE dateadd(dd,datediff(dd,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
						AND FXCX.CODCXA = '00014'
						AND FXCX.CODCOLIGADA = 1
						AND FXCX.COMPENSADO = 1
						AND LAYOUT_ARQUIVO.tipo_transacao = 2
						AND LAYOUT_ARQUIVO.identificacao_parcela = 3															
						
						UNION
				
						--DIVERSOS
						select distinct
						CONVERT(varchar(10), FXCX.DATACOMPENSACAO, 103) DATA_BRADESCO,
						ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
						SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
						FXCX.NUMERODOCUMENTO NSU_BRADESCO,
						CASE WHEN FXCX.VALOR >= 0  
								THEN cast(FXCX.VALOR  as numeric(15,2))
						end CREDITO_BRADESCO,
						CASE WHEN FXCX.VALOR < 0  
								THEN cast(ABS(FXCX.VALOR)  as numeric(15,2))
						end DEBITO_BRADESCO,
						SUBSTRING(cast(data_transacao as varchar), 7, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 5, 2)+'/'+SUBSTRING(cast(data_transacao as varchar), 1, 4) DATA_PAMCARY,
						RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
						NULL DESCRICAO_PAMCARY,
						cast(SUBSTRING(cast(valor_transacao as varchar), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_PAMCARY,
						NULL DATA_ECARGO,
						NULL LANCAMENTO_ECARGO,
						NULL VALOR_ECARGO,
						tipo_transacao TIPO_TRANSACAO,
						ld.id LANCAMENTO_ID,
						LAYOUT_ARQUIVO.id_parcela_cliente,
						P_PO.NOME_FANTASIA
						FROM Corpore..FXCX FXCX with (nolock)
						LEFT JOIN layout_arquivo LAYOUT_ARQUIVO with (nolock) on
							RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+right('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
						LEFT JOIN lancamento_divergente ld with (nolock) on
							ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS	
							and ld.status_id not in ('i')
						LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
							FRECONCILRELAC.IDXCX = FXCX.IDXCX
						AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
						LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
							FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
						AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
						LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
							P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
							AND P_PO.TAB_STATUS_ID = 1
						WHERE dateadd(dd,datediff(dd,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
						AND FXCX.CODCXA = '00014'
						AND FXCX.CODCOLIGADA = 1
						AND FXCX.COMPENSADO = 1
						AND LAYOUT_ARQUIVO.id IS NULL				
						
						UNION
						
						--PEDAGIO BRADESCO
						SELECT *
						FROM (
								SELECT DISTINCT 
								DATA_BRADESCO, LANCAMENTO_BRADESCO, DOCUMENTO, NSU_BRADESCO, 
								CREDITO_BRADESCO, DEBITO_BRADESCO, DATA_PAMCARY, 
								NSU_PAMCARY, DESCRICAO_PAMCARY, VALOR_PAMCARY,
								DATA_ECARGO,
								LANCAMENTO_ECARGO,
								VALOR_ECARGO,
								TIPO_TRANSACAO,
								LANCAMENTO_ID,
								ID_PARCELA_CLIENTE,
								NOME_FANTASIA
								FROM (
										SELECT DISTINCT
										CONVERT(VARCHAR(10), FXCX.DATACOMPENSACAO, 103) DATA_BRADESCO,
										ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO) LANCAMENTO_BRADESCO,
										SUBSTRING(FXCX.NUMERODOCUMENTO, 1, 7) DOCUMENTO,
										FXCX.NUMERODOCUMENTO NSU_BRADESCO,
										CASE WHEN FXCX.VALOR >= 0  
												THEN CAST(FXCX.VALOR  AS NUMERIC(15,2))
										END CREDITO_BRADESCO,
										CASE WHEN FXCX.VALOR < 0  
												THEN CAST(ABS(FXCX.VALOR)  AS NUMERIC(15,2))
										END DEBITO_BRADESCO,
										SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 7, 2)+'/'+SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 5, 2)+'/'+SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 1, 4) DATA_PAMCARY,
										RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) NSU_PAMCARY,
										CASE WHEN VALOR_TRANSACAO IS NOT NULL		
												THEN 'PEDAGIO'
												ELSE NULL
										END DESCRICAO_PAMCARY,
										CAST(SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2)) VALOR_PAMCARY,
										ISNULL(CONVERT(VARCHAR(10), VF.DATA_EMISSAO, 103) , CONVERT(VARCHAR(10), VF.DATA_ULT_ALT, 103)) DATA_ECARGO,
										VF.NUM_COMPROVANTE_PEDAGIO LANCAMENTO_ECARGO,
										VF.VAL_PEDAGIO VALOR_ECARGO,
										TIPO_TRANSACAO TIPO_TRANSACAO,
										LD.ID LANCAMENTO_ID,
										LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE,
										CASE WHEN LEN(LAYOUT_ARQUIVO.CNPJ_PONTO_EMB) = 14
												THEN P_PO.NOME_FANTASIA
												ELSE P_PO2.NOME_FANTASIA
										END NOME_FANTASIA
										FROM CORPORE..FXCX FXCX WITH (NOLOCK)
										LEFT JOIN LAYOUT_ARQUIVO LAYOUT_ARQUIVO WITH (NOLOCK) ON
											RTRIM(LAYOUT_ARQUIVO.DOCUMENTO_EXTRATO)+RIGHT('00000000'+RTRIM(LAYOUT_ARQUIVO.NUM_AUTORIZACAO_2),8) = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS
											AND DATEADD(DD,DATEDIFF(DD,0,CAST(LAYOUT_ARQUIVO.DATA_TRANSACAO AS VARCHAR)),0) BETWEEN DATEADD(DD, -10, '$data_inicial') AND '$data_final'
										LEFT JOIN CARGOSOL..VALE_FRETE VF WITH (NOLOCK) ON	
											VF.NUM_COMPROVANTE_PEDAGIO = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)
											AND VF.FLAG_INTEGRACAO = 'S'
											AND VF.TAB_STATUS_ID <> 2
										LEFT JOIN CARGOSOL..MANIFESTO_ROD MR WITH (NOLOCK) ON
											MR.VALE_FRETE_ID = VF.VALE_FRETE_ID
											AND MR.TAB_STATUS_ID <> 2
										LEFT JOIN CARGOSOL..COLABORADOR CO WITH (NOLOCK) ON
											CO.COLABORADOR_ID = MR.COLABORADOR_ID
											AND CO.TAB_TIPO_VINCULO_ID <> 1			
										LEFT JOIN LANCAMENTO_DIVERGENTE LD WITH (NOLOCK) ON
											LD.NSU = FXCX.NUMERODOCUMENTO COLLATE LATIN1_GENERAL_CI_AS	
											and ld.status_id not in ('i')
										LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
											FRECONCILRELAC.IDXCX = FXCX.IDXCX
										AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
										LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
											FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
										AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA	
										LEFT JOIN CARGOSOL..PESSOA AS P_PO WITH (NOLOCK) ON
											P_PO.PJ_CGC = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
											AND P_PO.TAB_STATUS_ID = 1
										LEFT JOIN CARGOSOL..PONTO_OPERACAO AS PONTO_OPERACAO WITH(NOLOCK) ON 
											CAST(PONTO_OPERACAO.PONTO_OPERACAO_ID AS VARCHAR) = LAYOUT_ARQUIVO.CNPJ_PONTO_EMB 
										LEFT JOIN CARGOSOL..PESSOA AS P_PO2 WITH (NOLOCK) ON 
											P_PO2.PESSOA_ID = PONTO_OPERACAO.PESSOA_ID
											AND P_PO2.TAB_STATUS_ID = 1
							
										WHERE DATEADD(DD,DATEDIFF(DD,0,FXCX.DATACOMPENSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
										AND FXCX.CODCXA = '00014'
										--AND MR.VIAGEM_ID IN (526351,526525,526434)
										AND FXCX.CODCOLIGADA = 1
										AND FXCX.COMPENSADO = 1
										AND LAYOUT_ARQUIVO.TIPO_TRANSACAO = 1
										AND LAYOUT_ARQUIVO.ID_PARCELA_CLIENTE = 0				
										AND LAYOUT_ARQUIVO.IDENTIFICACAO_PARCELA = 0
										AND NUMERO_TAG IS NULL
									)DADOS
							)DADOS1
					)DADOS2
				WHERE 1 = 1
				$condicao_lancamento_ecargo0
				$condicao_status0

				ORDER BY TIPO_TRANSACAO DESC, DOCUMENTO, DEBITO_BRADESCO";
				//print "<pre>$query</pre>";
				$result = $con->executar($query);
					
				$retorno = new stdClass();
				
				$total_adto_frota_pamcary = 0;
			
				while(odbc_fetch_row($result)) 
				{ 

					$data_bradesco 			= odbc_result($result, 1);
					$lancamento_bradesco 	= odbc_result($result, 2);
					$documento_bradesco		= odbc_result($result, 3);
					$nsu_bradesco 			= odbc_result($result, 4);
					$credito_bradesco		= odbc_result($result, 5);
					$debito_bradesco		= odbc_result($result, 6);
					$data_pamcary 			= odbc_result($result, 7);
					$nsu_pamcary 			= odbc_result($result, 8);
					$descricao_pamcary 		= odbc_result($result, 9);
					$valor_pamcary 			= odbc_result($result, 10);
					$data_ecargo 			= odbc_result($result, 11);
					$lancamento_ecargo 		= odbc_result($result, 12);
					$valor_ecargo 			= odbc_result($result, 13);
					$tipo_transacao			= odbc_result($result, 14);		
					$lancamento_id			= odbc_result($result, 15);		
					$id_sistema_cliente		= odbc_result($result, 16);									
					$ponto_operacao  		= odbc_result($result, 17);			
		
					/*
					if ($total_comprovante > 1)
					{
						$data_ecargo = '';
						$valor_ecargo = '';
						
						$query1 = "SELECT CONVERT(VARCHAR(10),DATA_EMISSAO,103), VAL_PEDAGIO
									FROM CARGOSOL..VALE_FRETE WITH (NOLOCK)
									WHERE NUM_COMPROVANTE_PEDAGIO = '$lancamento_ecargo'
									AND VALE_FRETE.TAB_STATUS_ID <> 2
									ORDER BY VALE_FRETE_ID";
						//print "<pre>$query1</pre>";
						$result1 = $con->executar($query1);
		
						while(odbc_fetch_row($result1)) 	
						{
							$data_ecargo1	= odbc_result($result1, 1);
							$valor_ecargo1	= odbc_result($result1, 2);
							
							$data_ecargo = $data_ecargo.'<p>'.$data_ecargo1;
							$valor_ecargo = $valor_ecargo.'<p>'.$valor_ecargo1;								
						}
						
					}
					*/
					
					if (($tipo_transacao == 2) && ($lancamento_ecargo == '') && ($lancamento_id == '') && ($credito_bradesco == '') && 
						(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES') || ($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))
						$cor = '#FF0000';
					else
						if (($nsu_pamcary == '') && ($nsu_bradesco != '') && ($lancamento_id == '') && ($credito_bradesco == '') && 
							(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES') || ($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))
							$cor = '#FF0000';
						else
							if (($debito_bradesco != '') && ($valor_pamcary != '') && ($valor_ecargo != '') &&
								(($debito_bradesco != $valor_pamcary) || ($debito_bradesco != $valor_ecargo) || 
								($valor_pamcary != $valor_ecargo)) && ($lancamento_id == ''))
								$cor = '#0000FF';
							else
							
								if (($nsu_bradesco != '') && ($nsu_pamcary != '') && 
									($nsu_bradesco == $nsu_pamcary) && ($debito_bradesco != $valor_pamcary))
									$cor = '#FF0000';
								else
									$cor = '#000000';
					

													
					$table .= "
						<tr>";
						
				if ($debito_bradesco != '')
				{
					$table .= "<td style='border:solid 1px #708090!important;'>
						<a href='javascript:lancamento_divergente(1,0,".chr(34).$nsu_bradesco.chr(34).",".chr(34).$data_bradesco.chr(34).",".chr(34).$debito_bradesco.chr(34).")'>
						<center><font size='-2' color='$cor'>".$data_bradesco."</font></a></center></td>";
				}
				else
				{
					$table .= "<td style='border:solid 1px #708090!important;'>
						<a href=javascript:baixa_credito(".chr(34).$documento_bradesco.chr(34).",".chr(34).$data_bradesco.chr(34).",".chr(34).$credito_bradesco.chr(34).")'>
						<center><font size='-2' color='$cor'>".$data_bradesco."</font></a></center></td>";					
				}					
					$table .= "	
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$lancamento_bradesco."</font></td>
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$documento_bradesco."</font></td>
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$nsu_bradesco."</font></td>
							<td style='border:solid 1px #708090!important;'>
								<font size='-2' color='$cor'>".number_format($credito_bradesco, 2, ',', '.')."</font></td>
							<td style='border:solid 1px #708090!important;'>
								<font size='-2' color='$cor'>".number_format($debito_bradesco, 2, ',', '.')."</font></td>

							<td><font size='-2' color='$cor'>&nbsp;&nbsp;</font></td>
							
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$data_pamcary."</font></td>
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$nsu_pamcary."</font></td>
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$ponto_operacao."</font></td>
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$descricao_pamcary."</font></td>
							<td style='border:solid 1px #708090!important;'>
								<font size='-2' color='$cor'>".number_format($valor_pamcary, 2, ',', '.')."</font></td>
						
							<td><font size='-2' color='$cor'>&nbsp;&nbsp;</font></td>
						
							<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$data_ecargo."</font></td>
							<td style='border:solid 1px #708090!important;'>
								<font size='-2' color='$cor'>".$lancamento_ecargo."</font></td>
							<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_ecargo, 2, ',', '.')."</font></td>
						</tr>";						
				
					$id_viagem_ant = $id_viagem;	
					
					$total_credito_bradesco = $total_credito_bradesco + $credito_bradesco;
					$total_debito_bradesco = $total_debito_bradesco + $debito_bradesco;		
					
					$total_valor_pamcary = $total_valor_pamcary + $valor_pamcary;	
					$total_valor_ecargo = $total_valor_ecargo + $valor_ecargo;			
					
					
					if ($descricao_pamcary == 'ADTO FROTA')
					{
						if ($lancamento_ecargo != '')
						{	
							$total_adto_frota_pamcary = $total_adto_frota_pamcary + $valor_pamcary;
							$total_adto_frota_ecargo = $total_adto_frota_ecargo + $valor_ecargo;
						}
						else
							$total_adto_frota_pamcary = $total_adto_frota_pamcary + $valor_pamcary;
					}
					else
						if ($descricao_pamcary == 'ADTO AGREG')
						{
							if ($lancamento_ecargo != '') 
							{
								$total_adto_agreg_pamcary = $total_adto_agreg_pamcary + $valor_pamcary;
								$total_adto_agreg_ecargo = $total_adto_agreg_ecargo + $valor_ecargo;
							}
							else
								$total_adto_agreg_pamcary = $total_adto_agreg_pamcary + $valor_pamcary;
						}
						else
							if ($descricao_pamcary == 'PEDAGIO')
							{
								$total_pedagio_pamcary 	= $total_pedagio_pamcary + $valor_pamcary;	
								$total_pedagio_bradesco = $total_pedagio_bradesco + $debito_bradesco;	
								$total_pedagio_ecargo	= $total_pedagio_ecargo + $valor_ecargo;	
							}
							else
								if ($descricao_pamcary == 'SALD AGREG')
									$total_saldo_bradesco = $total_saldo_bradesco + $debito_bradesco;
						
						
					if (($lancamento_id == '') && ($nsu_pamcary == '') && 
					(($lancamento_bradesco == 'CARGA CARTAO TRANSPORTES')||($lancamento_bradesco == 'TRANSPORT/PEDAGIO')))		
					{	
						$total_diferenca_bradesco = $total_diferenca_bradesco + $debito_bradesco;
						$total_diferenca_ecargo = $total_diferenca_ecargo + $debito_bradesco;
					}
		
					if (($lancamento_id == '') && ($nsu_pamcary != '') && ($lancamento_ecargo == '') && ($tipo_transacao == '2'))			
						$total_diferenca_ecargo = $total_diferenca_ecargo + $debito_bradesco;
						
					if (($debito_bradesco != '') && 
						(($lancamento_bradesco != 'CARGA CARTAO TRANSPORTES') && ($lancamento_bradesco != 'TRANSPORT/PEDAGIO')))
						$total_diversos_debitos = $total_diversos_debitos + $debito_bradesco;			
					
					if ($lancamento_id != '')
						$total_pendentes = $total_pendentes + $debito_bradesco;					
					
					
				}//fim while
				
			 	$total_diferenca_pamcary = $total_diferenca_bradesco;
				$total_saldo_pamcary 	 = abs($total_saldo_bradesco);
				$total_saldo_ecargo		 = abs($total_saldo_bradesco);	
				
				$table .= "<tr>			
							<td colspan='4' style='text-align: right;' ><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_credito_bradesco, 2, ',', '.')."</font></td>
							<td >
								<font size='-2'><b>".number_format($total_debito_bradesco, 2, ',', '.')."</font></td>
	
							<td><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='4' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_valor_pamcary, 2, ',', '.')."</font></td>
						
							<td><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td ><font size='-2'><b>".number_format($total_valor_ecargo, 2, ',', '.')."</font></td>
						  </tr>
						  
						   <tr>			
							<td colspan='16' style='border-right: none;'><font size='-2'>&nbsp;</font></td>
						   </tr>						  
					  
					  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Agregado:</font></td>
							<td colspan='2'  style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_agreg_pamcary, 2, ',', '.')."</font></td>

							<td><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Agreg:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_agreg_pamcary, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Agreg:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_agreg_ecargo, 2, ',', '.')."</font></td>
						  </tr>					  
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Frota:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_frota_pamcary, 2, ',', '.')."</font></td>

							<td><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Frota:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_frota_pamcary, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Adto Frota:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_adto_frota_ecargo, 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><b><font size='-2'>Total Pedágio:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_pedagio_bradesco, 2, ',', '.')."</font></td>

							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Pedágio:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_pedagio_pamcary, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2'style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Pedágio:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_pedagio_ecargo, 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Agregado:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_saldo_bradesco), 2, ',', '.')."</font></td>

							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Agregado:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_saldo_pamcary, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Agregado:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($total_saldo_ecargo, 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>Total Diversos Débitos:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_diversos_debitos), 2, ',', '.')."</font></td>

							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>Total Diversos Débitos:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_diversos_debitos), 2, ',', '.')."</font></td>
						
							<td <font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>Total Diversos Débitos:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_diversos_debitos), 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Pendentes:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_pendentes), 2, ',', '.')."</font></td>

							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Pendentes:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_pendentes), 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Total Pendentes:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format(abs($total_pendentes), 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>Diferença:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>".number_format($total_diferenca_bradesco, 2, ',', '.')."</font></td>

							<td><font size='-2'>&nbsp;&nbsp;</font></td>
							
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>Diferença:</font></td>
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>".number_format($total_diferenca_bradesco, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;&nbsp;</font></td>
						
							<td colspan='2' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='blue'><b>".number_format(abs($total_pamcary-$total_ecargo), 2, ',', '.')."</font></td>
						   </tbody></table>	
						  
						  <div class='col-xs-11'>&nbsp;</div> 
						   <div class='col-xs-1'>";
								$table1 .= "<img src='../../SCI/images/impressora.png' width='40' height='40' onClick='javascript:imprimi_div(1)'>";
						  $table1 .= "</div>
						  
						  ";		
						  
						  		 				  						  						  						  						  						  
			
			}
			else
			if ($aba == 1) //TAG
			{			  


				//consulta o saldo inicial
				$query1 = "SELECT 
							(select top 1 valor
							from saldo_pedagio with (nolock)
							where fornecedor_id = 4
							and data_conciliacao < '$data_final'
							and status_id = 'a'
							order by data_conciliacao desc) ultimo_deposito,
							(select top 1 usuario.nome+' EM '+CONVERT(VARCHAR(10), DATA_HORA_GRAVACAO, 103)+' '+CONVERT(VARCHAR(5), DATA_HORA_GRAVACAO, 108)
							from saldo_pedagio with (nolock)
							join usuario with (nolock) on
								usuario.id = saldo_pedagio.user_gravacao
							where fornecedor_id = 4
							and data_conciliacao = '$data_final'
							and status_id = 'a'
							order by saldo_pedagio.id desc)valor_conferido,
							(select top 1 replace(tarifa,'.',',') tarifa
							from saldo_pedagio with (nolock)
							where fornecedor_id = 4
							and data_conciliacao < '$data_final'
							and status_id = 'a'
							order by data_conciliacao desc) tarifa";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$saldo_deposito			= odbc_result($result1, 1);	
				$conferencia_efetuada	= odbc_result($result1, 2);		
				$tarifa_tag_diaria		= odbc_result($result1, 3);	

				$tarifa_tag  			= str_replace(',','.',$tarifa_tag_diaria);

				$table = "<br><br><br>
					<table class='table table-bordered table-hover' id='myTable3'>
						<thead>
							<th style='border:solid 1px #708090!important;' colspan='6' bgcolor='#d3d3d3'><b><center><font size='-2' color='blue'>PAMCARY</font></th>
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>				
							<th style='border:solid 1px #708090!important;' colspan='4' bgcolor='#d3d3d3'><b><center><font size='-2' color='blue'>E-CARGO</font></th>																
						</thead>
						<thead>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center>
								<font size='-2'>PROPRIETÁRIO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>COMPROVANTE</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>TARIFA</font></th>	
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DESCONTO</font></th>	
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
									
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>		
							
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center>
								<font size='-2'>PROPRIETÁRIO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>COMPROVANTE</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
									
															
						</thead><tbody>";	
				/*		
				$query = "
						SELECT *,
						CASE WHEN VALOR_TAG < VALOR_ECARGO
								THEN VALOR_ECARGO - VALOR_TAG
								ELSE 0
						END DESCONTO
						FROM (
								SELECT 
								SUBSTRING(cast(DATA_TRANSACAO as varchar), 7, 2)+'/'+SUBSTRING(cast(DATA_TRANSACAO as varchar), 5, 2)+'/'+SUBSTRING(cast(DATA_TRANSACAO as varchar), 1, 4) DATA_TAG,
								NOME_CONTRATADO PROPRIETARO,
								LAYOUT_ARQUIVO.ID_VIAGEM COMPROVANTE_TAG,
								CAST(SUBSTRING(cast(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_TAG,
								CONVERT(VARCHAR(10),VALE_FRETE.DATA_EMISSAO,103) DATA_ECARGO, 
								PESSOA.NOME PROPRIETARIO_ECARGO,
								VALE_FRETE.NUM_COMPROVANTE_PEDAGIO COMPROVANTE_ECARGO, 
								VALE_FRETE.VAL_PEDAGIO VALOR_ECARGO,
								LD.ID LANCAMENTO_ID,
								LAYOUT_ARQUIVO.ID_VIAGEM
								FROM LAYOUT_ARQUIVO WITH (NOLOCK)
								LEFT JOIN CARGOSOL..VALE_FRETE WITH (NOLOCK) ON	
									VALE_FRETE.NUM_COMPROVANTE_PEDAGIO = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)
									AND VALE_FRETE.TAB_STATUS_ID <> 2
								LEFT JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
									PESSOA.PESSOA_ID = VALE_FRETE.PROPRIETARIO_PF_ID
								LEFT JOIN lancamento_divergente ld with (nolock) on
									ld.nsu = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)
									and ld.status_id not in ('i')	
									and ld.fornecedor_id = 4						
								WHERE DATA_TRANSACAO BETWEEN REPLACE('$data_inicial','-','') AND REPLACE('$data_final','-','')
								AND NUMERO_TAG IS NOT NULL
							)DADOS	
							ORDER BY DATA_TAG, ID_VIAGEM";
				*/
				
				$query = "SELECT 
                                *,
        						CASE WHEN VALOR_TAG < VALOR_ECARGO
        								THEN VALOR_ECARGO - VALOR_TAG
        								ELSE 0
        						END DESCONTO
    						FROM (
                                SELECT DISTINCT 
                                	SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 7, 2)+'/'+SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 5, 2)+'/'+SUBSTRING(CAST(DATA_TRANSACAO AS VARCHAR), 1, 4) DATA_TAG,
                                	NOME_CONTRATADO PROPRIETARO,
                                	LAYOUT_ARQUIVO.ID_VIAGEM COMPROVANTE_TAG,
                                	CAST(SUBSTRING(CAST(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-2)+'.'+SUBSTRING(CAST(VALOR_TRANSACAO AS VARCHAR), LEN(CAST(VALOR_TRANSACAO AS VARCHAR))-1, 2)AS NUMERIC(15,2)) VALOR_TAG,
                                	ISNULL((select top 1 format(VALE_FRETE_AUX.DATA_EMISSAO,'dd/MM/yyyy') 
										FROM CARGOSOL..VALE_FRETE VALE_FRETE_AUX WITH (NOLOCK) 
										WHERE VALE_FRETE_AUX.NUM_COMPROVANTE_PEDAGIO = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR) 
										AND VALE_FRETE_AUX.DATA_EMISSAO IS NOT NULL AND VALE_FRETE_AUX.TAB_STATUS_ID <> 2), CONVERT(VARCHAR(10), PEDAGIO_AVULSO.DATA_INCLUSAO,103)) DATA_ECARGO,
									--ISNULL(CONVERT(VARCHAR(10),VALE_FRETE.DATA_EMISSAO,103), CONVERT(VARCHAR(10), PEDAGIO_AVULSO.DATA_INCLUSAO,103)) DATA_ECARGO, 
                                	ISNULL(PESSOA.NOME, DATA.NOME) PROPRIETARIO_ECARGO,
                                	ISNULL(VALE_FRETE.NUM_COMPROVANTE_PEDAGIO,PEDAGIO_AVULSO.PROTOCOLO) COMPROVANTE_ECARGO, 
                                	ISNULL(VALE_FRETE.VAL_PEDAGIO,PEDAGIO_AVULSO.VALOR_CARREGADO) VALOR_ECARGO,
                                	LD.ID LANCAMENTO_ID,
                                	ISNULL(LAYOUT_ARQUIVO.ID_VIAGEM, PEDAGIO_AVULSO.PROTOCOLO) ID_VIAGEM
                                                            
                                FROM LAYOUT_ARQUIVO WITH (NOLOCK)
                                
                                LEFT JOIN CARGOSOL..VALE_FRETE WITH (NOLOCK) ON	
                                	VALE_FRETE.NUM_COMPROVANTE_PEDAGIO = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)
                                	AND VALE_FRETE.TAB_STATUS_ID <> 2
                                
                                LEFT JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
                                	PESSOA.PESSOA_ID = VALE_FRETE.PROPRIETARIO_PF_ID
                                
                                LEFT JOIN CARGOSOL..VEICULO_FORNECEDOR WITH(NOLOCK)
                                ON VEICULO_FORNECEDOR.VEICULO_FORNECEDOR_ID = VALE_FRETE.VEICULO_FORNECEDOR_ID
                                
                                OUTER APPLY (
                                	SELECT
                                		VEICULO_FORNECEDOR_ID,
                                		PLACA,
                                		PESSOA_FORNECEDOR.NOME
                                	FROM CARGOSOL..VEICULO_FORNECEDOR AS VEICULO_FORNECEDOR2 WITH(NOLOCK)
                                	JOIN CARGOSOL..FORNECEDOR WITH(NOLOCK)
                                	ON FORNECEDOR.FORNECEDOR_ID = VEICULO_FORNECEDOR2.FORNECEDOR_ID	
                                	JOIN CARGOSOL..PESSOA AS PESSOA_FORNECEDOR WITH(NOLOCK)
                                	ON PESSOA_FORNECEDOR.PESSOA_ID = FORNECEDOR.PESSOA_ID
                                	WHERE VEICULO_FORNECEDOR2.PLACA = LAYOUT_ARQUIVO.PLACA_VEICULO COLLATE SQL_LATIN1_GENERAL_CP1_CI_AS
                                )DATA
                                
                                LEFT JOIN LANCAMENTO_DIVERGENTE LD WITH (NOLOCK) ON
                                	LD.NSU = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)
                                	AND LD.STATUS_ID NOT IN ('I')	
                                	AND LD.FORNECEDOR_ID = 4	
                                
                                OUTER APPLY(                            
                                	SELECT 
                                	* 
                                	FROM CARGOSOL..VALE_PEDAGIO_INTEGRADO WITH(NOLOCK) 
                                	WHERE PROTOCOLO = CAST(LAYOUT_ARQUIVO.ID_VIAGEM AS VARCHAR)                            
                                ) PEDAGIO_AVULSO
                                                            						
                                WHERE DATA_TRANSACAO BETWEEN REPLACE('$data_inicial','-','') 
                                    AND REPLACE('$data_final','-','')
                                    AND NUMERO_TAG IS NOT NULL
                            )DADOS	
							ORDER BY DATA_TAG, ID_VIAGEM";
				
				
				//print "<pre>$query</pre>";
				$result = $con->executar($query);
					
				$retorno = new stdClass();

			
				while(odbc_fetch_row($result)) 
				{ 

					$data_tag	 			= odbc_result($result, 1);
					$proprietario_tag 		= odbc_result($result, 2);
					$lancamento_tag 		= odbc_result($result, 3);
					$valor_tag 				= odbc_result($result, 4);
					$data_ecargo 			= odbc_result($result, 5);
					$proprietario_ecargo	= odbc_result($result, 6);
					$lancamento_ecargo 		= odbc_result($result, 7);
					$valor_ecargo 			= odbc_result($result, 8);
					$lancamento_id			= odbc_result($result, 9);	
					$desconto_valor_tag 	= odbc_result($result, 11);	


					$valor_tarifa = ($valor_tag  * $tarifa_tag) / 100;


					if (((($valor_tag != '') && ($valor_ecargo == '')) || (($valor_tag == '') && ($valor_ecargo != ''))) && 
						($lancamento_id == ''))
						$cor = '#FF0000';
					else
						if (($valor_tag != $valor_ecargo) && ($lancamento_id == ''))
							$cor = '#0000FF';
						else
							$cor = '#000000';	

					
					$table .= "<tr>			
								<td style='border:solid 1px #708090!important;'>
								<a href='javascript:lancamento_divergente(4,0,".chr(34).$lancamento_tag.chr(34).",".chr(34).$data_tag.chr(34).",".chr(34).$valor_tag.chr(34).")'><font size='-2' color='$cor'>".$data_tag."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$proprietario_tag."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$lancamento_tag."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_tarifa, 2, ',', '.')."</font></td>	
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($desconto_valor_tag, 2, ',', '.')."</font></td>	
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_tag, 2, ',', '.')."</font></td>
							
								<td><font size='-2' color='$cor'>&nbsp;</font></td>
							
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$data_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$proprietario_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$lancamento_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_ecargo, 2, ',', '.')."</font></td>
							  </tr>";	
							  
					$total_tag_tag 	=  $total_tag_tag + $valor_tag;			
					$total_tag_ecargo 	=  $total_tag_ecargo + 	$valor_ecargo;		
					
					$total_tarifas_tag = $total_tarifas_tag + $valor_tarifa;
					$total_descontos_tag = $total_descontos_tag + $desconto_valor_tag;		

				}//fim while
				
				$diferenca_tag 		= $total_tag_tag - $total_tag_ecargo;
				$diferenca_ecargo 	= $total_tag_ecargo - $total_tag_tag;
				
				
				//consulta se houve um deposito diário TAG
				$query1 = "SELECT SUM(VALOR)
							FROM CORPORE..FXCX WITH (NOLOCK)
							WHERE CODCXA = '20505'
							AND TIPO = 4
							AND DATA BETWEEN '$data_inicial' AND '$data_final'";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$deposito_diario	= odbc_result($result1, 1);									
				
				$saldo_disponivel = $saldo_deposito + $deposito_diario - $total_tag_tag - $total_tarifas_tag;
			
				$table .= "<tr>			
							<td colspan='5' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_tag_tag, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_tag_ecargo, 2, ',', '.')."</font></td>
						  </tr>
						  
						   <tr>			
							<td colspan='11' style='border-right: none;'><font size='-2'>&nbsp;</font></td>
						   </tr>
						   
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_tag, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_ecargo, 2, ',', '.')."</font></td>
						   </tr>
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Inicial:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_deposito, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3'><font size='-2'><b>&nbsp;</font></td>
						   </tr>	
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Depósito:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($deposito_diario, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>	
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Utilizado:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>-".number_format($total_tag_tag, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' >
								<font size='-2'><b>&nbsp;</font></td>
						   </tr>
						   
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'>
							<div style='display:flex;flex-direction:row-reverse'>
								<div style='font-size:15px'>%</div>
								<div style='width:8%'>
									<input type='text' class='form-control txt-center altura-campo only-number_virgula' aria-describedby='basic-addon1' name='tarifa_tag' id='tarifa_tag' value='$tarifa_tag_diaria'  maxlength='5' onBlur='javascript:grava_tarifa(4,".chr(34)."$data_inicial".chr(34).",this.value)'>
								</div>
								
							</div>
							</td>
							<td style='border:solid 0px #708090!important;text-align: right;'>
								<font size='-2'><b><b>Tarifas:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>-".number_format($total_tarifas_tag, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' >
								<font size='-2'><b>&nbsp;</font></td>
						   </tr>";	
						   /*
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Descontos:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>-".number_format($total_descontos_tag, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' >
								<font size='-2'><b>&nbsp;</font></td>
						   </tr>						   
						  */
				$table .= "
						   <tr>			
							<td colspan='5' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Disponível:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_disponivel, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							
							<td colspan='3' ><font size='-2' color='green'><b>&nbsp;</font></td>
						   </tr>
						   
						   </tbody></table>	

						   <div class='col-xs-11'>";
								$table1 .= "<img src='../../SCI/images/impressora.png' width='40' height='40' onClick='javascript:imprimi_div(4)'>";
						  $table1 .= "</div>
						  <div class='col-xs-1'>";
						   							   
							if ($conferencia_efetuada != '')
								$conf = "<font size='-1' color='green'><b> CONFERÊNCIA EFETUADA POR ".$conferencia_efetuada."</b></font>";					
							else
								$conf = "";
				
								$table .= "<left><input type='button' class='btn-sm btn-primary' value='Conferir Valores' id='gravar' name='gravar' onClick='javascript:confere_saldo_pedagio(4,".chr(34).$data_final.chr(34).",".chr(34).$saldo_disponivel.chr(34).")'>".$conf;	
						  $table1 .= "</div>";							   

			}	
			else
			if ($aba == 2) //AILOG
			{			  
				$table = "<br><br><br>
					<table class='table table-bordered table-hover' id='myTable3' >
						<thead>
							<th style='border:solid 1px #708090!important;' colspan='5' bgcolor='#d3d3d3'><b><center>
								<font size='-2' color='blue'>AILOG</font></th>
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>				
							<th style='border:solid 1px #708090!important;' colspan='4' bgcolor='#d3d3d3'><b><center>
								<font size='-2' color='blue'>E-CARGO</font></th>																
						</thead>
						<thead>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center>
								<font size='-2'>MOTORISTA</font></th>						

							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center>
								<font size='-2'>DOCUMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
							
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>		
							
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>MOTORISTA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
						</thead><tbody>";	
						
				$query = "
						SELECT 
						CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_AILOG,
						CASE WHEN DADOS.DATA_TRANSACAO IS NOT NULL
								THEN 'ADIANTAMENTO VIAGEM'
								ELSE NULL
						END DESCRICAO,
						CASE WHEN DESCRICAO LIKE '%(%'
							THEN LTRIM(SUBSTRING(DESCRICAO,PATINDEX('%-%',descricao)+1,PATINDEX('%(%',descricao)-PATINDEX('%-%',descricao)-1))
							ELSE NULL
						END MOTORISTA,
						TAG DOCUMENTO,
						CASE WHEN FXCX.VALOR < 0
								THEN ABS(DADOS.VALOR)
								ELSE DADOS.VALOR
						END VALOR_AILOG,
						CONVERT(VARCHAR(10), FXCX.DATA, 103) DATA,
						PFUNC.NOME MOTORISTA,
						FXCX.NUMERODOCUMENTO,
						ABS(FXCX.VALOR) VALOR_ECARGO,
						DADOS.ID
							FROM CORPORE..FXCX FXCX WITH (NOLOCK) 
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.CAMPOALFAOP2 = FXCX.NUMERODOCUMENTO
								AND FLAN.CODCOLIGADA = 1
							LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
								PFUNC.CHAPA = FLAN.CHAPA							
							LEFT JOIN DADOS_CONCILIACAO_AILOG DADOS WITH (NOLOCK) ON
								DADOS.TAG = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
								AND DADOS.STATUS_ID IN ('a')
								AND DESCRICAO NOT LIKE '%SALDO%'
							WHERE FXCX.CODCXA = '20504'
							AND FXCX.CODCOLIGADA = 1
							AND FXCX.COMPENSADO = 1	
							AND FXCX.TIPO = 6
							AND FXCX.DATA BETWEEN '$data_inicial' AND '$data_final'
							$condicao_status2

							UNION
							
							SELECT 
							CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_BPP,
							REPLACE(UPPER(DESCRICAO),'TRANSFER?NCIA','TRANSFERENCIA') DESCRICAO,
							CASE WHEN DESCRICAO LIKE '%(%'
								THEN LTRIM(SUBSTRING(DESCRICAO,PATINDEX('%-%',descricao)+1,PATINDEX('%(%',descricao)-PATINDEX('%-%',descricao)-1))
								ELSE NULL
							END MOTORISTA,
							TAG DOCUMENTO,
							DADOS.VALOR*(-1) VALOR_AILOG,
							NULL,
							NULL,
							NULL,
							NULL,
							DADOS.ID
							FROM DADOS_CONCILIACAO_AILOG DADOS WITH (NOLOCK)
							WHERE DATEADD(DD,DATEDIFF(DD,0,DADOS.DATA_TRANSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
							AND DESCRICAO NOT LIKE '%SALDO%'
							AND DESCRICAO NOT LIKE '%TRANSFERENCIA - EMPRESA DE TRANSPORTES COVRE%'
							AND DESCRICAO NOT LIKE '%TRANSFER?NCIA - EMPRESA DE TRANSPORTES COVRE%'
							AND DADOS.STATUS_ID IN ('a')
							AND DADOS.TAG NOT IN (SELECT FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
														FROM CORPORE..FXCX FXCX WITH (NOLOCK) 
														WHERE FXCX.CODCXA = '20504'
														AND FXCX.CODCOLIGADA = 1
														AND FXCX.COMPENSADO = 1	
														AND FXCX.TIPO = 6
														AND FXCX.DATA BETWEEN '$data_inicial' AND '$data_final'
														AND FXCX.NUMERODOCUMENTO = DADOS.TAG COLLATE SQL_Latin1_General_CP1_CI_AI)
							$condicao_lancamento_ecargo2														
							ORDER BY DATA_AILOG DESC, FXCX.NUMERODOCUMENTO";
				//print "<pre>$query</pre>";
				$result = $con->executar($query);
					
				$retorno = new stdClass();
				$lancamento_ecargo_anterior = '';
			
				while(odbc_fetch_row($result)) 
				{ 

					$data_ailog	 			= odbc_result($result, 1);
					$descricao_ailog	 	= odbc_result($result, 2);
					$motorista_ailog 		= odbc_result($result, 3);
					$documento_ailog 		= odbc_result($result, 4);
					$valor_ailog			= odbc_result($result, 5);
					$data_ecargo 			= odbc_result($result, 6);
					$motorista_ecargo 		= odbc_result($result, 7);
					$lancamento_ecargo 		= odbc_result($result, 8);
					$valor_ecargo 			= odbc_result($result, 9);
					$id_ailog	 			= odbc_result($result, 10);

					$descricao_ailog = utf8_encode($descricao_ailog);

					if ($lancamento_ecargo_anterior == $lancamento_ecargo)
						$valor_ecargo = 0;


					if ((($valor_ailog != '') && ($valor_ecargo == '')) || (($valor_ailog == '') && ($valor_ecargo != '')) 
						|| ($lancamento_ecargo_anterior == $lancamento_ecargo))
						$cor = '#FF0000';
					else
						if ($valor_ailog != $valor_ecargo)
							$cor = '#0000FF';
						else
							$cor = '#000000';	
					
					$table .= "<tr>	
								<td style='border:solid 1px #708090!important;'><a href='javascript:lancamento_divergente(2,$id_ailog,".chr(34).$documento_ailog.chr(34).",".chr(34).$data_ailog.chr(34).",".chr(34).$valor_ailog.chr(34).")'><font size='-2' color='$cor'>".$data_ailog."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$descricao_ailog."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$motorista_ailog."</font></td>

								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$documento_ailog."</font></td>								
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_ailog, 2, ',', '.')."</font></td>
							
								<td><font size='-2' color='$cor'>&nbsp;</font></td>
							
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$data_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$motorista_ecargo."</font></td>

								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$lancamento_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_ecargo, 2, ',', '.')."</font></td>
							  </tr>";	
							  
					$total_tag_ailog 	=  $total_tag_ailog + $valor_ailog;			
					$total_tag_ecargo 	=  $total_tag_ecargo + 	$valor_ecargo;
					
					$lancamento_ecargo_anterior = $lancamento_ecargo;									
					
				}//fim while	
				
                                /*$query_estorno = "SELECT
                                                    SUM(ABS(FXCX.VALOR))
                                                    FROM CORPORE..FXCX
                                                    JOIN CORPORE..CPARTIDA
                                                    ON FXCX.CODCOLIGADA =CPARTIDA.CODCOLIGADA
                                                    WHERE 
                                                    FXCX.CODCXA = '20504'
                                                    AND FXCX.CODTB2FLX = '3070'
                                                    AND FXCX.NUMERODOCUMENTO = CPARTIDA.DOCUMENTO
                                                    AND CPARTIDA.DEBITO = '1.1.1.1.009'
                                                    AND FXCX.DATA BETWEEN '$data_inicial' AND '$data_final'";
				//print "<pre>$query1</pre>";
				$result_estorno = $con->executar($query_estorno);
				$total_estorno	= odbc_result($result_estorno, 1);*/
                                
				$diferenca_ailog 	= $total_tag_ailog - $total_tag_ecargo;
				$diferenca_ecargo 	= $total_tag_ecargo - $total_tag_ailog;
				
				//consulta o saldo inicial
				$query1 = "SELECT 
							(select top 1 valor
							from saldo_pedagio with (nolock)
							where fornecedor_id = 2
							and data_conciliacao < '$data_final'
							and status_id = 'a'
							order by data_conciliacao desc) ultimo_deposito,
							(select top 1 usuario.nome+' EM '+CONVERT(VARCHAR(10), DATA_HORA_GRAVACAO, 103)+' '+CONVERT(VARCHAR(5), DATA_HORA_GRAVACAO, 108)
							from saldo_pedagio with (nolock)
							join usuario with (nolock) on
								usuario.id = saldo_pedagio.user_gravacao
							where fornecedor_id = 2
							and data_conciliacao = '$data_final'
							and status_id = 'a'
							order by saldo_pedagio.id desc)valor_conferido";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$saldo_deposito			= odbc_result($result1, 1);	
				$conferencia_efetuada	= odbc_result($result1, 2);	
				
				
				$query1 = "SELECT SUM(VALOR)
							FROM CORPORE..FXCX WITH (NOLOCK)
							WHERE CODCXA = '20504'
							AND TIPO = 4
							AND DATA BETWEEN '$data_inicial' AND '$data_final'";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$deposito_diario	= odbc_result($result1, 1);		
				
				
				$query1 = "SELECT SUM(ABS(VALOR))
							FROM CORPORE..FXCX WITH (NOLOCK)
							WHERE CODCXA = '20504'
							AND TIPO = 1
							AND DATA BETWEEN '$data_inicial' AND '$data_final'";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$tarifa_bancaria	= odbc_result($result1, 1);						
							
				                               
				$saldo_disponivel = $saldo_deposito + $deposito_diario - $tarifa_bancaria - $total_tag_ailog;			

				
				$table .= "<tr>			
							<td colspan='4' style='text-align: right;''><font size='-2'><b>TOTAL:</font></td>
							<td style='border-right: none;'><font size='-2'><b>".number_format($total_tag_ailog, 2, ',', '.')."</font></td>
						
							<td style='border-bottom: none;border-top:none;'><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='text-align: right;''><font size='-2'><b>TOTAL:</font></td>
							<td style='border-right: none;'><font size='-2'><b>".number_format($total_tag_ecargo, 2, ',', '.')."</font></td>
						  </tr>		
						   <tr style='border-top: none;'>			
							<td colspan='16' style='border-right: none;'><font size='-2'>&nbsp;</font></td>
						   </tr>							  
						   <tr>			
							<td colspan='4'style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_ailog, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_ecargo, 2, ',', '.')."</font></td>
						   </tr>

						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;none;none;text-align: right;'><font size='-2'><b>Saldo Inicial:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_deposito, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>	
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;none;none;text-align: right;'><font size='-2'><b>Depósito:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2'><b>".number_format($deposito_diario, 2, ',', '.')."</font></td>
						
							<td style='border-bottom: none;border-top:none;'><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>
                                                                                                       					   
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;none;none;text-align: right;'><font size='-2'><b>Tarifa Bancária:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2'><b>-".number_format($tarifa_bancaria, 2, ',', '.')."</font></td>
						
							<td style='border-bottom: none;border-top:none;'><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>						   
						   							   
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;none;none;text-align: right;'><font size='-2'><b>Saldo Utilizado</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2'><b>-".number_format($total_tag_ailog, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>							   
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;none;none;text-align: right;'><font size='-2'><b>Saldo Disponível:</font></td>
							<td style='border:solid 1px #708090!important;none;none;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_disponivel, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' ><font size='-2'><b>&nbsp;</font></td>
						   </tr>
						   
						   </tbody></table>	

						   <div class='col-xs-11'>";
								$table1 .= "<img src='../../SCI/images/impressora.png' width='40' height='40' onClick='javascript:imprimi_div(2)'>";
						  $table1 .= "</div>
						  <div class='col-xs-1'>";
						   					
						   
							if ($conferencia_efetuada != '')
								$conf = "<font size='-1' color='green'><b> CONFERÊNCIA EFETUADA POR ".$conferencia_efetuada."</b></font>";					
							else
								$conf = "";
				
								$table .= "<left><input type='button' class='btn-sm btn-primary' value='Conferir Valores' id='gravar' name='gravar' onClick='javascript:confere_saldo_pedagio(2,".chr(34).$data_final.chr(34).",".chr(34).$saldo_disponivel.chr(34).")'>".$conf;	
						  $table1 .= "</div>";					   						  
					  		
						
			}	
			else
			if ($aba == 3) //BRASIL PRÉ-PAGO
			{			  
				
				$table = "<br><br><br>
					<table class='table table-bordered table-hover' id='myTable3'>
						<thead>
							<th style='border:solid 1px #708090!important;' colspan='5' bgcolor='#d3d3d3'><b><center><font size='-2' color='blue'>BRASIL PRÉ-PAGO</font></th>
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>				
							<th style='border:solid 1px #708090!important;' colspan='4' bgcolor='#d3d3d3'><b><center><font size='-2' color='blue'>E-CARGO</font></th>																
						</thead>
						<thead>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>MOTORISTA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DOCUMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
							
							<th style='border: none!important;'><b><center><font size='-2'>&nbsp;&nbsp;</font></th>		
							
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>MOTORISTA</font>
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>LANÇAMENTO</font></th>						
							<th style='border:solid 1px #708090!important;' bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
						</thead><tbody>";	
						
				$query = "
							SELECT 
							CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_BPP,
							REPLACE(UPPER(ACAO),'CART?O','CARTAO') ACAO,
							DADOS.MOTORISTA,
							ASSUNTO DOCUMENTO,
							CASE WHEN ((ACAO LIKE '%DESCARGA CART%') OR (ACAO LIKE '%Carga Entidade%'))
									THEN DADOS.VALOR *-1
									ELSE DADOS.VALOR 
							END VALOR_BPP,
							CONVERT(VARCHAR(10), FXCX.DATA, 103) DATA,
							PFUNC.NOME MOTORISTA,
							FXCX.NUMERODOCUMENTO,
							CASE WHEN ((ACAO LIKE '%DESCARGA CART%') OR (ACAO LIKE '%Carga Entidade%'))
									THEN FXCX.VALOR
									ELSE ABS(FXCX.VALOR)
							END VALOR_BPP,
							DADOS.ID
							FROM CORPORE..FXCX FXCX WITH (NOLOCK) 
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.CAMPOALFAOP2 = FXCX.NUMERODOCUMENTO
								AND FLAN.CODCOLIGADA = 1
							LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
								PFUNC.CHAPA = FLAN.CHAPA							
							LEFT JOIN DADOS_CONCILIACAO_BPP DADOS WITH (NOLOCK) ON
								DADOS.ASSUNTO = FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
								AND DADOS.STATUS_ID IN ('a')
							WHERE FXCX.CODCXA = '20500'
							AND FXCX.CODCOLIGADA = 1
							AND FXCX.COMPENSADO = 1	
							AND FXCX.TIPO = 6
							AND FXCX.DATA BETWEEN '$data_inicial' AND '$data_final'
							$condicao_status3													
							
							UNION
							
							SELECT 
							CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_BPP,
							REPLACE(UPPER(ACAO),'CART?O','CARTAO') ACAO,
							UPPER(DADOS.MOTORISTA) MOTORISTA,
							ASSUNTO DOCUMENTO,
							CASE WHEN ((ACAO LIKE '%DESCARGA CART%') OR (ACAO LIKE 'Carga Entidade%'))
									THEN DADOS.VALOR *-1
									ELSE DADOS.VALOR 
							END VALOR_BPP,
							NULL,
							NULL,
							NULL,
							NULL,
							DADOS.ID
							FROM DADOS_CONCILIACAO_BPP DADOS WITH (NOLOCK)
							WHERE DATEADD(DD,DATEDIFF(DD,0,DADOS.DATA_TRANSACAO),0) BETWEEN '$data_inicial' AND '$data_final'
							AND ((ACAO NOT LIKE 'CARGA ENTIDADE%') OR ((ACAO LIKE 'CARGA ENTIDADE%') AND (ASSUNTO LIKE '%ESTORNO%')))
							AND DADOS.STATUS_ID IN ('a')
							AND DADOS.ASSUNTO NOT IN (SELECT 
														FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS
														FROM CORPORE..FXCX FXCX WITH (NOLOCK) 
														WHERE FXCX.CODCXA = '20500'
														AND FXCX.CODCOLIGADA = 1
														AND FXCX.COMPENSADO = 1	
														AND FXCX.TIPO = 6
														AND FXCX.DATA BETWEEN '$data_inicial' AND '$data_final'
														AND FXCX.NUMERODOCUMENTO = DADOS.ASSUNTO COLLATE SQL_Latin1_General_CP1_CI_AI)
							$condicao_lancamento_ecargo3
							ORDER BY DATA_BPP DESC, FXCX.NUMERODOCUMENTO";
				//print "<pre>$query</pre>";
				$result = $con->executar($query);
					
				$retorno = new stdClass();
				$lancamento_ecargo_anterior = '';
			
				while(odbc_fetch_row($result)) 
				{ 

					$data_bpp	 			= odbc_result($result, 1);
					$acao_bpp				= odbc_result($result, 2);
					$motorista_bpp		 	= odbc_result($result, 3);
					$documento_bpp 			= odbc_result($result, 4);
					$valor_bpp				= odbc_result($result, 5);
					$data_ecargo 			= odbc_result($result, 6);
					$motorista_ecargo	 	= odbc_result($result, 7);
					$lancamento_ecargo 		= odbc_result($result, 8);
					$valor_ecargo 			= odbc_result($result, 9);
					$id_bpp		 			= odbc_result($result, 10);

					$acao_bpp = utf8_encode($acao_bpp);
					$documento_bpp = utf8_encode($documento_bpp);
					
					if ($lancamento_ecargo_anterior == $lancamento_ecargo)
						$valor_ecargo = 0;					
					
					if ((($valor_bpp != '') && ($valor_ecargo == '')) || (($valor_bpp == '') && ($valor_ecargo != '')) ||
					($acao_bpp == 'DESCARGA CARTAO') || ($lancamento_ecargo_anterior == $lancamento_ecargo))
						$cor = '#FF0000';
					else
						if ($valor_bpp != $valor_ecargo)
							$cor = '#0000FF';
						else
							$cor = '#000000';					
					
					
					$table .= "<tr>			
								<td style='border:solid 1px #708090!important;'>
								<a href='javascript:lancamento_divergente(3,$id_bpp,".chr(34).$documento_bpp.chr(34).",".chr(34).$data_bpp.chr(34).",".chr(34).$valor_bpp.chr(34).")'>
								<font size='-2' color='$cor'>".$data_bpp."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$acao_bpp."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$motorista_bpp."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$documento_bpp."</font></td>								
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_bpp, 2, ',', '.')."</font></td>
							
								<td><font size='-2'>&nbsp;</font></td>
							
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$data_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".$motorista_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'><font size='-2' color='$cor'>".$lancamento_ecargo."</font></td>
								<td style='border:solid 1px #708090!important;'>
									<font size='-2' color='$cor'>".number_format($valor_ecargo, 2, ',', '.')."</font></td>
							  </tr>";	
							  
					$total_bpp 			=  $total_bpp + $valor_bpp;			
					$total_bpp_ecargo 	=  $total_bpp_ecargo + 	$valor_ecargo;	
					
					//if ($acao_bpp == 'CARGA CARTÃO')	
					//{
						$total_carga_bpp = $total_carga_bpp + $valor_bpp;	
						$total_carga_ecargo = $total_carga_ecargo + $valor_ecargo;
					//}
					//else
						//$total_diversos_bpp = $total_diversos_bpp + $valor_bpp;
				
					$lancamento_ecargo_anterior = $lancamento_ecargo;		

				}//fim while	
									
				$diferenca_bpp 		= $total_carga_bpp - $total_carga_ecargo;								
				$diferenca_ecargo 	= $total_carga_ecargo - $total_carga_bpp;

				//consulta o saldo inicial
				$query1 = "SELECT 
							(select top 1 valor
							from saldo_pedagio with (nolock)
							where fornecedor_id = 3
							and data_conciliacao < '$data_final'
							and status_id = 'a'
							order by data_conciliacao desc) ultimo_deposito,
							(select top 1 usuario.nome+' EM '+CONVERT(VARCHAR(10), DATA_HORA_GRAVACAO, 103)+' '+CONVERT(VARCHAR(5), DATA_HORA_GRAVACAO, 108)
							from saldo_pedagio with (nolock)
							join usuario with (nolock) on
								usuario.id = saldo_pedagio.user_gravacao
							where fornecedor_id = 3
							and data_conciliacao = '$data_final'
							and status_id = 'a'
							order by saldo_pedagio.id desc)valor_conferido";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$saldo_deposito			= odbc_result($result1, 1);	
				$conferencia_efetuada	= odbc_result($result1, 2);	

				//consulta se houve um deposito diário BPP
				$query1 = "SELECT SUM(VALOR)
							FROM CORPORE..FXCX WITH (NOLOCK)
							WHERE CODCXA = '20500'
							AND TIPO = 4
							AND DATA BETWEEN '$data_inicial' AND '$data_final'";
				//print "<pre>$query1</pre>";
				$result1 = $con->executar($query1);
				$deposito_diario	= odbc_result($result1, 1);	
				
				
				$saldo_disponivel = $saldo_deposito + $deposito_diario - $total_carga_bpp;	

				
				$table .= "<tr>			
							<td colspan='4' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_bpp, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='text-align: right;'><font size='-2'><b>TOTAL:</font></td>
							<td >
								<font size='-2'><b>".number_format($total_bpp_ecargo, 2, ',', '.')."</font></td>
						  </tr>		
						  
						   <tr >			
							<td colspan='16'><font size='-2'>&nbsp;</font></td>
						   </tr>		
						   
						  <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_bpp, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='3' style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>Diferença:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2' color='#0000FF'><b>".number_format($diferenca_ecargo, 2, ',', '.')."</font></td>
						  </tr>	
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Inicial:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_deposito, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='4' style='border-right: none;text-align: right;'><font size='-2'><b>&nbsp;</font></td>
						   </tr>	
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Depósito:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($deposito_diario, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='4'><font size='-2'><b>&nbsp;</font></td>
						   </tr>								   
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Utilizado</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>-".number_format($total_carga_bpp, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='4' style='border-right: none;text-align: right;'><font size='-2'><b>&nbsp;</font></td>
						   </tr>							   
						  
						   <tr>			
							<td colspan='4' style='border:solid 1px #708090!important;text-align: right;'><font size='-2'><b>Saldo Disponível:</font></td>
							<td style='border:solid 1px #708090!important;text-align: right;'>
								<font size='-2'><b>".number_format($saldo_disponivel, 2, ',', '.')."</font></td>
						
							<td ><font size='-2'>&nbsp;</font></td>
						
							<td colspan='4' ><font size='-2'>&nbsp;
							</font></td>
						   </tr>
						   
						   </tbody></table>	

						   <div class='col-xs-11'>";
								$table1 .= "<img src='../../SCI/images/impressora.png' width='40' height='40' onClick='javascript:imprimi_div(3)'>";
						  $table1 .= "</div>
						  <div class='col-xs-1'>";
						   							   
						   
							if ($conferencia_efetuada != '')
								$conf = "<font size='-1' color='green'><b> CONFERÊNCIA EFETUADA POR ".$conferencia_efetuada."</b></font>";					
							else
								$conf = "";
				
								$table .= "<left><input type='button' class='btn-sm btn-primary' value='Conferir Valores' id='gravar' name='gravar' onClick='javascript:confere_saldo_pedagio(3,".chr(34).$data_final.chr(34).",".chr(34).$saldo_disponivel.chr(34).")'>".$conf;	
						  $table1 .= "</div>";							   
						  					
			}								

			
			//$table .= "</tbody></table>";

			//$table .= "<script>order()</script/>";
				

		
			$retorno->relatorio = $table."|".$table1;
			
		}

		return $retorno;	
	
	}
	
	
	public function campoLancamentoDivergente($fornecedor_id, $id, $nsu, $data, $valor){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = new stdClass();


	//convertendo a data
	$data_bradesco = 
	implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
	array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data))); 
	
	if ($tipo != 2)
	{
		if ($fornecedor_id == 1)
		{
			$query = "select distinct 
						convert(varchar(10), FXCX.data, 103),
						FXCX.NUMERODOCUMENTO,
						ISNULL(FRECONCIL.HISTORICO, FXCX.HISTORICO),
						cast(ABS(FXCX.valor) as numeric(15,2)),
						ld.id,
						ld.observacao,
						ld.status_id
						from Corpore..FXCX with (nolock)
						left join lancamento_divergente ld with (nolock) on
							ld.nsu = FXCX.NUMERODOCUMENTO collate Latin1_General_CI_AS
							and ld.fornecedor_id = 1
							and ld.status_id not in ('i')
						LEFT JOIN CORPORE..FRECONCILRELAC FRECONCILRELAC WITH(NOLOCK) ON
							FRECONCILRELAC.IDXCX = FXCX.IDXCX
						AND FRECONCILRELAC.CODCOLIGADA = FXCX.CODCOLIGADA
						
						LEFT JOIN CORPORE..FRECONCIL FRECONCIL WITH(NOLOCK) ON
							FRECONCIL.IDRECONCIL = FRECONCILRELAC.IDRECONCIL
						AND FRECONCIL.CODCOLIGADA = FRECONCILRELAC.CODCOLIGADA
									
						where FXCX.NUMERODOCUMENTO like '%$nsu%'
						AND FXCX.COMPENSADO = 1
						AND dateadd(dd,datediff(dd,0,FXCX.DATA),0)  = '$data_bradesco'
						AND FXCX.VALOR = '-$valor'";
		}
		else
		if ($fornecedor_id == 2)
		{
			if ($id != '')
			{
				$query = "SELECT 
							CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_AILOG,
							TAG DOCUMENTO,
							REPLACE(UPPER(DESCRICAO),'TRANSFER?NCIA','TRANSFERENCIA') ACAO,
							ABS(DADOS.VALOR) VALOR_AILOG,
							ld.id,
							ld.observacao,
							ld.status_id							
							FROM DADOS_CONCILIACAO_AILOG DADOS WITH (NOLOCK)
							left join lancamento_divergente ld with (nolock) on
								ld.nsu = DADOS.TAG
								and ld.fornecedor_id = 2
								and ld.status_id not in ('i')
							LEFT JOIN CORPORE..FXCX FXCX WITH (NOLOCK) ON
								FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS =  DADOS.TAG
								AND FXCX.CODCXA = '20504'
								AND FXCX.CODCOLIGADA = 1
								AND FXCX.COMPENSADO = 1	
								AND FXCX.TIPO = 6
								AND DADOS.ID = 50
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.CAMPOALFAOP2 = FXCX.NUMERODOCUMENTO
								AND FLAN.CODCOLIGADA = 1
							LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
								PFUNC.CHAPA = FLAN.CHAPA
							WHERE DADOS.STATUS_ID IN ('a')
							AND DADOS.ID = $id";	
			}
			
		}
		else
		if ($fornecedor_id == 3)
		{
			if ($id != '')
			{
				$query = "SELECT 
							CONVERT(VARCHAR(10), DADOS.DATA_TRANSACAO, 103) DATA_BPP,
							ASSUNTO DOCUMENTO,
							UPPER(ACAO)+' - '+MOTORISTA ACAO,
							DADOS.VALOR VALOR_BPP,
							ld.id,
							ld.observacao,
							ld.status_id							
							FROM DADOS_CONCILIACAO_BPP DADOS WITH (NOLOCK)
							left join lancamento_divergente ld with (nolock) on
								ld.nsu = DADOS.assunto
								and ld.fornecedor_id = 3
								and ld.status_id not in ('i')
							LEFT JOIN CORPORE..FXCX FXCX WITH (NOLOCK) ON
								FXCX.NUMERODOCUMENTO COLLATE Latin1_General_CI_AS =  DADOS.ASSUNTO
								AND FXCX.CODCXA = '20500'
								AND FXCX.CODCOLIGADA = 1
								AND FXCX.COMPENSADO = 1	
								AND FXCX.TIPO = 6
								AND DADOS.ID = 50
							LEFT JOIN CORPORE..FLAN WITH (NOLOCK) ON
								FLAN.CAMPOALFAOP2 = FXCX.NUMERODOCUMENTO
								AND FLAN.CODCOLIGADA = 1
							LEFT JOIN CORPORE..PFUNC WITH (NOLOCK) ON
								PFUNC.CHAPA = FLAN.CHAPA
							WHERE DADOS.STATUS_ID IN ('a')
							AND DADOS.ID = $id";	
			}
			
		}
		else
		if ($fornecedor_id == 4)
		{
			$query = "SELECT 
							CONVERT(VARCHAR(10), LAYOUT_ARQUIVO.DATA_TRANSACAO, 103) DATA_TAG,
							ID_VIAGEM DOCUMENTO,
							NOME_CONTRATADO PROPRIETARIO,
							CAST(SUBSTRING(cast(LAYOUT_ARQUIVO.VALOR_TRANSACAO AS VARCHAR), 1, len(cast(valor_transacao as varchar))-2)+'.'+SUBSTRING(cast(valor_transacao as varchar), len(cast(valor_transacao as varchar))-1, 2)as numeric(15,2)) VALOR_TAG,
							ld.id,
							ld.observacao,
							ld.status_id
						FROM LAYOUT_ARQUIVO WITH (NOLOCK)
						left join lancamento_divergente ld with (nolock) on
							ld.nsu = LAYOUT_ARQUIVO.ID_VIAGEM
							and ld.fornecedor_id = 4
							and ld.status_id not in ('i')
						WHERE NUMERO_TAG IS NOT NULL
						AND ID_VIAGEM = $nsu
						ORDER BY DATA_TAG, LAYOUT_ARQUIVO.ID_VIAGEM";
		}		
	}
	else
	{
		$query = "select '$data', nsu, 'REGISTRO NAO ENCONTRADO DO BRADESCO', valor, id, observacao
					from 
					where nsu = '$nsu'	
					and 2 = '$tipo'
					
					union					
					select '$data', '$nsu', 'REGISTRO NAO ENCONTRADO DO BRADESCO', '$valor', null, null";
	}
	print "<pre>$query</pre>";
	$result = $con->executar($query);	
	
	$data 			= odbc_result($result, 1);
	$documento	 	= odbc_result($result, 2);
	$historico		= odbc_result($result, 3);
	$valor			= odbc_result($result, 4);
	$lancamento_id	= odbc_result($result, 5);	
	$observacao		= odbc_result($result, 6);
	$status_id		= odbc_result($result, 7);
	
	$historico = utf8_encode($historico);
	$observacao = utf8_encode($observacao);
	
	$campos .= "<table class='table table-bordered table-hover' id='myTable3' 
					 style='border-bottom: none;border-top:none;'>
						<thead>
							<th bgcolor='#d3d3d3'><b><center><font size='-2'>DATA</font></th>
							<th bgcolor='#d3d3d3'><b><center><font size='-2'>DOCUMENTO</font></th>
							<th bgcolor='#d3d3d3'><b><center><font size='-2'>DESCRIÇÃO</font></th>						
							<th bgcolor='#d3d3d3'><b><center><font size='-2'>VALOR</font></th>	
							<th bgcolor='#d3d3d3'><b><center><font size='-2'>OBSERVAÇÃO</font></th>	
						</thead><tbody>		
					<tr>
					  <td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$data."</strong></font></div></td>";
		if (($fornecedor_id == 1) || ($fornecedor_id == 4))
		$campos .= "<td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$documento."</strong></font></div></td>";
		else
		$campos .= "<td bgcolor='#FFFFFF'><div align='center'>
			<input name='documento_duplicado' type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' id='documento_duplicado' size='10' value='$documento' /></div></td>";
		
		$campos .= "<td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$historico."</strong></font></div></td>
					<td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".str_replace('.',',',$valor)."</strong></font></div></td>";
					
		if ($status_id == 'f')
			$campos .= "<td bgcolor='#FFFFFF'><div align='center'><font size='-1'>".$observacao."</strong></font></div></td>";
		else
			$campos .= "
					<td bgcolor='#FFFFFF'><font size='-1' >
						<font size='-1' color='#FF0000'>
						<div align='center'>
						  <input name='observacao_divergente' type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' id='observacao_divergente' size='40' value='$observacao' />
						  <font size='-1' >
						</div></td>";
			$campos .= "
					  </tr>						 
		   </table>
				<div class='col-md-12' style='text-align: center;'>
					<input name='inserir' class='btn-sm btn-primary' id='inserir' onclick='grava_observacao_divergente($fornecedor_id,$id,".chr(34).$documento.chr(34).",".chr(34).$valor.chr(34).")' type='button' value='Gravar' >
					<input name='finalizar' class='btn-sm btn-success' id='finalizar' onclick='cancela_finaliza_divergente(2,$lancamento_id,$fornecedor_id)' type='button' value='Finalizar' >										
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input name='exluir' class='btn-sm btn-warning' id='exluir' onclick='cancela_finaliza_divergente(3,$lancamento_id,$fornecedor_id)' type='button' value='Excluir' >					
					<br>
				</div>  
		   ";

		
		$retorno->campos = '1|'.$campos;

		return $retorno;
	
	}	
	
	
	public function ultimaInformacao(){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = new stdClass();



		$query = "SELECT TOP 1 convert(varchar(10),data_transacao,103)
					FROM dados_conciliacao_ailog with (nolock)
					WHERE STATUS_ID = 'a'
					order by data_transacao desc";
		$result = $con->executar($query);	
		$ailog 	= odbc_result($result, 1);						
					
		$query = "SELECT TOP 1 convert(varchar(10),data_transacao,103)
					FROM dados_conciliacao_bpp with (nolock)
					WHERE STATUS_ID = 'a'
					order by data_transacao desc";
		$result = $con->executar($query);	
		$bpp 	= odbc_result($result, 1);						

		$ailog = utf8_encode($ailog);
		$bpp = utf8_encode($bpp);		
		
		
		$retorno->informacao = "<p><font color='#0000FF' size='-1'><b>ÚLTIMA DATA IMPORTADA (AILOG): ".$ailog."<p>ÚLTIMA DATA IMPORTADA (BRASIL PRÉ-PAGO):".$bpp;

		return $retorno;
	
	}		
	
	
}


?>
