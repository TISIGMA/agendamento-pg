# Erros Frontend da Aplicação
Este arquivo lista todos os erros de frontend encontrados na aplicação, explica o que são e como corrigi-los.

---

## Erros

### 1. `ReferenceError: jQuery is not defined` (morris.min.js)
**Arquivo: `home.php` / `schedules/index.php`
**O que é**: O Morris.js tenta usar `jQuery` antes que a biblioteca esteja carregada.
**Causa**: Ordem incorreta dos scripts no HTML (Outros scripts dependentes são carregados antes do jQuery.
**Como corrigir**: Colocar o jQuery como PRIMEIRO script a ser carregado, antes de todas as outras bibliotecas que dependem dele.

---

### 2. `net::ERR_ABORTED http://localhost/agendamento/data/morris-data.js`
**Arquivo**: `home.php`
**O que é**: Arquivo/diretório não existe.
**Causa**: O projeto não tem um diretório `data/` na raiz.
**Como corrigir**: 
1. Remover ou comentar a linha que tenta carregar esse arquivo, ou criá-lo se for realmente necessário.

---

### 3. `net::ERR_ABORTED http://localhost/agendamento/jquery.datetimepicker.js`
**Arquivo**: `schedules/index.php`
**O que é**: Arquivo `jquery.datetimepicker.js` não existe no caminho especificado.
**Causa**: Caminho incorreto ou arquivo faltante.
**Como corrigir**: Carregar o datetimepicker de um CDN ou verificar se o arquivo existe no local correto.

---

### 4. `TypeError: Cannot read properties of undefined (reading 'Responsive')` (dataTables.responsive.js)
**Arquivo**: `home.php`
**O que é**: Responsive não está definido.
**Causa**: Ordem de carregamento dos plugins DataTables está incorreta (Responsive precisa ser carregado após o DataTables principal).
**Como corrigir**: Garantir que `jquery.dataTables.min.js` é carregado ANTES de `dataTables.responsive.js`.

---

### 5. `TypeError: $(...).metisMenu is not a function` (sb-admin-2.js)
**Arquivo**: `home.php`
**O que é**: O plugin `metisMenu` não está carregado ou está carregado na ordem errada.
**Causa**: O plugin não está presente ou jQuery é carregado após o `sb-admin-2.js`.
**Como corrigir**: Garantir que `metisMenu` está carregado antes do `sb-admin-2.js`.

---

### 6. `TypeError: $(...).mask is not a function` (schedules/index.php)
**Arquivo**: `schedules/index.php`
**O que é**: O plugin jQuery Mask não está carregado na página.
**Causa**: O plugin não está incluído no HTML ou está na ordem errada.
**Como corrigir**: Adicionar o jQuery Mask no `schedules/index.php` (de preferência do CDN).

---

### 7. `TypeError: $(...).datetimepicker is not a function` (schedules/index.php)
**Arquivo**: `schedules/index.php`
**O que é**: O plugin datetimepicker e sua dependência Moment.js não estão carregados na página.
**Causa**: Faltam as bibliotecas Moment.js e datetimepicker no HTML.
**Como corrigir**: Adicionar Moment.js e datetimepicker no `schedules/index.php` (de preferência do CDN).