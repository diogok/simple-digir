<?php
include 'DigirQuery.php';
if(isset($_GET["query"])) {
    $_POST["query"] = $_GET["query"];
}

if(!file_exists("cache.db")){
    $db = new PDO("sqlite:cache.db");
    $db->exec("CREATE TABLE cache (query TEXT PRIMARY KEY, result TEXT, last INTEGER)");
    $db = null;
}

if(isset($_POST["query"])) {
    $_POST["query"] = stripslashes($_POST["query"]);
    $db = new PDO("sqlite:cache.db");
    $caches = $db->prepare("SELECT result, last FROM cache WHERE query = ?");
    $caches->execute(array($_POST["query"]));
    $cache = $caches->fetchObject();
    $tolerance = time() - (24 * 60 * 60);
    if($cache != false && $cache->last >= $tolerance ) {
        $r = $cache->result ;
    } else {
        $records = DigirQuery::create($_POST["query"])->getResult();
        $result  = '{"success": true, "count": '.count($records).', "result":'. json_encode($records)  .'}';
        if($cache == false) {
            $insert  = $db->prepare('INSERT INTO cache (query,result,last) VALUES (?,?,?)');
            $insert->execute(array($_POST["query"],$result,time()));
        } else {
            $update  = $db->prepare('UPDATE cache set result = ?, last = ? WHERE query = ?');
            $update->execute(array($result,time(),$_POST["query"]));
        }
        $r = $result;
    }
    if(isset($_GET['csv'])) {
        $data = json_decode($r)->result ;
        $csv = "";
        foreach($data[1] as $k=>$v) {
            $csv .= $k.";";
        }
        $csv .= "\n";
        foreach($data as $obj) {
            foreach($obj as $v) {
                $csv .= str_replace(";",",",$v).";";
            }
            $csv .= "\n";
        }
        echo $csv;
    } else {
        echo $r ;
    }
}

?>
