<?php

class AuthLib {

  public $message;

  /**
   * Tipo de permissão de acesso do usuário
   * @var <type> 
   */
  public $accss;

  public function __construct() {
    //$this->testLogin();
  }

  /**
   * Faz a validação do usuário e senha.
   * @param string $user
   * @param string $pass
   * @return boolean
   */
  public function login($fieldUser, $user, $fieldPass, $pass, $fields = null) {
    $user = trim($user);
    $pass = convert_pass($pass);

    $sql = 'select id, ' . $fieldUser;
    $f = false;
    if (is_array($fields)) {
      $f = true;
      foreach ($fields as $f) {
        $sql.=',' . $f;
      }
    }
    if (!Db::$conected) {
      Db::connect();
    }
    $query = $sql . ' from usuario where ' . $fieldUser . '=\'' . $user . '\' and ' . $fieldPass . ' = \'' . $pass . '\'';
    if (Db::query($query, 1)) {
      $this->logout();
      $r = Db::getData(1);
      $this->id = $r[0]['id'];
      $this->user = $r[0][$fieldUser];
      $_SESSION['auth']['id'] = $this->id;
      $_SESSION['auth'][$fieldUser] = $this->user;

      if ($f) {
        foreach ($fields as $f) {
          $_SESSION['auth'][$f] = $r[0][$f];
        }
      }
      set_flash('Login realizado com sucesso.','success');
      if(isset($_SESSION['auth_url'])){
        $a = $_SESSION['auth_url'];
        unset($_SESSION['auth_url']);
        redirect($a);
      }
      else{
        redirect(App::$auth_url);
      }
    } else {
      set_flash('Usuário ou senha inválidos','alert');
      return false;
    }
  }

  /**
   * Verifica se o usuário está logado, caso não esteja ele é redirecionado
   * para a página de login que é definida no arquivo routers.php
   */
  public function test() {
    if (!isset($_SESSION['auth']['id']) && !isset($_SESSION['auth']['usuario'])) {
      $_SESSION['auth_url'] = App::$url;
      redirect(App::$login_url);
    } else {
      return true;
    }
  }

  /**
   * Destroi a SESSION auth, forçando assim o logof do usuário.
   */
  public function logout() {
    unset($_SESSION['auth']);
  }
  
  public function exists(){
    if (isset($_SESSION['auth']['id']) && !isset($_SESSION['auth']['usuario'])) {
      return $_SESSION['auth'];
    } else {
      return false;
    }
  }

}

?>