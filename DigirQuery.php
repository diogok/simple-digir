<?php
include "SimpleDigir.php" ;

class DigirQuery {
    
    private function __construct() {
    }

    static public function query($sql) {
        $sql = trim($sql);
        $regex = "@^SELECT ([\w\d_-]+) FROM ([^ ]+)( WHERE .*)?$@i";
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
