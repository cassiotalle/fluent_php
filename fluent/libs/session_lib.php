<?php

/**
 * Classe LibSession faz a interface com sessões.
 */
class SessionLib {

  public static $used = false;
  
  /**
   * Seta um sesseção na variavel global $_SESSION['flash']
   * @access public
   * @param string $mensage
   */
  public function set_flash($mensage, $name = 'flash') {
    $_SESSION['flash'][$name] = $mensage;
  }
 
  /**
   * Verifica se uma Session existe
   * @param string $key : nome da session que será verificada
   * @return boolean
   */
  public function check($key) {
    if (isset($_SESSION[$key]))
      return true;
    return false;
  }

  /**
   * Retorna a variavel que foi setada pela função setFlash()
   * @return string
   */
  public function flash() {
    self::$used = true;
    if(check_array($_SESSION['flash'])){
      foreach (array_keys($_SESSION['flash']) as $type){
        return '<div class="notice '.$type.'" id="flash_'.$type.'">' . $_SESSION['flash'][$type] . '</div>';
      }
    }
  }

  /**
   * Destroi a session flash, esta funcão é utilizada para apagar a session flash antiga.
   */
  public function destructFlash() {
    if (isset($_SESSION['flash'])) {
      //mantem sessão flash em caso de redirecionamento
      //App::$reaload_flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
    }
  }
  
  function __destruct() {
    if(self::$used){
      $this->destructFlash();
    }
  }
}
?>