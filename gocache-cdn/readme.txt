=== GoCache ===
Contributors: apiki, gocachecdn, aguiart0, lucasbg0, daniloalvess
Tags: CDN, cache, optimization, performance, speed
Requires at least: 5.3
Tested up to: 6.3
Stable tag: 1.3.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Acelere seu site e reduza seus custos com cloud.

== Description ==
Conecta seu Wordpress com a GoCache, que acelera de forma inteligente as páginas e arquivos estáticos do site, reduzindo o consumo de recursos no servidor web e banco de dados.
A GoCache possui tecnologia CDN de última geração, que ajuda na otimização de sua
infraestrutura web e oferece uma melhor experiência para os visitantes.

= Requisitos =

- PHP versão 5.6 ou superior.
- Conta ativa na [GoCache](http://www.gocache.com.br/ "GoCache").

== Installation ==
1. Faça upload deste plugin em seu WordPress, e ative-o;
2. Entre no menu lateral "GoCache > Autenticação";
3. Preencha com a chave da API da GoCache (A chave está disponível em https://painel.gocache.com.br/login.php);

== Screenshots ==
1. Tela de autenticação do plugin
2. Configurações gerais
3. Limpeza de cache

== Changelog ==

= 1.3.5 - 13/10/2023 =
* Adicionando versões sem barras de urls com barras no final
* Permitindo a utilização de chave de cache que possuem o caractere '/'

= 1.3.4 - 21/09/2023 =

* Adicionando versões sem barras de urls com barras no final
* Permitindo a utilização de chave de cache que possuem o caractere '/'

= 1.3.3 - 11/08/2023 =

* Atualizando compatibilidade com a versão 8.0 do PHP

= 1.3.2 - 18/03/2022 =

* Adicionando suporte a protocolos http e https

= 1.3.1 - 25/02/2022 =

* Compatibilidade com a nova versão do Wordpress - 5.9
* Resolvendo aviso no wp-admin não fechando quando se clica no botão de fechar

= 1.3.0 - 02/02/2022 =

* Atualizando mídias e nome do plugin

= 1.2.8 - 15/12/2021 =

* Criação de constante para ocultar o plugin do painel administrativo

= 1.2.6 - 09/12/2021 =

* Adicionando opção de purge de urls personalizadas por post
* Adicionando opção de purge de urls personalizadas nas configurações gerais
* Resolvendo problema de chave de cache em urls sem barras no final
* Alterando imagens do plugin

= 1.2.5 - 13/08/2021 =

* Resolvendo erros na autenticação
* Resolvendo erros nao sobrescrever domínios
* Resolvendo purge de post archives

= 1.2.4 - 04/08/2021 =

* Tratando erros durante as requisições com WP Request

= 1.2.3 - 28/07/2021 =

* Alterando formato de requests para funções nativas do Wordpress
* Melhorando a higienização e validação de variáveis
* Removendo short tags do PHP

= 1.2.2 - 26/07/2021 =

* Compatibilidade com a nova versão do Wordpress - 5.8

= 1.2.1 - 23/07/2021 =

* Melhorando purge automático após remoção de posts ou mídias

= 1.1.9 - 28/06/2021 =

* Atualizando imagens do repositório
* Adicionando purge automático após remoção de posts ou mídias
* Atualizando de pacotes e dependências
* Tradução de campos nas configurações de cache

= 1.1.8 - 14/06/2021 =

* Suporte para multisites
* Tradução de campos nas configurações de cache

= 1.1.7 - 31/05/2021 =

* Adicionando novos modelos de URLs AMP

= 1.1.6 - 10/04/2021 =

* Adicionando Opção de purge para URLs AMP
* Adicionando URLs de posts com validador AMP de forma dinâmica
* Adicionando URLs de archives por data

= 1.1.5 - 22/04/2021 =

* Refatorando purge automático de sitemaps

= 1.1.4 - 20/04/2021 =

* Busca automática e inserção manual de sitemaps nas configurações de cache
* Identificação protocolo e correção de duplicação de urls com protocolos diferentes

= 1.1.3 - 13/04/2021 =

* Inclusão de sitemaps na limpeza de cache automática de posts
* Inclusão de urls de arquivos anexados ao post na limpeza de cache automática de posts

= 1.1.2 - 18/03/2021 =

* Revisão do código e verificação compatibilidade com a versão 5.7 do WordPress.

= 1.1.1 - 10/03/2021 =

* Verificando compatibilidade com a versão 5.6 do WordPress.

= 1.1.0d - 13/11/2019 =

* Adicionada limpeza de api do wordpress de listagem de posts

= 1.1.0c - 01/06/2019 =

* Adicionada limpeza de páginas de categorias

= 1.1.0 - 11/01/2019 =

* Adicionando urls de CPTs públicos e taxonomias públicas na limpeza de cache.

= 1.0.9 - 09/02/2018 =

* Removendo jshint para atender as especificações do wordpress.org

= 1.0.8 - 25/01/2018 =

* Adicionando opção para inserção de string customizadas na limpeza do cache.
* Melhoria no envio das URLs para API.

= 1.0.7 - 29/12/2017 =

* Adicionando mais variações de urls para limpeza de cache.
* Compatibilidade com a versão 4.9.1 do WordPress.
*
= 1.0.6 - 14/02/2017 =

* Melhorias de UX.
* Alterando ícone do plugin na administração.
* Inclusão de botões de ajuda.

= 1.0.5 - 23/12/2016 =

* Verificando compatibilidade com a versão 4.7 do WordPress.

= 1.0.4 - 11/10/2016 =

* Adicionando barra ao final das urls durante a limpeza automática do cache.

= 1.0.3 - 27/09/2016 =

* Limpando cache automaticamente quando houver publicação de comentários.

= 1.0.2 - 13/09/2016 =

* Correção de bug ao obter o domínio automaticamente.
* Alterando protocolo dinamicamente ao limpar cache.

== Upgrade Notice ==

= 1.0.1 - 09/09/2016 =

* Alteração da URL onde se obtem uma chave da GoCache.
* Definindo opção "Limpar cache automaticamente" como padrão.
* Limpando o cache do domínio padrão sempre que o cache for deletado automaticamente.

= 1.1.0 - 11/01/2019 =

* Adicionando urls de CPTs públicos e taxonomias públicas na limpeza de cache.

= 1.0.9 - 09/02/2018 =

* Removendo jshint para atender as especificações do wordpress.org

= 1.0.8 - 25/01/2018 =

* Adicionando opção para inserção de string customizadas na limpeza do cache.
* Melhoria no envio das URLs para API.

= 1.0.7 - 29/12/2017 =

* Adicionando mais variações de urls para limpeza de cache.
* Compatibilidade com a versão 4.9.1 do WordPress.

= 1.0.6 - 14/02/2017 =

* Melhorias de UX.
* Alterando ícone do plugin na administração.
* Inclusão de botões de ajuda.
*
= 1.0.5 - 23/12/2016 =

* Verificando compatibilidade com a versão 4.7 do WordPress.

= 1.0.3 - 27/09/2016 =

* Limpando cache automaticamente quando houver publicação de comentários.

= 1.0.2 - 13/09/2016 =

* Correção de bug ao obter o domínio automaticamente.
* Alterando protocolo dinamicamente ao limpar cache.

= 1.0.1 - 09/09/2016 =

* Alteração da URL onde se obtem uma chave da GoCache.
* Definindo opção "Limpar cache automaticamente" como padrão.
* Limpando o cache do domínio padrão sempre que o cache for deletado automaticamente.

= 1.0.0 - 23/08/2016 =

* Versão inicial
