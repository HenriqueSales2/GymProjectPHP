-- ============================================
-- ACADEMIA ZENET - Banco de Dados Completo
-- Execute no phpMyAdmin ou MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS academia_zenet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE academia_zenet;

-- ============================================
-- TABELA: professores
-- ============================================
CREATE TABLE IF NOT EXISTS professores (
                                           id INT AUTO_INCREMENT PRIMARY KEY,
                                           nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- ============================================
-- TABELA: alunos
-- ============================================
CREATE TABLE IF NOT EXISTS alunos (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_nascimento DATE,
    objetivo VARCHAR(100),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- ============================================
-- TABELA: grupos_musculares
-- ============================================
CREATE TABLE IF NOT EXISTS grupos_musculares (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 nome VARCHAR(60) NOT NULL
    );

INSERT INTO grupos_musculares (nome) VALUES
                                         ('Peito'), ('Costas'), ('Ombros'), ('Bíceps'), ('Tríceps'),
                                         ('Antebraço'), ('Quadríceps'), ('Posterior de Coxa'), ('Glúteos'),
                                         ('Panturrilha'), ('Abdômen'), ('Lombar'), ('Trapézio'), ('Corpo Todo');

-- ============================================
-- TABELA: exercicios (catálogo)
-- ============================================
CREATE TABLE IF NOT EXISTS exercicios (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          nome VARCHAR(100) NOT NULL,
    grupo_muscular_id INT NOT NULL,
    descricao TEXT,
    equipamento VARCHAR(80),
    FOREIGN KEY (grupo_muscular_id) REFERENCES grupos_musculares(id)
    );

-- PEITO
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Supino Reto com Barra', 1, 'Deite no banco, segure a barra na largura dos ombros e empurre verticalmente.', 'Barra + Banco'),
                                                                             ('Supino Inclinado com Halteres', 1, 'Banco inclinado a 30-45°. Empurra os halteres para cima convergindo.', 'Halteres + Banco'),
                                                                             ('Supino Declinado com Barra', 1, 'Banco declinado, foco na porção inferior do peitoral.', 'Barra + Banco'),
                                                                             ('Crossover no Cabo', 1, 'Puxada em "V" com cabos para trabalhar o peitoral com contração máxima.', 'Cabo'),
                                                                             ('Flexão de Braço', 1, 'Empurra o solo com as mãos, corpo em prancha. Peso corporal.', 'Peso Corporal'),
                                                                             ('Peck Deck (Voador)', 1, 'Máquina de abdução do peitoral. Junta os braços na frente.', 'Máquina'),
                                                                             ('Supino com Halteres', 1, 'Mesmo que supino reto, porém com halteres para maior amplitude.', 'Halteres + Banco'),
                                                                             ('Mergulho em Paralelas (Peitoral)', 1, 'Corpo inclinado para frente, desce e sobe nas barras paralelas.', 'Paralelas');

-- COSTAS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Puxada Frontal na Polia', 2, 'Segura a barra acima e puxa até a altura do queixo.', 'Polia Alta'),
                                                                             ('Remada Curvada com Barra', 2, 'Corpo a ~45°, puxa a barra em direção ao abdômen.', 'Barra'),
                                                                             ('Remada Unilateral com Halter', 2, 'Apoiado no banco, puxa o halter em direção ao quadril.', 'Halter + Banco'),
                                                                             ('Remada na Máquina', 2, 'Sentado na máquina, puxa as alças em direção ao abdômen.', 'Máquina'),
                                                                             ('Puxada com Pegada Supinada', 2, 'Puxada frontal com palmas voltadas para você, foco no bíceps e dorsal.', 'Polia Alta'),
                                                                             ('Barra Fixa', 2, 'Pendurado na barra, puxa o corpo até o queixo ultrapassar a barra.', 'Barra Fixa'),
                                                                             ('Remada Cavalinho', 2, 'Peito apoiado em banco inclinado, remada bilateral com halteres.', 'Halteres + Banco'),
                                                                             ('Pullover com Halter', 2, 'Deitado no banco, segura o halter atrás da cabeça e traz à frente.', 'Halter + Banco'),
                                                                             ('Serrote (Remada Unilateral no Cabo)', 2, 'Remada unilateral usando polia baixa.', 'Cabo');

-- OMBROS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Desenvolvimento com Barra', 3, 'Em pé ou sentado, empurra a barra verticalmente acima da cabeça.', 'Barra'),
                                                                             ('Elevação Lateral com Halteres', 3, 'Eleva os braços para os lados até altura dos ombros.', 'Halteres'),
                                                                             ('Elevação Frontal com Anilha', 3, 'Eleva a anilha à frente do corpo até a altura dos ombros.', 'Anilha'),
                                                                             ('Desenvolvimento Arnold', 3, 'Desenvolvimento com halteres com rotação dos punhos.', 'Halteres'),
                                                                             ('Encolhimento de Ombros (Trapézio)', 13, 'Em pé com halteres, eleva os ombros o máximo possível.', 'Halteres'),
                                                                             ('Voador Inverso (Deltóide Posterior)', 3, 'Tronco inclinado, eleva os halteres para os lados.', 'Halteres'),
                                                                             ('Face Pull no Cabo', 3, 'Puxa o cabo em direção ao rosto, separando as mãos.', 'Cabo'),
                                                                             ('Desenvolvimento na Máquina', 3, 'Desenvolvimento para ombros na máquina guiada.', 'Máquina');

-- BÍCEPS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Rosca Direta com Barra', 4, 'Em pé, flexiona os cotovelos com barra reta.', 'Barra'),
                                                                             ('Rosca Alternada com Halteres', 4, 'Flexiona um braço de cada vez com halteres.', 'Halteres'),
                                                                             ('Rosca Concentrada', 4, 'Sentado, cotovelo apoiado na coxa, flexão unilateral.', 'Halter'),
                                                                             ('Rosca Scott (Barra W)', 4, 'Braços apoiados no banco Scott, usa barra W.', 'Barra W + Banco Scott'),
                                                                             ('Rosca Martelo', 4, 'Flexão com halteres com pegada neutra (polegar para cima).', 'Halteres'),
                                                                             ('Rosca no Cabo', 4, 'Rosca direta usando polia baixa para tensão constante.', 'Cabo'),
                                                                             ('Rosca 21', 4, '7 parciais inferiores + 7 parciais superiores + 7 completas.', 'Barra ou Halteres');

-- TRÍCEPS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Tríceps Testa com Barra W', 5, 'Deitado, desce a barra até a testa e empurra.', 'Barra W + Banco'),
                                                                             ('Tríceps Corda no Cabo', 5, 'Puxa a corda para baixo, separando as pontas no final.', 'Cabo'),
                                                                             ('Mergulho em Paralelas (Tríceps)', 5, 'Corpo ereto, desce e sobe nas paralelas.', 'Paralelas'),
                                                                             ('Tríceps Francês com Halter', 5, 'Sentado, halter atrás da cabeça, extensão acima.', 'Halter'),
                                                                             ('Supino Fechado', 5, 'Supino com pegada estreita, foco no tríceps.', 'Barra + Banco'),
                                                                             ('Kick Back com Halter', 5, 'Tronco inclinado, estende o braço para trás.', 'Halter'),
                                                                             ('Tríceps Polia Alta', 5, 'Extensão do cotovelo usando polia alta.', 'Cabo');

-- QUADRÍCEPS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Agachamento Livre', 7, 'Com barra nas costas, desce até as coxas ficarem paralelas ao chão.', 'Barra + Rack'),
                                                                             ('Leg Press 45°', 7, 'Sentado na máquina, empurra a plataforma em diagonal.', 'Máquina'),
                                                                             ('Cadeira Extensora', 7, 'Sentado, estende as pernas na máquina.', 'Máquina'),
                                                                             ('Hack Squat', 7, 'Agachamento na máquina inclinada.', 'Máquina'),
                                                                             ('Afundo (Lunge)', 7, 'Passo à frente, desce o joelho traseiro quase ao chão.', 'Peso Corporal ou Halteres'),
                                                                             ('Agachamento Búlgaro', 7, 'Pé traseiro elevado, agachamento unilateral.', 'Halteres ou Barra'),
                                                                             ('Agachamento Goblet', 7, 'Com halter ou kettlebell no peito, agachamento profundo.', 'Halter'),
                                                                             ('Agachamento Sumô', 7, 'Postura larga, pontas dos pés para fora.', 'Barra ou Halter');

-- POSTERIOR DE COXA
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Stiff com Barra', 8, 'Em pé, desce a barra com pernas levemente flexionadas.', 'Barra'),
                                                                             ('Mesa Flexora', 8, 'Deitado de bruços na máquina, flexiona os joelhos.', 'Máquina'),
                                                                             ('Cadeira Flexora', 8, 'Sentado, flexiona os joelhos contra a resistência.', 'Máquina'),
                                                                             ('Levantamento Terra Romeno', 8, 'Similar ao stiff, pernas semi-flexionadas, barra próxima ao corpo.', 'Barra'),
                                                                             ('Levantamento Terra Convencional', 8, 'Pega a barra no chão e fica em pé completamente.', 'Barra'),
                                                                             ('Curl Deitado no Cabo', 8, 'Deitado de bruços, flexiona o joelho usando cabo.', 'Cabo'),
                                                                             ('Good Morning', 8, 'Barra nas costas, inclina o tronco à frente com quadril.', 'Barra');

-- GLÚTEOS
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Hip Thrust com Barra', 9, 'Costas no banco, barra no quadril, empurra o quadril para cima.', 'Barra + Banco'),
                                                                             ('Elevação Pélvica', 9, 'Mesmo que hip thrust sem banco, deitado no chão.', 'Peso Corporal'),
                                                                             ('Glúteo no Cabo (4 apoios)', 9, 'Preso ao cabo, estende a perna para trás em 4 apoios.', 'Cabo'),
                                                                             ('Agachamento Sumo com Halter', 9, 'Postura larga, halter entre as pernas.', 'Halter'),
                                                                             ('Passada Lateral', 9, 'Passo lateral com agachamento unilateral.', 'Peso Corporal'),
                                                                             ('Abdução de Quadril na Máquina', 9, 'Sentado, abre as pernas contra a resistência.', 'Máquina');

-- PANTURRILHA
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Panturrilha em Pé na Máquina', 10, 'Eleva os calcanhares na máquina de panturrilha.', 'Máquina'),
                                                                             ('Panturrilha Sentado', 10, 'Sentado com peso nos joelhos, eleva os calcanhares.', 'Máquina ou Halteres'),
                                                                             ('Panturrilha no Leg Press', 10, 'Empurra a plataforma com as pontas dos pés.', 'Leg Press'),
                                                                             ('Panturrilha Livre (Escada)', 10, 'Eleva os calcanhares em degrau, peso corporal ou halteres.', 'Degrau');

-- ABDÔMEN
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Abdominal Supra (Crunch)', 11, 'Deitado, eleva o tronco em direção aos joelhos.', 'Peso Corporal'),
                                                                             ('Abdominal Infra (Elevação de Pernas)', 11, 'Deitado, eleva as pernas juntas até 90°.', 'Peso Corporal'),
                                                                             ('Prancha (Plank)', 11, 'Sustenta o corpo em posição de prancha por tempo.', 'Peso Corporal'),
                                                                             ('Abdominal Oblíquo (Russian Twist)', 11, 'Sentado, gira o tronco de lado a lado com peso.', 'Anilha ou Halter'),
                                                                             ('Roda Abdominal', 11, 'Com a roda abdominal, estende e retrai o corpo.', 'Roda Abdominal'),
                                                                             ('Crunch na Polia', 11, 'Ajoelhado, flexiona o tronco usando cabo acima.', 'Cabo'),
                                                                             ('Abdominal na Máquina', 11, 'Flexão de tronco na máquina de abdômen.', 'Máquina'),
                                                                             ('Mountain Climber', 11, 'Em posição de prancha, alterna trazendo joelhos ao peito.', 'Peso Corporal');

-- CORPO TODO (Funcionais/Cardio)
INSERT INTO exercicios (nome, grupo_muscular_id, descricao, equipamento) VALUES
                                                                             ('Burpee', 14, 'Agachamento + flexão + salto. Exercício aeróbico total.', 'Peso Corporal'),
                                                                             ('Kettlebell Swing', 14, 'Balanço do kettlebell entre as pernas até a altura dos ombros.', 'Kettlebell'),
                                                                             ('Battle Rope', 14, 'Ondulação das cordas com movimento vigoroso de braços.', 'Corda Funcional'),
                                                                             ('Agachamento com Salto', 14, 'Agachamento explosivo com salto vertical.', 'Peso Corporal'),
                                                                             ('Dead Hang', 14, 'Pendurado na barra, sustenta o peso por tempo.', 'Barra Fixa'),
                                                                             ('Farmer Walk', 14, 'Caminha carregando halteres pesados em cada mão.', 'Halteres Pesados');

-- ============================================
-- TABELA: treinos (cabeçalho)
-- ============================================
CREATE TABLE IF NOT EXISTS treinos (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       aluno_id INT NOT NULL,
                                       professor_id INT NOT NULL,
                                       tipo_divisao ENUM('A/B/C/D/E','Upper/Lower','Full Body','Push/Pull/Legs') NOT NULL,
    letra_treino VARCHAR(10) NOT NULL COMMENT 'Ex: A, B, Upper, Push, Full Body',
    descricao VARCHAR(200),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professores(id)
    );

-- ============================================
-- TABELA: treino_exercicios (itens do treino)
-- ============================================
CREATE TABLE IF NOT EXISTS treino_exercicios (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 treino_id INT NOT NULL,
                                                 exercicio_id INT NOT NULL,
                                                 series INT DEFAULT 3,
                                                 repeticoes VARCHAR(20) DEFAULT '12' COMMENT 'Ex: 12, 8-12, 15, Falha',
    carga_kg DECIMAL(5,1) DEFAULT NULL COMMENT 'Em kg, pode ser nulo se peso corporal',
    descanso_seg INT DEFAULT 60 COMMENT 'Descanso em segundos',
    observacao VARCHAR(200),
    ordem INT DEFAULT 0,
    FOREIGN KEY (treino_id) REFERENCES treinos(id) ON DELETE CASCADE,
    FOREIGN KEY (exercicio_id) REFERENCES exercicios(id)
    );

-- ============================================
-- DADOS DE EXEMPLO
-- ============================================

-- Professor padrão (senha: 1234)
INSERT INTO professores (nome, email, cpf, senha) VALUES
                                                      ('Prof. Carlos Silva', 'carlos@zenet.com', '00000000001', MD5('1234')),
                                                      ('Prof. Ana Souza', 'ana@zenet.com', '00000000002', MD5('1234'));

-- Aluno de exemplo (senha: 123456)
INSERT INTO alunos (nome, email, cpf, senha, data_nascimento, objetivo) VALUES
    ('João Teste', 'joao@email.com', '12345678900', MD5('123456'), '2000-01-15', 'Hipertrofia');