<?php
/* 
 * Classe que faz a interface com arquivo de dados DBF.
 * Obs: Para esta classe funcionar o extension=php_dbase.dll deve estar
 * descomentado no php.ini.
 */
class DbfLib{

    /**
     * Arquivo dbf
     * @var string
     */
    public $file;

    /**
     * Contem os dados de cabeçado do arquivo dbf
     * @var array
     */
    public $header;

    /**
     * Dados garregados do banco de dados dbf.
     * @var array
     */
    public $data;
    
    /**
     * Conexão de com o bando de dados dbf
     * @var object
     */
    public $base;

    /**
     *
     * @var <type>
     */
    public $rows;
    public $nunFields;

    public $nunLoadFields;
    public $fields;

    public $sql;

    /**
     * Abre um arquivo DBF
     * @param string $arquivo : Arquivo dbf que será acessado, Ex: banco.dbf;
     * @return boolean
     */
    function openDbf($file){
        $this->file = WEBROOT.$file;
        if(@$this->base=dbase_open($this->file,2)){
            $this->rows=dbase_numrecords($this->base);
            $this->header=dbase_get_header_info($this->base);
            $i=0;
            foreach($this->header as $field){
                $this->fields[$i][] = $i;
                $this->fields[$i][] = $field['name'];
                $i++;
            }
            $this->nunFields=count($this->header);
            return true;
	}
	else return false;
    }

    function loadDbf($col=null,$assoc=false)
    {
        if(is_null($col)){
            for($i=0;$i<$this->nunFields;$i++){
                $col[$i]=$i;
            }
	}
        $this->nunLoadFields=count($col);
        echo $this->rows;
        if(is_array($col)){
            $f = $this->rows+1;
            for($i=1;$i<$f;$i++){
                $linha = dbase_get_record($this->base,$i);
		foreach ($col as $ind){
                    $this->data[$i][]=$linha[$ind];
		}
            }
	}
    }

    private function geraSql($tabela)
    {
        for($a=0;$a<$this->nunLoadFields;$a++){
            $separador[$a]=',';
        }
        $separador[$this->nunLoadFields-1]='';
        $sqldescribe = "describe ".$tabela;
        $this->base->sql($sqldescribe,0);

        if(($this->base->resultArray[0][0] == 'id') && ($this->base->resultArray[0][3] == 'PRI')) $init = "(null,";
        else $init = "(";

        $l =$this->rows ;

        for($i=1;$i<$l;$i++){
            $sql.= " insert into $tabela values ";

            if($i+1000< $l){
                $l_max = $i+1000;
            }
            else{
                $l_max = $l;
            }

            for ($i=$i;$i<$l_max;$i++){
                $sql.=$init;
                for($j=0;$j<$this->nunLoadFields;$j++) {
                    $sql.="'".rtrim($this->data[$i][$j])."'{$separador[$j]}";
                }
                $sql.='),';
            }
            $sql.=$init;

            for($j=0;$j<$this->nunLoadFields;$j++){
                $sql.="'".rtrim($this->data[$i][$j])."'{$separador[$j]}";
            }
            $sql.=');';
            $this->sql[]=$sql;
            $sql=null;
        }
    }

    private function executeSql($lista_sql)
    {
        foreach ($lista_sql as $sql){
            if(!$this->banco->sqlMysql($sql)){
                return false;
		exit;
            }
        }
	return true;
    }

    function converteParaMysql($tabela, $atualiza=false)
    {
        if(@$this->base = new DbMysql()){
            if($atualiza){
                $truncate = "truncate ".$tabela;
		$this->base->sql($truncate);
            }
            $error = false;
            $this->geraSql($tabela);
            if(is_array($this->sql))
            {
                foreach($this->sql as $query){
                    if(!$this->base->sql($query)){
                        $error = true;
                    }
                }
            }

            if($error){
                return false;
            }
            else{
                return true;
            }

            /* if($this->base->sql($this->geraSql($tabela))){

                echo "Tabela importada com sucesso";
                return true;
            }
            else {
                echo "erro ao importar tabela";
                return false;
            }*/
        }

	else echo "Tabela de banco de dados inexistente";
    }

    function __destruct(){
        if(isset($this->banco)){
            //dbase_close($this->banco);
	}
    }
}
?>
