<?php

class Db {

  public static $conected;
  public static $conect_details = null;
  public static $data = array();
  public static $numRows;

  public static function connect() {
    $db_conect = Neon::decode_file(PATH . 'fluent' . DS . 'db_connect.neon');
    self::$conect_details = mysql_connect($db_conect['host'], $db_conect['user'], $db_conect['password']);

    if (self::$conect_details) {
      if (mysql_select_db($db_conect['db'], self::$conect_details)) {
        self::$conected = true;
      } else {
        echo mysql_error();
      }
    } else {
      echo mysql_error();
    }
  }

  public static function query($sql) {
    if (self::$data = mysql_query($sql)) {
      self::$numRows = mysql_affected_rows();
      if (self::$numRows == 0)
        return false;
      else
        return true;
    }
    else {
      echo mysql_error(self::$conect_details);
      throw new Exception('Insturção sql inconsistente: <b>' . $sql . '</b>');
    }
  }

  public static function getData() {
    if (self::$numRows) {
      while ($data[] = mysql_fetch_assoc(self::$data));
      if (self::$numRows == 1) {
        $data = $data[0];
      } else {
        array_pop($data);
      }
      return $data;
    }
    else
      return false;
  }

  public static function numRows() {
    return mysql_affected_rows();
  }

}

?>