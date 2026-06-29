# Alterações no Banco de Dados

## Data: 26/06/2026

### Objetivo
Sincronizar a estrutura do banco `labsoft_teste` com o banco `labsoft_novo`.

### Alterações Realizadas

Foram adicionadas 4 novas colunas na tabela `janela` do banco `labsoft_teste`:

| Coluna | Tipo | Valor Padrão |
|--------|------|--------------|
| scaneado | varchar(50) | 'Não' |
| carga_em_qualidade | varchar(50) | 'Não' |
| carregando_ou_rejeitado | varchar(50) | NULL |
| documentos | varchar(50) | 'aguardando' |

### Comandos SQL Executados

```sql
ALTER TABLE `janela` ADD COLUMN `scaneado` varchar(50) DEFAULT 'Não';

ALTER TABLE `janela` ADD COLUMN `carga_em_qualidade` varchar(50) DEFAULT 'Não';

ALTER TABLE `janela` ADD COLUMN `carregando_ou_rejeitado` varchar(50);

ALTER TABLE `janela` ADD COLUMN `documentos` varchar(50) DEFAULT 'aguardando';
```

### Arquivo de Backup
Os comandos SQL também foram salvos em: `alteracoes_banco.sql`
