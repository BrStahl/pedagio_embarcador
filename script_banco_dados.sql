-- Tabela de cabeçalho para controle dos arquivos importados
CREATE TABLE layout_cabecalho (
    layout_cabecalho_id INT IDENTITY(1,1) PRIMARY KEY,
    data_inclusao DATETIME DEFAULT GETDATE(),
    nome_arquivo VARCHAR(255),
    usuario_id INT
);

-- Tabela para armazenar os dados importados dos arquivos (Pamcary, Bravo, Sem Parar, etc.)
CREATE TABLE layout_arquivo (
    layout_arquivo_id INT IDENTITY(1,1) PRIMARY KEY,
    layout_cabecalho_id INT,

    -- Colunas Comuns / Bravo
    cnpj_contratante VARCHAR(20),
    id_viagem VARCHAR(50),
    placa_veiculo VARCHAR(20),
    categoria_veiculo VARCHAR(50),
    pais_origem VARCHAR(50),
    uf_cidade_origem VARCHAR(50),
    cidade_origem VARCHAR(100),
    pais_destino VARCHAR(50),
    uf_cidade_destino VARCHAR(50),
    cidade_destino VARCHAR(100),
    data_embarque_viagem DATETIME,
    valor_transacao DECIMAL(18,2),
    numero_documento VARCHAR(100),
    nome_favorecido VARCHAR(100),
    cpf_favorecido VARCHAR(20),
    data_transacao DATETIME,
    numero_pamcard VARCHAR(50),
    valor_pedagio_solicit DECIMAL(18,2),
    embarcador VARCHAR(50),
    status_id CHAR(1),

    -- Colunas Específicas Pamcary (inferidas)
    tipo_registro CHAR(1),
    data_movimento DATETIME,
    numero_sequencial VARCHAR(20),
    tipo_registro_1 CHAR(1),
    cnpj_ponto_emb VARCHAR(20),
    indicador_contrat CHAR(1),
    tipo_documento VARCHAR(10),
    numero_contrato VARCHAR(50),

    FOREIGN KEY (layout_cabecalho_id) REFERENCES layout_cabecalho(layout_cabecalho_id)
);
