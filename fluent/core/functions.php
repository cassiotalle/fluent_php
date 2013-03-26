<?php

/**
 * @author Cássio Talle <cassiotalle@gmail.com>
 * Lista de funções que auxiliam no desenvolvimento do sistema
 */

/**
 * Exibe de forma um conteúdo estrutural para melhor visualização
 * @param type $data
 */
function pr($data) {
  echo '<pre>' . print_r($data, true) . '</pre>';
}

/**
 * Redireciona arquivo
 * @param type $link
 */
function redirect($link) {
  if (isset(App::$reaload_flash)) {

    $_SESSION['flash'] = App::$reaload_flash;
  }
  header('Location: ' . $link, true);
  exit;
}

/**
 * Verifica se um array está vazia
 * @param type $array
 * @return boolean
 */
function check_array($array) {
  if (@isset($array) && !empty($array)) {
    return true;
  }
  return false;
}

/**
 * Retorna uma string separadas por "," ou outra opção escolhida pelo usuário
 * @param array $array
 * @param type $by
 * @return type
 */
function concat_array(array $array, $by = ',') {
  return implode($by, $array);
}

/**
 * Remove os pontos e substitui a virgula pelo ponto e retorna o valor formatado para gravar no banco
 * @param type $get_valor
 * @return type
 */
function moeda($get_valor) {
  $source = array('.', ',');
  $replace = array('', '.');
  $valor = str_replace($source, $replace, $get_valor); 
  return $valor; 
}

/**
 * Simplifica uma string retirando acentos e espaços
 * @param type $string
 * @param type $spaces
 * @param type $alter
 * @return type
 */
function simpleString($string = '', $spaces = false, $alter = 0) {
  if ($spaces === false) {
    $spaces = '';
  } elseif ($spaces === true) {
    $spaces = ' ';
  }
  $return = strtolower(ereg_replace("[^a-zA-Z0-9-]", $spaces, strtr(utf8_decode(trim($string)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")));
  switch ($alter) {
    case 1: $return = strtoupper($return);
    case -1: $return = strtolower($return);
  }
  return $return;
}

/**
 * Comverte string separada por "_" em separação por letras maiúsculas
 * Ex: uper_('pagina_principal') == PaginaInicial
 * @param type $string
 * @param type $delimiter
 * @return string
 */
function uper_($string, $delimiter='_'){
  $a = explode($delimiter, $string);
  foreach ($a as $b){
    $r .= ucfirst($b);
  }
  return $r;
}

/**
 * Hora atual
 * @return date
 */
function now() {
  return date("Y-m-d H:i:s");
}

/**
 * Conversão md5 usando salt
 * @param type $pass
 * @return string
 */
function convert_pass($pass) {
  return md5($pass . App::$salt);
}

/**
 * Informar erro
 * @param type $error
 * @param type $object
 * @param type $feactures
 */
function set_include_error($error, $object, $feactures) {
  $op = array(
      408 => array(
          'Módulo não localizado',
          'O módulo <b>' . $object . '</b> não foi localizado em: <span>' . $feactures . '</span>',
          '<li> Verificar na pasta ' . LAYOUT . 'modules' . DS . '</li>' .
          '<li> Verificar se o nome do módulo foi digitado corretamente no arquivo: ' . LAYOUT . 'loads.php'
      ),
      409 => array(
          'Lib não localizada',
          'A lib <b>' . $object . '</b> não foi localizada em: <span>' . $feactures . '</span>',
          '<li> Verificar se o arquivo <b>' . $object . '_lib.php </b> na pasta ' . LIBS . '</li>' .
          '<li> Verificar se o nome do módulo foi digitado corretamente no controller ' . App::$Controller . ' : ' . CONTROLLER . App::$controller . '_controller.php' .
          '<li> Verificar se o nome da lin foi digitada corretamente no arquivo: ' . LAYOUT . 'loads.php'
      ),
      407 => array(
          'Layout não localizado',
          'O layout <b>' . $object . '</b> não foi localizada em: <span>' . $feactures . '</span>',
          '<li> Verificar na pasta ' . LAYOUT . 'modules' . DS . '</li>' .
          '<li> Verificar se o nome do módulo foi digitado corretamente no controller ' . App::$Controller . ' : ' . CONTROLLER . App::$controller . '_controller.php' .
          '<li> Verificar se o nome da lin foi digitada corretamente no arquivo: ' . LAYOUT . 'loads.php'
      ),
      410 => array(
          'Layout não localizado',
          'O layout <b>' . $object . '</b> não foi localizada em: <span>' . $feactures . '</span>')
  );
  
  if (App::$development)
    include(PATH . 'error' . DS . 'error.php');
  else {
    $url;
    $time;
    include PATH . 'error' . DS . '404';
  }
  exit();
}

/**
 * Seta uma variável 
 * @param type $name
 * @param type $var
 */
function set($name, $var) {
  App::$vars[$name] = $var;
}

/**
 * Aplica ações de debug se a variável App::$development = true
 */
function development_true() {
  if (App::$development) {
    echo '<div align="right" style="padding:4px 10px;margin:0;position:fixed;bottom:0;right:0;background:#5f7d77;z-index:999;color:#FFF">' .
    round(memory_get_usage(true)/1024, 2) . 'KB | ' .
    round(memory_get_peak_usage(true), 2) . 'KB |' .
    round(microtime(true) - $_SERVER['REQUEST_TIME'] , 3) . 's</div>';
  }
}


?>