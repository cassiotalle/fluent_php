<?php

class Db {

  public static $conected;
  public static $sql = null;
  public static $data = array();
  public static $numRows;
  public static $name = null;

  public static function connect() {
    $db_conect = Neon::decode_file(PATH . 'fluent' . DS . 'db_connect.neon');
    self::$sql = new mysqli($db_conect['host'], $db_conect['user'], $db_conect['password'], $db_conect['db']);
    self::$name = $db_conect['db'];
    if (@mysqli_connect()) {
          
      printf("Erro na conexão com o banco de dados: %s\n", mysqli_connect_error());
    }
  }

  public static function query($sql) {
    if (self::$data = self::$sql->query($sql)) {
      if (self::$sql->affected_rows == 0)
        return false;
      else
        return true;
    }
    else {
      printf("Sql inconsistente: %s\n", self::$sql->error);
    }
  }

  public static function getData($one=false) {
    
    if (self::$data->num_rows) {
      while ($data[] = self::$data->fetch_array(1));
      if ($one) {
        $data = $data[0];
      } else {
        array_pop($data);
      }
      self::$data->close();
      return $data;
    }
    else
      return false;
  }

  public static function describe($table){
    self::query('describe '.$table);
    return self::getData();
  }

  public static function numRows() {
    return mysql_affected_rows();
  }

}

?>