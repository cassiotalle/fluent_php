<?php

include (CORE . 'db.php');

class Model {

  public $_table;
  public $_fields = ' * ';
  private $_sql;
  private $_action = 'SELECT';
  private $_where = null;
  private $_limit = null;
  private $_group = null;
  private $_order = null;
  private $_data = array();

  function __construct() {
    Db::connect();
  }

  public function __get($name) {
    $this->_table = $name;
    return $this;
  }

  /**
   * Executa instruções sql de três formas:
   * 1)Digitando diretamente a instrução
   * <code>
   * $this->data->exec('select * from usuarios where idade > 18;');
   * </code>
   * 2)Instrução sql com variáveis interpostas.
   * <code>
   * $this->data->exec('select ? from ? whre ?',$campos, $tabela, $where);
   * </code>
   * 3)Para concluir uma operação encadeada por outras funções.
   * <code>
   * $this->data->usuarios->filds('*')->where('idade > 18')->order('nome desc')->exec();
   * </code>
   * 
   */
  public function exec() {
    $n = func_num_args();
    $arg = func_get_args();
    $this->_group = concat_array(func_get_args());
    if ($n == 0) {
      switch ($this->_action) {
        case 'SELECT':
          return $this->e_select();
          break;
        case 'CREATE':
          return $this->e_create();
          break;
        case 'DELETE':
          return $this->e_delete();
          break;
        case 'UPDATE':
          return $this->e_update();
          break;
      }
    } elseif ($n == 1) {
      $this->_sql = $arg[0];
    } elseif ($n > 1) {
      $i = 1;
      for ($i = 1; $i < $n; $i++) {
        $arg[0] = replace_firt($arg[$i], $arg[0]);
      }
    }
    $this->reset();
  }

  public function fields($value) {
    $this->_fields = " $value ";
    return $this;
  }

  public function select(){
    $this->_action = 'SELECT';
    return $this;
  }
  
  public function update($data) {
    $this->_action = 'UPDATE';
    $this->_data = $data;
    return $this;
  }

  public function create($data) {
    $this->_action = 'CREATE';
    $this->_data = $data;
    return $this;
  }

  public function delete() {
    $this->_action = 'DELETE';
    return $this;
  }

  public function where($value) {
    $this->_where = ' WHERE ' . $value;
    return $this;
  }

  public function group($value) {
    $this->_group = ' GROUP BY ' . $value;
    return $this;
  }

  public function limit($value) {
    $this->_limit = ' LIMIT ' . $value . ' ';
    return $this;
  }

  public function order($value) {
    $this->_order = ' ORDER BY ' . $value;
    return $this;
  }

  private function reset() {
    $this->_fields = ' * ';
    $this->_action = 'SELECT';
    $this->_where = null;
    $this->_limit = null;
    $this->_group = null;
    $this->_order = null;
    $this->_data = array();
  }

  /**
   * Cria dinâmicamente funções para executar operações SQL.
   * Ex 1: selecionar usando campo específico como parâmentro.
   * <code>
   * // Retorna todos os usuários com idade maior do que 10
   * $this->data->usuario->selectByIdade('>10');
   * // select * from usuario where idade > 10;
   * </code>
   * 
   * Ex 2: deletar
   * <code>
   * // Deleta o usuário com id=99
   * $this->data->usuario->deleteById('99');
   * // delete from usuario where id = 99;
   * </code>
   * 
   * @param type $method
   * @param type $params
   */
  public function __call($method, $params) {
    $m = preg_split('/([A-Z][^A-Z]+)/', $method, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if (is_array($m))
      $id = strtolower($m[2]);
    if (!preg_match('/^<|>|!|like|=/', $params[0])) {
      $params[0] = '= ' . $params[0];
    }
    if ($m[0] . $m[1] == 'selectBy') {
      $this->_where = " WHERE {$id} {$params[0]} ";
      return $this->e_select();
    } elseif ($m[0] . $m[1] == 'deleteBy') {
      $this->_where = " WHERE {$id} {$params[0]} ";
      return $this->e_delete();
    } elseif ($m[count($m) - 1] == 'id') {
      $this->_where = " WHERE " . strtolower(concat_array($m, '_')) . " {$params[0]} ";
      return $this->e_select(true);
    }
  }

  private function e_select($one = false) {
    if (Db::query($this->_action . $this->_fields . 'FROM ' . $this->_table . $this->_where . $this->_group . $this->_order . $this->_limit)) {
      return Db::getData($one);
    } else {
      return false;
    }
  }

  private function e_delete() {
    set_model($this->_table);
    $keys = array_keys(App::$model[$this->_table]);
    $files = null;
    foreach ($keys as $k){
      if (array_key_exists('type', App::$model[$this->_table][$k])) {
          if (App::$model[$this->_table][$k]['type'] == 'file' || App::$model[$this->_table][$k]['type'] == 'img') {
            $files[$k] = App::$model[$this->_table][$k]['thumb'];
          }
      }
    }
    if(is_array($files)){
      $keys = array_keys($files);
      $this->fields(concat_array($keys));
      $file_link = $this->e_select(true);
      foreach ($keys as $k){
        if(is_array($files[$k])){
          $j = array_keys($files);
          foreach ($j as $l){
            @unlink(replace_firt($l,$file_link[$k]));
          }
        }
        else{
          @unlink($file_link[$k]);
        }
      }
    }
    $this->_sql = 'DELETE FROM ' . $this->_table . $this->_where;
    return Db::query($this->_sql);
  }

  private function e_create() {
    if (App::$obj['Validate']->process($this->_table, $this->_data)) {
      $k = array_keys(App::$obj['Validate']->data);
      $v = App::$obj['Validate']->data;
      $sql = 'INSERT INTO ' . Db::$name . '.' . $this->_table . ' (' . concat_array($k) . ') VALUES (' . concat_array($v, ",") . ');';
      Db::query($sql);
      $id = Db::$sql->insert_id;
      if (check_array(App::$obj['Validate']->files) && $id) {
        App::$obj['upload']->upFile($id, $this->_table, App::$obj['Validate']->files);
        $this->update(App::$obj['upload']->file)->where('id = ' . $id)->exec();
      }
      return $id;
    } else {
      return false;
    }
  }

  private function e_update() {
    if (App::$obj['Validate']->process($this->_table, $this->_data, true)) {
      $k = array_keys(App::$obj['Validate']->data);
      $v = App::$obj['Validate']->data;
      $b = '';
      foreach ($k as $a) {
        $b[] = $a . ' = ' . $v[$a] . ' ';
      }
      $sql = 'UPDATE ' . $this->_table . ' SET ' . concat_array($b) . ' ' . $this->_where;
      $exec = Db::query($sql);
      if (is_array($_FILES[$this->_table]['name'])) {
        $k_f = array_keys($_FILES[$this->_table]['name']);
        $files = null;
        foreach ($k_f as $f) {
          if (strlen($_FILES[$this->_table]['name'][$f]))
            $files[] = $f;
        }
        if (isset($files)) {
          $this->fields('id');
          $this->select();
          $field = $this->e_select(true);
          App::setIstance('upload','core')->upFile($field['id'], $this->_table, $files);
        }
      }

      return $exec;
    } else {
      return false;
    }
  }

}

?>
