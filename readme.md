# 💰 Gerenciador Financeiro Simplify

Um sistema de gestão financeira pessoal e familiar robusto, construído com PHP e MySQL. Desenvolvido com uma arquitetura *multi-tenant* e inspirado nos conceitos de Livro-Razão (Ledger), este projeto garante consistência matemática absoluta no cálculo de saldos e projeções futuras.

## 🚀 Principais Recursos

* **Arquitetura Multi-tenant:** Controle financeiro isolado por Instituição/Família, permitindo múltiplos usuários sob o mesmo teto.
* **Ledger Dinâmico (Livro-Razão):** Os saldos das contas bancárias são calculados em tempo real baseados no histórico completo (Big Bang), eliminando inconsistências de saldos falsos ao editar lançamentos passados.
* **Gestão Inteligente de Parcelas:** Separação estrita de responsabilidades no CRUD. Geração em lote apenas na inserção, protegendo o banco contra mutações acidentais na edição.
* **Dashboard Analítico:** Gráficos interativos (via Chart.js) exibindo o fluxo de caixa dos últimos 12 meses e projetando despesas para os próximos 12 meses.
* **Notificações Proativas (Cron):** Varredura diária que agrupa vencimentos (hoje, 3, 5 e 10 dias) e dispara um relatório executivo único por e-mail utilizando o Symfony Mailer.

## 🛠 Tecnologias Utilizadas

* **Back-end:** PHP 8.2+
* **Banco de Dados:** MySQL 8.0+
* **Front-end:** HTML, Tailwind CSS, jQuery, Chart.js
* **E-mail:** Symfony Mailer
* **Infraestrutura:** Docker (opcional) e Cron Jobs (Linux)

## ⚙️ Instalação e Configuração

### 1. Clonar e Instalar Dependências

    git clone [https://github.com/gilsonreis/gerenciador-conta](https://github.com/gilsonreis/gerenciador-conta)
    cd gerenciador-conta
    composer install

### 2. Banco de Dados

Crie um banco de dados no seu MySQL e importe a estrutura inicial:

    mysql -u seu_usuario -p controle_contas < database/schema.sql

### 3. Configuração de Variáveis de Ambiente (Segurança)

Este projeto **não** utiliza arquivos `.env` tradicionais. Em vez disso, adotamos uma abordagem baseada em arrays nativos do PHP para maior performance e segurança.

Na pasta `config/`, você encontrará um arquivo chamado `env.example.php`. Siga os passos:

1. Renomeie ou copie o arquivo `env.example.php` para `env.local.php` (para desenvolvimento) ou `env.prod.php` (para produção). **Estes arquivos já estão no `.gitignore` e não subirão para o repositório.**
2. O arquivo principal `config/env.php` (que é comitado no Git) carrega valores seguros e vazios por padrão, e é automaticamente sobrescrito caso os arquivos `.local` ou `.prod` existam no servidor.

**Exemplo de preenchimento (`config/env.prod.php`):**

    <?php
    // config/env.prod.php
    return [
        'DB_HOST'   => 'localhost',
        'DB_NAME'   => 'controle_contas',
        'DB_USER'   => 'usuario_producao',
        'DB_PASS'   => 'senha_super_secreta',
        
        'SMTP_HOST' => 'smtp.seudominio.com.br',
        'SMTP_PORT' => 587,
        'SMTP_USER' => 'avisos@seudominio.com.br',
        'SMTP_PASS' => 'senha_do_email',
    ];

### 4. Configuração das Notificações (Cron Job)

Para que o sistema envie os resumos diários de contas a vencer, você deve configurar uma tarefa Cron no seu servidor VPS para rodar uma vez ao dia (ex: às 08:00 da manhã).

Abra o painel de Cron do seu servidor Linux:

    crontab -e

Adicione a seguinte linha (ajuste os caminhos absolutos do PHP e do projeto conforme sua VPS):

    0 8 * * * /usr/bin/php /var/www/gerenciador-conta/cron/notificar_vencimentos.php >> /var/www/gerenciador-conta/cron/cron.log 2>&1

## 🤝 Contribuição

Sinta-se à vontade para abrir *Issues* relatando bugs ou enviando *Pull Requests* com melhorias. Toda contribuição é bem-vinda!

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.