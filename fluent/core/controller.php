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
  protected $tpl = null;

  public function main() {
    App::setIstance('Validate', 'core');
    
    if (isset($_SESSION['_error_list'])) {
      Validate::$error_list = $_SESSION['_error_list'];
    }
    // Carrega libs no controller
    App::$defealt_load_libs = array_unique(array_merge($this->libs, App::$defealt_load_libs));
    foreach (App::$defealt_load_libs as $ln) {
      $this->$ln = App::setIstance($ln, 'Lib');
    }

    // Carrega valiaveis
    $this->post = $_POST;
    $this->action = App::$action;

    $this->beforeRender();

    //Executa o conteúdo da action
    $this->{App::$action}();

    //Verifica se a view
    if (!is_file(VIEW . App::$controller . DS . App::$action . '.php')) {
      echo "necesita da view:" . VIEW . App::$controller . DS . App::$action . '.php';
      include(SITE . '404.php');
      exit();
    }

    if (!isset($this->tpl)) {
      $this->tpl = App::$layout;
    }

    if ($this->tpl) {
      //verifica se o layout existe
      if (($this->tpl != null) && (!is_file(VIEW . App::$controller . DS . App::$action . '.php'))) {
        include(SITE . '404.php');
        exit();
      }
      include(CORE . 'tpl.php');

      $Tpl = new Tpl();
      //carrega modulos
      $modules = Neon::decode(file_get_contents(LAYOUT . 'modules.neon'));
      if (($modules) && (array_key_exists($this->tpl, $modules['layout_modules']))) {
        $Tpl->modules = $modules['layout_modules'][$this->tpl];
        // /taz os js e javascripts de cada módulo
        foreach ($Tpl->modules as $m) {
          if (array_key_exists($m, $modules['modules_dependences'])) {
            if (array_key_exists('libs', $modules['modules_dependences'][$m])) {
              App::$defealt_load_libs = array_unique(array_merge(App::$defealt_load_libs, $modules['modules_dependences'][$m]['libs']));
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

      $Tpl->tpl = $this->tpl;
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
      $Tpl->compile();
      $this->afterRender();
    } else {
      // sem template;
    }
  }

  /**
   * Esta função é carregada antes que o rederização do site seja feita.
   */
  protected function beforeRender() {
    
  }

  /**
   * Esta função é carregada no final do renderização do site.
   */
  protected function afterRender() {
    
  }

  public function loadLib() {
    $total = func_num_args();
    $names = func_get_args();
    for ($i = 0; $total > $i; $i++) {
      $name = ucfirst($names[$i]);
      $this->{$name} = App::setIstance($name, 'Lib');
    }
  }

}

?>