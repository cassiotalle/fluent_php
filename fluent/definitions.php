<?php

/**
 * Nome da companhia
 */
App::$company_name = 'CSDev';

/**
 * Define se o sistema esta instalado em uma subpasta.
 * Ex: http://www.dominio.com/seusite
 */
App::$sub_dir = 'fluent_php';

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
App::$preload_css = array('kube.min','master', 'config');

/**
 * Lista js carrefados em todas as páginas
 */
App::$preload_js = array('jquery','kube.buttons');

/**
 *  Login site
 */
App::$login_url = 'usuario/logar';

/**
 *  Variável de segurança utilizada na criptografia
 */
App::$salt = '7gf5dsf2';

/**
 * Define a região da aplicação;
 */

App::$time_zone = "America/Sao_Paulo";

/**
 * Ambiente de desenvolvimento
 */
App::$development = true;

/**
 * Email do desenvolvedor para receber relatório de erros do sistema
 */
App::$email_administrator = 'cassiotalle@gmail.com';

?>