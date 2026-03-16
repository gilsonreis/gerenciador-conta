-- Auto initialized using MYSQL_DATABASE

CREATE TABLE instituicoes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    instituicao_id INT NOT NULL, 
    nome VARCHAR(50) NOT NULL, 
    CONSTRAINT fk_categoria_instituicao FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    instituicao_id INT NOT NULL, 
    nome VARCHAR(100) NOT NULL, 
    email VARCHAR(150) NOT NULL UNIQUE, 
    senha VARCHAR(255) NOT NULL, 
    CONSTRAINT fk_usuario_instituicao FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE
);

CREATE TABLE caixa_entradas (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    instituicao_id INT NOT NULL, 
    usuario_id INT NOT NULL, 
    origem VARCHAR(255) NOT NULL, 
    valor DECIMAL(10, 2) NOT NULL, 
    data_entrada DATE NOT NULL, 
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    CONSTRAINT fk_caixa_instituicao FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE
);

CREATE TABLE lancamentos (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    instituicao_id INT NOT NULL, 
    usuario_id INT NOT NULL, 
    categoria_id INT NOT NULL, 
    descricao VARCHAR(255) NOT NULL, 
    conta_fixa TINYINT(1) DEFAULT 0, 
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    CONSTRAINT fk_lanc_instituicao FOREIGN KEY (instituicao_id) REFERENCES instituicoes(id) ON DELETE CASCADE, 
    CONSTRAINT fk_lanc_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT
);

CREATE TABLE parcelas (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    lancamento_id INT NOT NULL, 
    numero_parcela INT DEFAULT 1, 
    total_parcelas INT DEFAULT 1, 
    valor DECIMAL(10, 2) NOT NULL, 
    data_vencimento DATE NOT NULL, 
    status ENUM('pendente', 'pago') DEFAULT 'pendente', 
    CONSTRAINT fk_parcela_lancamento FOREIGN KEY (lancamento_id) REFERENCES lancamentos(id) ON DELETE CASCADE
);

-- Seed Data
INSERT INTO instituicoes (nome) VALUES ('Família Reis');

-- Admin admin@familia.com | 123456
INSERT INTO usuarios (instituicao_id, nome, email, senha) VALUES (1, 'Admin', 'admin@familia.com', '$2y$10$y6mGE6vEwMIFL/wK3xG6bORzZDBvXYL.GIsV2O8N/t.JkEDK.D/.u');

INSERT INTO categorias (instituicao_id, nome) VALUES (1, 'Alimentação'), (1, 'Moradia'), (1, 'Transporte');
