<?php
include "SimpleDigir.php" ;

class DigirQuery {

    public $sql = "";
    public $fields = array();
    public $from = array();
    public $where = array();
    
    private function __construct($sql){
        $sql = str_replace('\"',"'",$sql);
        $sql = str_replace('"',"'",$sql);
        $sql = str_replace('`',"'",$sql);
        $sql = str_replace('´',"'",$sql);
        $this->sql = $sql;
        $this->fields = $this->parseFields($sql);
        $this->from = $this->parseFrom($sql);
        $this->where = $this->parseWhere($sql);
    }

    public function getResult() {
        $results = resultsay();
        foreach($this->from() as $url=>$resource) {
            if(is_int($url)) {
               $results[] = SimpleDigir::create($resource)->setResource("*")->getResult();
            } else {
                $q = SimpleDigir::create($url)->setResource($resource);
                $filters = $this->where();
                if(isset($filters['equals'])) {
                    foreach($filters['equals'] as $field=>$value) {
                        $q->addFilter($field,"equals",$value);
                    }
                }
                if(isset($filters['like'])) {
                    foreach($filters['like'] as $field=>$value) {
                        $q->addFilter($field,"like",$value);
                    }
                }
                $results[] = $q->getResult();
            }
        }
        $records = array();
        foreach($results as $result) {
            foreach($result as $record) {
                $records[] = $record;
            }
        }
        $response = array() ;
        foreach($records as $rec) {
            $mappings = $this->fields();
            $item = new StdClass ;
            foreach($mappings as $field=>$map) {
                $item->$field = $rec[$map];
            }
            $response[] = $item;
        }
        return $response;
    }

    public function parseFields($sql) {
        $fields = array();
        if(preg_match("@^SELECT (.*) FROM@i",$sql,$reg)) {
            if($reg[1] != "*")  {
                $parts = explode(",",$reg[1]);
                foreach($parts as $fd) {
                    $field = trim(str_replace("'","",$fd));
                    if(preg_match("@([\w\d]+)( as ([\w\d]+))?@",$fd,$reg)){
                        if(isset($reg[3])) {
                            $fields[trim($reg[1])]=trim($reg[3]);
                        } else {
                            $fields[trim($reg[1])]=trim($reg[1]);
                        }
                    }
                }

            }
        }
        return $fields ;
    }

    public function parseFrom($sql) {
        $from = array();
        if(preg_match('@FROM ([^ ]+)( WHERE.*)?$@i',$sql,$reg)){
            $parts = explode(",",$reg[1]);
            foreach($parts as $part) {
                if(preg_match("@^'([^']+)'.'([^']+)'$@",$part,$pair)){
                    $url = trim($pair[1]);
                    if(!isset($from[$url])) $from[$url] = array();
                    $from[$url][] = trim($pair[2]);
                } else if(preg_match("@^'([^']+)'$@",$part,$pair)){
                    $url = trim($pair[1]);
                    $from[] = $url;
                }
            }
        }
        return $from ;
    }

    public function parseWhere($sql) {
        $where = array();
        if(preg_match("@WHERE (.*)\$@i",$sql,$reg)) {
            $combo = str_replace(",","AND",$reg[1]);
            if(preg_match_all("@'?([^= ]+)'? *(=|like) *'([^']+)'( AND)?@i",$combo,$parts)) {
                foreach($parts[0] as $k=>$v)  {
                    if($parts[2][$k] == "=") {
                        $op = "equals";
                    } else {
                        $op = "like";
                    }
                    $where[$op][trim($parts[1][$k])] = trim($parts[3][$k]);
                }
            }
        }
        return $where;
    }

    public function fields() {
        return $this->fields ;
    }

    public function from() {
        return $this->from;
    }

    public function where() {
        return $this->where ;
    }

    static public function create($sql) {
        return new DigirQuery($sql);
        $sql = trim($sql);
        $regex = "@^SELECT ([\w\d_\*-]+) FROM ([^ ]+)( WHERE .*)?$@i";
        if(preg_match($regex,$sql,$reg)) {
            $resource = $reg[1];
            $url = $reg[2];
            $s = SimpleDigir::create(trim($url))->setResource(trim($resource));
            if(isset($reg[3])) {
                $where = trim(str_replace("WHERE ","",$reg[3]));
                $where_regex = "@([^ ]+) ([\w]+) '([^']+)'@i";
                if(preg_match_all($where_regex,$where,$filters)) {
                    for($i=0;$i<count($filters[0]);$i++) {
                        $s->addFilter($filters[1][$i],$filters[2][$i],$filters[3][$i]);
                    }
                }
            }
            return $s ;
        }
    }

}
?>
