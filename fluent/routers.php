<?php
/**
 * Neste documento são definidas as rotas de links e redirecionamentos para a 
 * aplicação.
 */

/**
 *  Router defealt que será exibido como index da aplicação.
 */
App::$router_defealt = array('home','index');

/**
 * Define uma url alternativa para controllers e actions
 *<code>
 *  App::$routers=array(
    'testerouter'=>'router',
    't2router'=>
        array('index'=>
            array('router2','index'),
        'user'=>
            array('add'=>'adicionar')
        )
    );
 * </code>
 * @var array
 */
App::$routers=array(
        'usuario'=>'usuarios',
        'usuario'=>array('cadastrar'=>'add')
    );

/**
 * Define um prefixo para para um conjunto controlles. Estes controllers devem 
 * iniciar com o mesmo nome do prefixo ao qual ele pertence. 
 * Ex1:
 * Nome da classe: AdminPainelController
 * Nome do arquivo: /controller/admin_painel_contoller.php
 * Link de acesso: http://www.site.com/admin/painel
 * 
 * Nome da classe AdminController
 * Nome do arquivo /controller/admin
 * Link de acesso http://www.site.com/admin
 * <code>
 * Configure::$prefix_router = array('admin');
 * </code>
 */
App::$prefix_router = array('admin');


?>