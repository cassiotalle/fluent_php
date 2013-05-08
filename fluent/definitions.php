<?php

/**
 * Nome da companhia
 */
App::$company_name = null;

/**
 * Define se o sistema esta instalado em uma subpasta.
 * Ex: http://www.dominio.com/seusite
 */
App::$sub_dir = '';

/**
 * Configura as libs que serão carregadas em todo o projeto
 * <code>
 * App::$defealt_load_libs = array('Session','Auth');
 * </code>
 */
App::$defealt_load_libs = array('Session','Auth');

/**
 * Lista css carregados em todas as páginas
 * <code>
 * App::$preload_css = array('scaffold','layout');
 * </code>
 */
App::$preload_css = array('kube.min', 'config');

/**
 * Lista js carrefados em todas as páginas
 */
App::$preload_js = array('jquery','kube.buttons');

/**
 * Nome do layout padrão do sistema
 */
App::$layout = 'default';

/**
 *  Login site
 */
App::$login_url = '/usuario/login';

/**
 *  Variável de segurança utilizada na criptografia
 */
App::$salt = '123qwe';

/**
 * Define a região da aplicação;
 */

App::$time_zone = "America/Sao_Paulo";

/**
 * Ambiente de desenvolvimento
 */
App::$development = false;

/**
 * E-mail do desenvolvedor para receber relatório de erros do sistema
 */
App::$email_administrator = 'cassiotalle@gmail.com';

/**
 * Servidor smtp para o envio de e-mails
 */
App::$smtp_host = 'smtp.gmail.com';

/**
 * Endereço e e-mail padrão para o envio do email
 */
App::$smtp_email = 'seuemail@gmail.com';

/**
 * Senha de acesso a conta de e-mail
 */
App::$smtp_pass = 'ca339297';

/**
 * Porta para o envio de e-mail
 */
App::$smtp_port = 587;


?>