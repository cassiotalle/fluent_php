<?php

class Tpl {

  /**
   * O valores das variaveis a serem carregadas no template.
   * @var array
   */
  public $vars = array();

  /**
   * Variável que identifica se será utilizado algum layout para o template.
   * Se o valor padão 'null' for mantido nao será carregada nenhum tipo de
   * layout para este template.
   * @var String
   */
  public $tpl;

  /**
   * Lista de módulos partencentes ao layout.
   * @var array
   */
  public $modules = null;

  /**
   * Código xhtml com includes de js e css
   * @var string
   */
  public $loadHead = null;

  /**
   * Seta uma variável para ser carregada no template.
   * @param string $var
   * @param mixed $value
   */
  public function set($key, $value) {
    $this->vars[$key] = $value;
  }

  /**
   * Complila do o template e exeibe o resultado.
   */
  public function compile() {
    $this->setVars();
    $__kk = array_keys($this->vars);
    foreach ($__kk as $__kkn) {
      $$__kkn = $this->vars[$__kkn];
    }

    $__kk = array_keys(App::$obj['Libs']);
    foreach ($__kk as $__kkn) {
      if (ctype_upper($__kkn[0]))
        $$__kkn = App::$obj['Libs'][$__kkn];
    }

    unset($__kk);
    unset($__kkn);

    //Carrega template ou view
    if ($this->tpl) {
      include(LAYOUT . $this->tpl . '.php');
    } else {
      include($layout['main']);
    }
  }

  /**
   * Cria variáveis para serem utilizadas no template.
   */
  private function setVars() {
    if (check_array($this->modules)) {
      $modules = $this->loadModules();
    }
    
    //Carrega variáveis impultadas pela função set.
    if (check_array(App::$vars)) {
      $this->vars = array_merge(App::$vars, $this->vars);
    }
    $modules['main'] = VIEW . App::$controller . DS . App::$action . '.php';
    $this->set('layout', $modules);
    $this->set('url', App::$url);
    $this->set('link', App::$link);
    $this->set('partial', VIEW.App::$controller.DS.'_');
    $this->set('load_head', $this->loadHead);
    $this->set('controller', App::$controller);
    $this->set('action', App::$action);
    $this->set('asset', App::$obj['asset']);
    $this->set('validation', Validate::$error_list);
  }

  /**
   * Carrega módulos vinculados ao layout.
   */
  public function loadModules() {
    foreach ($this->modules as $m) {
      if (!is_file(LAYOUT . 'modules' . DS . $m . '.php')) {
        set_include_error(408, $m, LAYOUT . 'modulos' . DS . $m . '.php');
      } else {
        $j[$m] = LAYOUT . 'modules' . DS . $m . '.php';
      }
    }
    return $j;
  }

}

?>