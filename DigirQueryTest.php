<?php
require_once 'PHPUnit/Framework.php';
include 'DigirQuery.php';

class DigirQueryTest extends PHPUnit_Framework_TestCase {

    function testQuery() {
        $sql =  "SELECT * FROM 'http://url.com'";
        $query = DigirQuery::create($sql);
        $from=array();
        $from[] = "http://url.com";
        $this->assertEquals($from,$query->from());

        $sql =  "SELECT * FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array();
        $this->assertEquals($fields,$query->fields());
        $from=array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1 FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1','Field2'=>'Field2','field3'=>'field3');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array();
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1,Field2 as f2,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi'";
        $query = DigirQuery::create($sql);
        $fields = array('field1'=>'field1','Field2'=>'f2','field3'=>'field3');
        $this->assertEquals($fields,$query->fields());
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi'));
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2='me'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('field1'=>'hi','field2'=>'me');
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me'));
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1,Field2 ,field3 FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2='me' AND field3='meme'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me','field3'=>'meme'));
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT field1 FROM 'http://url.com'.'foo-bar','http://url2.com'.'rec2','http://url.com'.'rec2' WHERE field1='hi' AND field2='me'";
        $query = DigirQuery::create($sql);
        $from = array();
        $from['http://url.com'][] = 'foo-bar';
        $from['http://url2.com'][] = 'rec2';
        $from['http://url.com'][] = 'rec2';
        $this->assertEquals($from,$query->from());
        $where = array('equals'=>array('field1'=>'hi','field2'=>'me'));
        $this->assertEquals($where,$query->where());

        $sql =  "SELECT * FROM 'http://url.com'.'foo-bar' WHERE field1='hi' AND field2 like 'me' AND field3='meme'";
        $query = DigirQuery::create($sql);
        $where = array('equals'=>array('field1'=>'hi','field3'=>'meme'),'like'=>array('field2'=>'me'));
        $this->assertEquals($where,$query->where());
    }
}
?>
