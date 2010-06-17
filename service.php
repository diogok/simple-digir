<?php
include 'DigirQuery.php';
if(isset($_GET["query"])) {
    $_POST["query"] = $_GET["query"];
}
if(isset($_POST["query"])) {
    try {
        $result = DigirQuery::query($_POST["query"])->getResult();
        echo "{success: true, count: ".count($result).", result:". json_encode($result)  ."}";
    } catch(Exception $e) {
        echo "{success:false, count:0, result: []}";
    }
}
?>
