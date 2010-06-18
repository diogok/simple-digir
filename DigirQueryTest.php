<?php
require_once 'PHPUnit/Framework.php';
include 'DigirQuery.php';

class DigirQueryTest extends PHPUnit_Framework_TestCase {


    function testParseResults() {
        $sql =  "SELECT field1,Field2 as f2,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi'";
        $query = DigirQuery::create($sql);
        $fake = array() ;

        $fake1 = array() ;
        $fake11 = new StdClass;
        $fake11->field1 = "hi";
        $fake11->Field2 = "me";
        $fake11->field3 = "yo";
        $fake11->field4 = "boo";
        $fake1[] = $fake11;
        $fake12 = new StdClass;
        $fake12->field1 = "hi";
        $fake12->Field2 = "me";
        $fake12->field3 = "yo";
        $fake12->field4 = "boo";
        $fake1[] = $fake12;


        $fake2 = array() ;
        $fake21 = new StdClass;
        $fake21->field1 = "hi";
        $fake21->Field2 = "me";
        $fake21->field3 = "yo";
        $fake21->field4 = "boo";
        $fake2[] = $fake21;
        $fake22 = new StdClass;
        $fake22->field1 = "hi";
        $fake22->Field2 = "me";
        $fake22->field3 = "yo";
        $fake22->field4 = "boo";
        $fake2[] = $fake22;

        $fake[] = $fake1 ;
        $fake[] = $fake2 ;

        $expected = array() ;
        $expected11 = new StdClass;
        $expected11->field1 = "hi";
        $expected11->f2 = "me";
        $expected11->field3 = "yo";
        $expected[] = $expected11;
        $expected12 = new StdClass;
        $expected12->field1 = "hi";
        $expected12->f2 = "me";
        $expected12->field3 = "yo";
        $expected[] = $expected12;

        $expected21 = new StdClass;
        $expected21->field1 = "hi";
        $expected21->f2 = "me";
        $expected21->field3 = "yo";
        $expected[] = $expected21;
        $expected22 = new StdClass;
        $expected22->field1 = "hi";
        $expected22->f2 = "me";
        $expected22->field3 = "yo";
        $expected[] = $expected22;

        $this->assertEquals($expected,$query->parseResults($fake));
    }

    function testQuery0() {
        $sql =  "SELECT * FROM 'http://url.com'";
        $query = DigirQuery::create($sql);
        $from=array();
        $from[] = "http://url.com";
        $this->assertEquals($from,$query->from());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("*")->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery1() {
        $sql =  "SELECT * FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array();
        $this->assertEquals($fields,$query->fields());
        $from=array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery2() {
        $sql =  "SELECT field1 FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery3() {
        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1','Field2'=>'Field2','field3'=>'field3');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery4() {
        $sql =  "SELECT field1,Field2 as f2,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1','Field2'=>'f2','field3'=>'field3');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi'));
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->addFilter('field1','equals','hi')->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery5() {
        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2='me'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('field1'=>'hi','field2'=>'me');
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me'));
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->addFilter('field1','equals','hi')->addFilter('field2','equals','me')->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery6() {
        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2='me' AND field3='meme'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me','field3'=>'meme'));
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->addFilter('field1','equals','hi')->addFilter('field2','equals','me')->addFIlter('field3','equals','meme')->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }

    function testQuery7() {
        $sql =  "SELECT field1 FROM 'http://url.com'.'foo-bar','http://url2.com'.'rec2','http://url.com'.'rec2' WHERE field1='hi' AND field2='me'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $from['http://url2.com'][] = 'rec2';
        $from['http://url.com'][] = 'rec2';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me'));
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml[] = SimpleDigir::create("http://url.com")->setResource("foo-bar")->addFilter('field1','equals','hi')->addFilter('field2','equals','me')->makeRequest();
        $xml[] = SimpleDigir::create("http://url2.com")->setResource("rec2")->addFilter('field1','equals','hi')->addFilter('field2','equals','me')->makeRequest();
        $xml[] = SimpleDigir::create("http://url.com")->setResource("rec2")->addFilter('field1','equals','hi')->addFilter('field2','equals','me')->makeRequest();
        $this->assertEquals($xml[0],$clients[0]->makeRequest());
        $this->assertEquals($xml[1],$clients[2]->makeRequest());
        $this->assertEquals($xml[2],$clients[1]->makeRequest());
    }

    function testQuery8() {
        $sql =  "SELECT * FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2 like 'me' AND field3 = 'meme'";
        $query = DigirQuery::create($sql);
        $where = array('equals'=>array('field1'=>'hi','field3'=>'meme'),'like'=>array('field2'=>'me'));
        $this->assertEquals($where,$query->where());
        $clients = $query->makeClients();
        $xml = SimpleDigir::create("http://url.com")->setResource("foo-bar")->addFilter('field1','equals','hi')->addFIlter('field3','equals','meme')->addFilter('field2','like','me')->makeRequest();
        $this->assertEquals($xml,$clients[0]->makeRequest());
    }
}
?>
