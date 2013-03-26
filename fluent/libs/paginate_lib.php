<?
class PaginateLib{

    public $set;
    public $rg_total;
    public $pg_total;
    public $pg_actual;
    public $interval;
    public $rg_limit;
    public $half;
    public $sql_limit;
    public $html;

    function limit($limit,$interval,$pagina_actual=1,$link= null) {
        if(is_null($link)){
            $link = App::$url.App::$controller.'/'.App::$action;
        }
        $this->interval=$interval;
        $this->link = $link;
        $this->rg_limit=$limit;
        $this->half=ceil($this->interval/2);
        $this->pg_actual=$pagina_actual;
        $i = $this->pg_actual*$this->rg_limit-$this->rg_limit;
        $this->sql_limit = "limit $i, $this->rg_limit";
    }

    function listPages($total) {
        $this->rg_total = $total;
        $this->pg_total = ceil($this->rg_total/$this->rg_limit);

        if($this->rg_total>$this->rg_limit) {
            // verifica se a p�gina actual � maior do que 1
            if($this->pg_actual > 1) {
                $this->html = "<span><a href=\"{$this->link}/1\">< <</a></span>";
                $n = $this->pg_actual-1;
                $this->html .= "<span><a href=\"{$this->link}/$n\"><</a></span>";
            }
            /*
	se pagina total maior do que interval
	ou
	pagina pagina actual menor do que metade do interval
            */
            if($this->pg_total<=$this->interval) {
                for($i=1;$i<=$this->pg_total;$i++) {
                    if($this->pg_actual != $i)$this->html .= "<span><a href=\"{$this->link}/$i\">$i</a></span>";
                    else $this->html .= "<span id='actual'>$i</span>";
                }
            }
            elseif($this->pg_actual<=$this->half) {
                for($i=1;$i<=$this->interval;$i++) {
                    if($this->pg_actual != $i)$this->html .= "<span><a href=\"{$this->link}/$i\">$i</a></span>";
                    else $this->html .= "<span id='actual'>$i</span>";
                }
            }
            /*se pagina actual maior que pagina pagina total - half interval*/
            elseif($this->pg_actual>($this->pg_total-$this->half)) {
                $i = $this->pg_total-$this->interval+1;
                for($i;$i<=$this->pg_total;$i++) {
                    if($this->pg_actual != $i)$this->html .= "<span><a href=\"{$this->link}/$i\">$i</a></span>";
                    else $this->html .= "<span id='actual'>$i</span>";
                }
            }
            else {
                $i = $this->pg_actual-($this->half-1);
                if($this->half*2==$this->interval)$final= $this->pg_actual+$this->half;
                else $final=$this->pg_actual+$this->half-1;
                for($i;$i<=$final;$i++) {
                    if($this->pg_actual != $i)$this->html .= "<span><a href=\"{$this->link}/$i\">$i</a></span>";
                    else $this->html .= "<span id='actual'>$i</span>";
                }
            }
            # atalho final de pagina��o
            if($this->pg_actual < $this->pg_total) {
                $n = $this->pg_actual+1;
                $this->html .= "<span><a href=\"{$this->link}/$n\">></a></span>";
                $this->html .= "<span><a href=\"{$this->link}/$this->pg_total\">> ></a></span>";
            }
        }
        $this->setInTemplate();
    }

    public function setInTemplate(){
        App::$obj['tpl']->set('paginate',$this->html);
    }
}
?>