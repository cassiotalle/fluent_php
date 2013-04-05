<?php

/**
 * Classe de controlador
 * @author Cássio Talle e silva
 */
class Controller {

  /**
   * Lista de obiblitecas que serão carreganas na aplicação.
   * @var array
   */
  protected $libs = array();

  /**
   * Lista de css para serem carregados pelo controller
   * @var array Description
   */
  protected $css = array();

  /**
   * Lista de js para serem carregados pelo controller
   * @var array Description
   */
  protected $js = array();

  /**
   * Verifica se a abstração de banco de dados será carregada.
   * @var boolean
   */
  public $table = 'default';
  public $data;

  /**
   * Identifica o layout que será carregado para o aplicação, caso vo valor
   * null não será nenhum layout para esta aplicação.
   * Se o valor desta variável não for alterado por padrão ela buscara o default
   * layout "basic".
   * @var string
   */
  protected $tpl = 'default';

  public function main() {
    include(CORE . 'tpl.php');

    // Carrega a classe de banco de dados.
    include (CORE . 'db.php');
    include (CORE . 'model.php');
    Db::connect();
    App::$obj['model'] = new Model();
    App::setIstance('Validate', 'core');
    if(check_array($_SESSION['_error_list'])){
      Validate::$error_list = $_SESSION['_error_list'];
    }
    $Tpl = new Tpl();


    if ($this->tpl) {
      //carrega modulos
      $modules = Neon::decode(file_get_contents(LAYOUT . 'modules.neon'));
      if (array_key_exists($this->tpl, $modules['layout_modules'])) {
        $Tpl->modules = $modules['layout_modules'][$this->tpl];
        // /taz os js e javascripts de cada módulo
        foreach ($Tpl->modules as $m) {
          if (array_key_exists($m, $modules['modules_dependences'])) {
            if (array_key_exists('libs', $modules['modules_dependences'][$m])) {
              $this->libs = array_merge($this->libs, $modules['modules_dependences'][$m]['libs']);
              $this->libs = array_unique(array_merge($this->libs, App::$defealt_load_libs));
            }
            if (array_key_exists('css', $modules['modules_dependences'][$m])) {
              $this->css = array_merge($this->css, $modules['modules_dependences'][$m]['css']);
            }
            if (array_key_exists('js', $modules['modules_dependences'][$m])) {
              $this->js = array_merge($this->js, $modules['modules_dependences'][$m]['js']);
            }
          }
        }
      }
    }

    if (!empty($this->libs)) {
      foreach ($this->libs as $l) {
        $$l = App::setIstance($l, 'Lib');
      }
    }
    //Carrega libs no controller
    $n_obj = array_keys(App::$obj);
    unset($n_obj[0]);
    unset($n_obj[1]);
    foreach ($n_obj as $o) {
      $this->{$o} = App::$obj[$o];
    }
    $this->post = $_POST;
    
    //Executa o conteúdo da action
    $action = App::$action;
    $this->$action();

    $Tpl->tpl = $this->tpl;
    
    //Verifica se a view
    if (!is_file(VIEW . App::$controller . DS . App::$action . '.php')) {
      include(SITE . '404.php');
      exit();
    }

    //verifica se o layout existe
    if (($this->tpl =! null) && (!is_file(VIEW . App::$controller . DS . App::$action . '.php'))) {
      include(SITE . '404.php');
      exit();
    }

    $this->beforeRender();

    // Parapara arquivos de CSS e JS
    include CORE . 'asset.php';
    $Asset = App::$obj['asset'] = new Asset();

    if (check_array($this->css)) {
      foreach ($this->css as $css) {
        if (is_array($css)) {
          
        } else {
          $Asset->css[] = $css;
        }
      }
    }
    $Asset->js = array_merge($this->js, $Asset->js);

    //compila template
    if ($this->tpl != null) {
      $Tpl->compile();
    }
    $this->afterRender();
  }

  /**
   * Esta função é carregada antes que o rederização do site seja feita.
   */
  private function beforeRender() {
    
  }

  /**
   * Esta função é carregada no final do renderização do site.
   */
  private function afterRender() {
    
  }

  public function load_lib() {
    $total = func_num_args();
    $names = func_get_args();
    for ($i = 0; $total > $i; $i++) {
      $name = ucfirst($names[$i]);
      $this->{$name} = App::setIstance($name, 'Lib');
    }
  }

}

?>