ALTER TABLE `janela` ADD COLUMN `scaneado` varchar(50) DEFAULT 'Não';

ALTER TABLE `janela` ADD COLUMN `carga_em_qualidade` varchar(50) DEFAULT 'Não';

ALTER TABLE `janela` ADD COLUMN `carregando_ou_rejeitado` varchar(50);

ALTER TABLE `janela` ADD COLUMN `documentos` varchar(50) DEFAULT 'aguardando';