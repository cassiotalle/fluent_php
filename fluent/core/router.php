<?php

/*
 * Identifica os parâmentros da url. Controller, Action, Conditions e $_GET
 */
App::$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
App::$link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . App::$sub_dir . '/';
if (isset($_SESSION['_data'])) {
  App::$data = $_SESSION['_data'];
  unset($_SESSION['_data']);
}
if (isset($_GET['url'])) {
  $url = preg_split('/(\/)/', $_GET['url'], -1, PREG_SPLIT_NO_EMPTY);
  $url[0] = strtolower($url[0]);
  $url_count = count($url);
  //verifica se há prefixo para esta url definida pela veriável App::$prefix_router
  if (in_array($url[0], App::$prefix_router) && $url_count > 1) {
    if (file_exists(CONTROLLER . $url[0] . '_' . $url[1] . '_controller.php')) {
      $c = $url[0] . '_' . $url[1];
      $url = array_slice($url, 2);
      array_unshift($url, $c);
    }
  }
  // verifica se há alguma regra de reescrita para esta rota definida pela veriável App::$routers 
  elseif (array_key_exists($url[0], App::$routers)) {
    //se for string substitua o nome do controller para todas as actions
    if (is_string(App::$routers[$url[0]])) {
      $url[0] = App::$routers[$url[0]];
    } elseif($url_count == 1 && is_array(App::$routers[$url[0]])){
      $url[1] = App::$routers[$url[0]][1];
      $url[0] = App::$routers[$url[0]][0];
    }
  } elseif ($url_count > 1 && key_exists($url[0] . '/' . $url[1], App::$routers)) {
    $k = $url[0] . '/' . $url[1];
    $url[0] = App::$routers[$k][0];
    $url[1] = App::$routers[$k][1];
  }
  // se for array atualize action específica

  if (count($url) == 1)
    $url[1] = 'index';
  elseif ($url_count > 2) {
    App::$conditions = array_slice($url, 2);
  }
  App::$controller = $url[0];
  App::$Controller = uper_(App::$controller);

  if (is_numeric($url[1])) {
    App::$action = 'show';
    array_unshift(App::$conditions, $url[1]);
  } else {
    App::$action = $url[1];
  }
} else {
  // garraga rota padrão definida pela variável App::$router_default
  App::$controller = App::$router_defealt[0];
  App::$Controller = ucfirst(App::$controller);
  App::$action = App::$router_defealt[1];
  ;
}
if (file_exists(CONTROLLER . App::$controller . '_controller.php')) {
  include(CONTROLLER . App::$controller . '_controller.php');
  $controller = App::setIstance('controller');
  if (method_exists($controller, App::$action)) {
    $controller->main();
  } elseif (file_exists(VIEW . App::$controller . DS . App::$action . '.php')) {
    include VIEW . App::$controller . DS . App::$action . '.php';
  } else {
    VIEW . App::$controller . DS . App::$action . '.php';
    // não encontrou a view da action
    include(WEBROOT . '404.php');
  }
} elseif (file_exists(VIEW . App::$controller . DS . App::$action . '.php')) {
  include(VIEW . App::$controller . DS . App::$action . '.php');
} elseif (VIEW . App::$router_defealt[0] . DS . App::$action . '.php') {
  if (file_exists(CONTROLLER . App::$router_defealt[0] . '_controller.php')) {
    App::$action = App::$controller;
    App::$controller = App::$router_defealt[0];
    App::$Controller = ucfirst(App::$router_defealt[0]);
    include(CONTROLLER . App::$controller . '_controller.php');
    $controller = App::setIstance('controller');
    if (method_exists($controller, App::$action)) {
      $controller->main();
    } elseif (file_exists(VIEW . App::$router_defealt[0] . DS . App::$action . '.php')) {

      include VIEW . App::$router_defealt[0] . DS . App::$action . '.php';
    } else {
      include(WEBROOT . '404.php');
      exit();
    }
  }
} else {
  // não encontrou a view
  include(WEBROOT . '404.php');
  exit();
}
unset($_GET['url']);
?>