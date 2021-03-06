<?

class Validate {

  public static $error_list = array();
  private $add = array();
  public $data = array();
  private $validate_user;
  private $table;
  public $files = array();

  function __construct() {
    $this->validate_user = Neon::decode_file(PATH . 'fluent' . DS . 'validate.neon');
  }

  /**
   * Grupo de tipo de dados
   */

  /**
   * Valida e-mail
   * @param type $field
   * @param type $email
   * @param type $message
   * @return boolean
   */
  public function email($field, $email, $message = null) {
    $pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
    if (preg_match($pattern, $email)) {
      return true;
    } else {
      $this->set_message_error('email', $field, $message);
      return false;
    }
  }

  /**
   * Valida url
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function url($field, $value, $message = null) {
    if (preg_match("/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/i", $value)) {
      return true;
    } else {
      $this->set_message_error('url', $field, $message);
      return false;
    }
  }

  /**
   * Verifica se variável é um número inteiro
   * @param type $field
   * @param type $number
   * @param type $message
   * @return boolean
   */
  public function int($field, $number, $message = null) {
    if (is_int($number)) {
      return true;
    } else {
      $this->set_message_error('int', $field, $message);
      return false;
    }
  }

  /**
   * Verifica se a variável é um número decimal
   * @param type $field
   * @param type $number
   * @param type $message
   * @return boolean
   */
  public function decimal($field, $number, $message = null) {
    if (preg_match('!^\d+.\d\d$!', $number)) {
      return true;
    } else {
      $this->set_message_error('decimal', $field, $message);
      return false;
    }
  }

  /**
   * Faz a validação de um número de CPF de acordo com o seu dígito verificador.
   * @param type $field
   * @param type $cpf
   * @param type $message
   * @return boolean
   */
  public function cpf($field, $cpf, $message = null) {
    $sinais = array(".", "-");
    $cpf = str_replace($sinais, "", $cpf);
    $cpf_validar = substr($cpf, 0, 9);
    $soma = 0;
    $n = 11;

    for ($i = 0; $i <= 9; $i++) {
      $n = $n - 1;
      $soma = $soma + (substr($cpf_validar, $i, 1) * $n);
    };
    $resto = $soma % 11;
    if ($resto < 2) {
      $cpf_validar = $cpf_validar . "0";
    } else {
      $cpf_validar = $cpf_validar . (11 - $resto);
    };
    //Segunda parte da validação do CPF
    $soma = 0;
    $n = 12;
    for ($i = 0; $i <= 10; $i++) {
      $n = $n - 1;
      $soma = $soma + (substr($cpf_validar, $i, 1) * $n);
    };
    $resto = $soma % 11;
    if ($resto < 2) {
      $cpf_validar = $cpf_validar . "0";
    } else {
      $cpf_validar = $cpf_validar . (11 - $resto);
    }
    if ($cpf_validar == $cpf) {
      return true;
    } else {
      $this->set_message_error('cpf', $field, $message);
      return false;
    };
  }

  /**
   * Verifica se valo é uma data válida
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function date($field, $value, $message = null) {
    $m = (int) $value[4] . $value[5];
    $d = (int) $value[6] . $value[7];
    $y = (int) $value[0] . $value[1] . $value[2] . $value[3];
    if (checkdate($m, $d, $y)) {
      return true;
    } else {
      $this->set_message_error('date', $field, $message);
      return false;
    }
  }

  public function password($field, $value, $message = null) {
    if (isset($value)) {
      return true;
    } else {
      $this->set_message_error('password', $field, $message);
      return false;
    }
  }

  /**
   * Faz a validação do valor gerado pela imagem captcha.
   * @param type $value
   * @param type $message
   * @return boolean
   */
  function captcha($value, $message = 'Código de segurança inválido.') {
    if ($_SESSION['captcha'] == $value) {
      unset($_SESSION['captcha']);
      return true;
    } else {
      unset($_SESSION['captcha']);
      self::$error_list['captcha'] = $message;
      return false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function equal($field, $value, $value2, $message = null) {
    if ($value == $value2) {
      return true;
    } else {
      $this->set_message_error('equal', $field, $message, $value2);
      return false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $message
   * @param type $table
   * @return boolean
   */
  public function unique($field, $value, $value2, $message = null) {
    Db::query('SELECT ' . $field . ' FROM ' . $this->table . ' where ' . $field . ' = "' . $value . '";');
    if (!Db::numRows()) {
      return true;
    } else {
      $this->set_message_error('unique', $field, $message);
      false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function max($field, $value, $value2, $message = null) {
    if ($value > $value2) {
      $this->set_message_error('max', $field, $message, $value2);
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function min($field, $value, $value2, $message = null) {
    if ($value < $value2) {
      return true;
    } else {
      $this->set_message_error($field, $message);
      return false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function not_null($field, $value) {
    if (strlen($value) < 1 || is_null($value)) {
      $this->set_message_error('not_null', $field, $this->validate_user['validation_mesages']['not_null']);
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $max
   * @return boolean
   */
  public function max_len($field, $value, $max, $message = null) {
    $len = strlen($value);
    if ($len > $max) {
      $this->set_message_error('max_len', $field, $message, $max);
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $min
   * @return boolean
   */
  public function min_len($field, $value, $min, $message = null) {
    $len = strlen($value);
    if ($len < $min) {
      $this->set_message_error('min_len', $field, $message, $min);
      return false;
    } else {
      return true;
    }
  }

  private function set_message_error($type, $field, $message, $value = null) {
    if (isset($message)) {
      $message = replace_firt($value, $this->validate_user['validation_mesages'][$type]);
    } else {
      $message = $this->validate_user['validation_mensages'][$type];
    }
    self::$error_list[$field] = $message;
  }

  public function process($table, $data, $update = false) {
    set_model($table);
    $this->data = array();
    $this->table = $table;
    $valid = true;
    if (App::$model[$table]) {
      if ($update) {
        unset($data['id']);
        $keys = array_keys($data);
      } else {
        $keys = array_keys(App::$model[$table]);
      }
      foreach ($keys as $k) {
        if(App::$model[$table][$k]['not_null'] == true && !$this->not_null($k, $data[$k])) {continue;}
        // Chama validação de tipo de dado
        if (array_key_exists('type', App::$model[$table][$k])) {
          // Formata dados
          if (App::$model[$table][$k]['type'] == 'file' || App::$model[$table][$k]['type'] == 'img') {
            if (!$update) {
              $this->files[] = $k;
              $this->data[$k] = 'NULL';
            } elseif (isset($data[$k])) {
              $this->data[$k] = "'$data[$k]'";
            }
            continue;
          } elseif (array_key_exists($k, $data)) {
            $this->data[$k] = "'" . $this->filter($data[$k], App::$model[$table][$k]['type']) . "'";
          } else {
            $this->data[$k] = 'NULL';
          }
          $type = App::$model[$table][$k]['type'];
          $valid = $this->$type($k, $this->data[$k]);
        } else {
          $this->data[$k] = "'" . $data[$k] . "'";
        }

        if ($valid && array_key_exists('validate', App::$model[$table][$k]) && check_array(App::$model[$table][$k]['validate'])) {
          $kvs = array_keys(App::$model[$table][$k]['validate']);
          foreach ($kvs as $kv) {
            $message = true;
            if (is_array(App::$model[$table][$k]['validate'][$kv]) && count(App::$model[$table][$k]['validate'][$kv]) == 2) {
              $message = App::$model[$table][$k]['validate'][$kv][1];
              App::$model[$table][$k]['validate'][$kv] = App::$model[$table][$k]['validate'][$kv][0];
            }
            $this->$kv($k, $data[$k], App::$model[$table][$k]['validate'][$kv], $message);
          }
        }
        $valid = true;
      }
    }
    $this->files();
    if (check_array(self::$error_list)) {
      return false;
    } else {
      return true;
    }
  }

  private function filter($value, $type) {
    switch ($type) {
      case 'date':
        $p = explode("/", $value);
        if (count($p) == 3) {
          $value = $p[2] . $p[1] . $p[0];
        }
        break;
      case 'decimal':
        $value = moeda($value);
        break;
      case 'euro':
        $value = moeda($value);
        break;
      case 'real':
        $value = moeda($value);
        break;
      case 'dollar':
        $value = moeda($value);
        break;
      case 'password':
        if (strlen($value) > 0) {
          $value = convert_pass($value);
        }
        break;
    }
    return $value;
  }

  public function add($type, $field, $value, $msg = null) {
    $this->add[$field] = array($type, $value, $msg);
  }

  public function files() {
    if (check_array($this->files)) {
      $update = App::setIstance('upload', 'core');
      $update->process($this->table, $this->files);
    }
  }

}

?>