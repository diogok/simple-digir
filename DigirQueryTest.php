<?php
require_once 'PHPUnit/Framework.php';
include 'DigirQuery.php';

class DigirQueryTest extends PHPUnit_Framework_TestCase {

    function testQuery() {
        $sql =  "SELECT re-foo_bar123 FROM http://url.com";
        $xml =  SimpleDigir::create("http://url.com")->setResource("re-foo_bar123")->makeRequest();
        $this->assertEquals($xml,DigirQuery::query($sql)->makeRequest());

        $sql =  "SELECT re-foo_bar123 FROM http://url.com WHERE foo like 'bar%'";
        $xml =  SimpleDigir::create("http://url.com")->setResource("re-foo_bar123")->addFilter("foo","like","bar%")->makeRequest();
        $this->assertEquals($xml,DigirQuery::query($sql)->makeRequest());

        $sql =  "SELECT re-foo_bar123 FROM http://url.com WHERE foo equals 'foo bar'";
        $xml =  SimpleDigir::create("http://url.com")->setResource("re-foo_bar123")->addFilter("foo","equals","foo bar")->makeRequest();
        $this->assertEquals($xml,DigirQuery::query($sql)->makeRequest());

        $url = "http://dig.goog21.com.br:8081/hi-a/foo/bar.me/digir.php";
        $sql =  "SELECT re-foo_bar123 FROM ".$url." WHERE foo equals 'bar'";
        $xml =  SimpleDigir::create($url)->setResource("re-foo_bar123")->addFilter("foo","equals","bar")->makeRequest();
        $this->assertEquals($xml,DigirQuery::query($sql)->makeRequest());

        $url = "http://dig.goog21.com.br:8081/hi-a/foo/bar.me/digir.php";
        $sql =  "SELECT re-foo_bar123 FROM ".$url." WHERE foo equals 'bar', bar like 'foo%'";
        $xml =  SimpleDigir::create($url)->setResource("re-foo_bar123")->addFilter("foo","equals","bar")->addFilter('bar','like','foo%')->makeRequest();
        $this->assertEquals($xml,DigirQuery::query($sql)->makeRequest());

    }
}
?>
