-- Adiciona a coluna carregando_ou_rejeitado à tabela janela
ALTER TABLE janela ADD COLUMN IF NOT EXISTS carregando_ou_rejeitado VARCHAR(50) DEFAULT NULL;
