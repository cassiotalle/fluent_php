<?php

/**
 * @author Cássio Talle <cassiotalle@gmail.com>
 * Registra variáveis básicas de acesso clobal do sistema do aplicativo
 */
class App {

  /**
   * Indica de o ambiente é de desenvolvimento ou de produção.
   */
  public static $development = false;

  /**
   * Email do desenvolvedor do sistema, caso o memo deseje receber notícias
   * relatórios de inconsistências do sistema no modo de produção.
   * @var string
   */
  public static $email_administrator = null;

  /**
   * Variavel de securaça para criptografia
   * @var string
   */
  public static $salt = null;

  /**
   * Url padrão para realização de login, utilizada apenas quando a Lib Auth é
   *  requisitada no sistema
   * @var string
   */
  public static $login_url = '/';

  
  /**
   * Url de redirecionamento após o login.
   */
  public static $auth_url = '/';

    /**
   * Controller e Action padrão do sistema, equvalente ao index. Caso o action 
   * não seja informado o sistema entende que o action padrão é o index.
   * @var array('Nome do Controller','Nome da Action')
   */
  public static $router_defealt = array('home');

  /**
   * Libs para serem carregadas por padrão em todo o sistema
   * @var array
   */
  public static $defealt_load_libs = array();

  /**
   * Mascara de entrada
   */
  public static $mask = array();

  /**
   * Rotas para renomear cons controller e as actions
   * @var array
   */
  public static $routers = array();

  /**
   * Rotas administrativas para controller e actions.
   * @var array
   */
  public static $prefix_router = array();

  /**
   * Defina time zone apara a aplicação
   * @var type 
   */
  public static $time_zone = "America/Sao_Paulo";

  /**
   * Nome do controller atual com letra maiúscula.
   * @var type 
   */
  public static $Controller;

  /**
   * Nome do controller com a primeira letra minúscula.
   * @var string
   */
  public static $controller;

  /**
   * Nome da action
   * @var string
   */
  public static $action;

  /**
   * url base do sistema
   * @var string
   */
  public static $url;

  /**
   * Link completo atual da página.
   * @var string
   */
  public static $link;

  /**
   * Data que retornada depois de um processo de consulta ou validação
   */
  public static $data = array();

  /**
   * Condições apresentadas na url, Ex: localhost/controller/action/confição1/
   * condição2/condição3
   * @var string
   */
  public static $conditions = array();

  /**
   * Lista de objetos criados pelo sistema, que podem ser acessados a
   * qualquer momento.
   * @var array
   */
  public static $obj = array();

  /**
   * Indica o subdiretório dentro do domínio ondem se esncontra a raiz do
   * aplicativo utilizando "/" em caso de mais de um subdiretório.
   * @var string
   */
  public static $sub_dir = null;

  /*
   * Guarda o flash em caso de redirecionamento
   */
  public static $reaload_flash = null;

  /**
   * Recebe valores imputados através da função set.
   * @var array 
   */
  public static $vars = array();

  /**
   * Arquivos de css pré-carregados em todo o sistema.
   * @var array 
   */
  public static $preload_css = array('tabela', 'scaffold', 'reset');

  /**
   * Arquivos de css pré-carregados em todo o sistema.
   * @var array 
   */
  public static $preload_js = array('query');

  /**
   * Layout padrão do sistema, se false o layout não será carregado.
   * @var array 
   */
  public static $layout = false;

  /**
   * Techo de código pra ser carregado o hearde da página
   * @var array 
   */
  public static $head = array();
  
    /**
   * Techo de código pra ser carregado o hearde da página
   * @var array 
   */
  public static $model = array();

  /**
   * Cria um objeto usando o design patner Singleton
   * @param $string $name : Nome do objeto
   * @param $string $type : Tipo do objeto
   * @return instance
   */
  public static function setIstance($name, $type = null) {

    if ($type == 'Lib') {
      if (isset(self::$obj[$name])) {
        return self::$obj[$name];
      } else {
        $file = strtolower($name) . '_' . strtolower($type) . '.php';
        include LIBS . $file;
        $class = $name . $type;
        return self::$obj['Libs'][$name] = new $class;
      }
    }

    if (isset(self::$obj[$name])) {
      return self::$obj[$name];
    } else {
      $file = strtolower($name) . '_' . strtolower($type) . '.php';
      if ($name == 'controller') {
        $class = self::$Controller . 'Controller';
      } elseif ($type == 'core') {
        include CORE . strtolower($name) . '.php';
        $class = $name;
      }
      self::$obj[$name] = new $class;
      return self::$obj[$name];
    }
  }

  /**
   * Cria uma nova instância para uma classe mesmo que ela já tenha sido
   * criada anteriormente.
   * @param string $class : Nome da classe
   * @param string $var : Identificador para a nova instância
   */
  public static function newIstance($class, $var) {
    self::$obj[$var] = new $class;
  }

}
?>