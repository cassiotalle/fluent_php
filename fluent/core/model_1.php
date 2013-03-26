<?php
include (CONFIG.'validate.php');
class Model  {

    public $data;

    public $fields;

    public $numFields;

    public $table;

    public function __construct() {

    }

    /**
     *
     * @param <type> $method
     * @param <type> $params
     */
    public function __call($method, $params) {
        $m = preg_split('/([A-Z][^A-Z]+)/', $method, -1 ,PREG_SPLIT_DELIM_CAPTURE |  PREG_SPLIT_NO_EMPTY);
        if(is_array($m)) {
            $id = strtolower($m[2]);
            if(in_array($id,$this->fields)) {
                if($m[0].$m[1] == 'selectBy') {

                }
                elseif($m[0].$m[1] == 'deleteBy') {

                }
                elseif($m[0].$m[1] == 'alterBy') {

                }
                elseif($m[0].$m[1] == 'selectAllBy') {

                }
                elseif($m[0].$m[1] == 'deleteAllBy') {

                }
                elseif($m[0].$m[1] == 'alterAllBy') {

                }
            }
            else {
                // campo inesistente em método $method;
            }
        }
        else {
            // método inesistente
        }

    }

    /**
     *
     * @param <type> $fields
     * @param <type> $where
     * @param <type> $arguments
     * @param <type> $foregin
     * @return <type>
     */
    public function select($fields, $where = null, $arguments = null) {
        if(is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        $pg = false;
        if(array_key_exists('Paginate',App::$obj)){
            $arguments = ' '.App::$obj['Paginate']->sql_limit;
            $pg = true;
        }
        if(isset($where)) {
            $where = ' WHERE '.$where;
        }
        $sql = 'SELECT SQL_CALC_FOUND_ROWS '.$fields.' FROM '.$this->table.$where.' '.$arguments;
        if($query = $this->query($sql,1)){
            if($pg){
                $n = $this->realNumQuery();
                App::$obj['Paginate']->listPages($n[0][0]);
            }
            return $this->formatLoadData($query);
        }
        else{
            return array();
        }
    }

    
    
    /**
     *
     * @param <type> $arguments
     * @param <type> $foregin
     * @return <type>
     */

    public function update(array $data, $where, $foregin = null) {
        if(App::$obj['Validate']->process($data)) {
            $k = array_keys(App::$obj['Validate']->data);
            $values = $this->filterByType(App::$obj['Validate']->data);
            $c = count($k);
            for($i=0;$i<$c;$i++) {
                if(array_key_exists($k[$i], App::$model)){
                    $values[$i] = $k[$i].'='.$values[$i];
                }
            }
            $sql = 'UPDATE '.$this->table.' SET '.concat_array($values).' WHERE '.$where.' ;';
            return Db::query($sql,1);
        }
        else {
            return false;
        }
    }

    /**
     *
     * @param array $data
     * @param <type> $foregin
     */
    public function add(array $data, $foregin = null) {
        if(App::$obj['Validate']->process($data)) {
            $k = array_keys(App::$obj['Validate']->data);
            $values = $this->filterByType(App::$obj['Validate']->data);
            $sql = 'INSERT into '.$this->table.' ('.concat_array($k).') VALUES ('.concat_array($values).');';
            return Db::query($sql);
        }
        else {
            return false;
        }

    }

    public function query($sql,$return=0) {
        if(Db::query($sql)) {
            if($return > 0) {
                $return = Db::getData($return);
            }
            return $return;
        }
        else {
            return false;
        }
    }

    public function realNumQuery() {
        Db::query('select found_rows()');
        return Db::getData(0);
    }

    public function lastId(){
        return mysql_insert_id();
    }

    public function describe() {
        $fidsldes = $this->query('describe '.$this->table,1);
        $this->numFields = Db::$numRows;
        $model = array();
        $array_model = false;
        // carrega model.ini
        if(is_file(PATH.'model'.DS.$this->table.'.ini')) {
            $model = parse_ini_file(PATH.'model'.DS.$this->table.'.ini',true);
            $array_model = check_array($model);
        }
        // mescla describe com model
        foreach($fidsldes as $f) {//
            $v = explode('(',$f['Type']);
            $f['Maxlen']=null;
            if(count($v)==2) {
                if($v[0]=='varchar')$f['Type']='string';
                $f['Maxlen']=substr($v[1], 0,-1);
            }
            $f['Title']=null;
            $f['Legend']=null;
            $f['Minlen']=null;
            if(array_key_exists($f['Field'], $model)) {
                $f = array_merge($f,$model[$f['Field']]);
            }
            App::$model[$f['Field']]= $f;
        }
    }
    private function filterByType(array $value) {
        foreach($value as $v) {
            if (is_string($v)) {
                $v = "'".$v."'";
            }
            elseif(is_null($v)) {
                $v = 'NULL';
            }
            elseif(is_bool($v)) {
                $v = $v ? 'TRUE' : 'FALSE';
            }
            $r[]=$v;
        }
        return $r;
    }

    // formatação de dados

    public function formatLoadData(array $data,$table=null) {
        $keys = array_keys($data[0]);
        $count = count($data);
        $confront = array();

        foreach($keys as $k) {
            if(array_key_exists($k, App::$model)) {
                if($r = $this->format(App::$model[$k]['Type']))
                $confront[$k] = array($k,$r);
            }
        }
        if(check_array($confront)){
            for($i=0;$i<$count;$i++){
                foreach ($confront as $j){
                    $data[$i][$j[0]] = $this->$j[1]($data[$i][$j[0]]);
                }
            }
        }
        return $data;
    }

    private function format($type) {
        switch($type) {
            case 'dollar':
                return 'reloadFormatDecimal';
                break;
            case 'real':
                return 'reloadFormatDecimal';
                break;
            case 'euro':
                return 'reloadFormatDecimal';
                break;
            case 'decimal':
                return 'reloadFormatDecimal';
                break;
            case 'date':
                return 'reloadFormatDate';
                break;
        }
        return false;
    }

    private function reloadFormatDecimal($value) {
        return number_format($value, 2, ',', '.');

    }

    private function reloadFormatDate($d) {
        $d = str_replace("-","",$d);
        return $d[6].$d[7]."/".$d[4].$d[5]."/".$d[0].$d[1].$d[2].$d[3];
    }
}
?>
