<?php

/**
 * @author Cássio Talle <cassiotalle@gmail.com>
 * Classe para o carregamento dinâmico de arquivos css e js.
 * 
 * Os arquivos css e js são acessados por essa classe de três forma.
 * <b>1)</b>Arquivos que serão pré-carregados em toda a aplicação são definidos no arquivo /config/definitions.php da aeguinte forma:
 * <code>
 * App::$preload_css = array('scaffold','layout');
 * App::$preload_js = array('jquery','ui_jquery');
 * </code>
 *
 * <b>2)</b>Definindo arquivos através do controller:
 * <code>
 * // Arquivos que serão carregados em todas as actions do controller.
 * class HomeController extends Controller{
 * public $js = array('jquery');
 * public $css = array('tabelas');
 * ...
 * // Arquivos carregados em action específica
 * function index(){
 * // para substituir os arquivos já definidos pelo controller.
 * $this->css = array('style1','style1','style1');
 * // para adicionar arquivos
 * $this->asset->load_css('style1','style1','style1');
 * //ou
 * $this->css[]= 'style1';
 * $this->css[]= 'style2';
 * </code> 
 * <b>1)</b>Arquivos carregados pelo prório arquivo de layout
 * <code>
 * <html>
 *  <head>
  <?=$asset->load_css('arquivo1','pasta/arquivo2')?>
 * </code>
 */
class Asset {

  var $config;
  var $return_path = false;
  public $css = array();
  public $js = array();

  /**
   * Library Constructor
   * Verifica os arquivos que serão pré-caregados pelo sistema através das veriáveis App::$preload_css, App::$preload_js
   * @access private
   * @param string
   */
  function __construct() {
    $this->preload_css();
    $this->preload_js();
  }

  private function preload_css() {
    foreach (App::$preload_css as $css) {
      $this->css[] = $css;
    }
  }

  private function preload_js() {
    foreach (App::$preload_js as $js) {
      $this->js[] = $js;
    }
  }

  /**
   * Garrega os arquivos css mesclados e comprimidos.
   */
  public function css() {
    if (check_array($this->css)) {
      $this->css = array_unique($this->css);
      $files = concat_array($this->css, ",");
      return '<link rel="stylesheet" type="text/css" href="' . App::$link . 'webroot/assets/css.php?files=' . $files . '" media="screen" />' . "\n";
    }
  }

  /**
   * Gera dinamicamente um arquivo js mesclando e comprimindo todos os arquivos js que devem ser carregados.
   */
  public function js() {
    if (check_array($this->js)) {
      $this->js = array_unique($this->js);
      $files = concat_array($this->js);
      echo '<script type="text/javascript" src="' . App::$link . 'webroot/assets/js.php?files=' . $files . ' "></script>' . "\n";
    }
  }

  public function img($image, $alt = null, $atributes = null) {
    echo '<img alt="' . $alt . '" src="' . App::$link . 'webroot/assets/img/' . $image . '" ' . $atributes . ' />' . "\n";
  }

  public function img_db($image, $size = null, $alt = null, $atributes = null) {
    if (isset($size))
      echo '<img alt="' . $alt . '" src="' . App::$link . 'webroot/'.$image . '" ' . $atributes . ' />' . "\n";
    else
      echo '<img alt="' . $alt . '" src="' . App::$link . 'webroot/'.replace_firt($image, $size) . $image . '" ' . $atributes . ' />' . "\n";
  }

  /**
   * Adiciona um ou mais arquivos css que devem ser carregados.
   * <code>
   * Asset->load_css('estilo1','estilo2', 'estilo3'...);
   * </code>
   */
  public function load_css() {
    $total = func_num_args();
    $name = func_get_args();
    for ($i = 0; $total > $i; $i++) {
      $this->css[] = $name[$i];
    }
  }

  /**
   * Adiciona um ou mais arquivos js que devem ser carregados.
   * <code>
   * Asset->load_js('script1','script2', 'script3'...);
   * </code>
   */
  function load_js() {
    $total = func_num_args();
    $name = func_get_args();
    for ($i = 0; $total > $i; $i++) {
      $this->js[] = $name[$i];
    }
  }

  /**
   * @author Adam Griffiths
   * @param string
   * @param string
   * @param string
   *
   * Loads in an image.
   */
}