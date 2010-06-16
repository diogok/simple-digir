<?php
require_once 'PHPUnit/Framework.php';
include 'SimpleDigir.php';

function clear($txt) {
    return str_replace("\n","",str_replace("\t","",$txt));
}

class SimpleDigirTest extends PHPUnit_Framework_TestCase {

    function testCreate() {
        $s = SimpleDigir::create("foobar");
        $this->assertEquals($s->url, "foobar");
    }

    function testFilter() {
        $s = SimpleDigir::create("foobar");
        $s->addFilter("ScientificName","like","Quercus");
        $shouldBe = '<filter><like><dwc:ScientificName>Quercus</dwc:ScientificName></like></filter>';
        $this->assertEquals(clear($s->filters()),$shouldBe);
    }

    function testHeader() {
        $s = SimpleDigir::create("foobar");
        $should = '<header><version>$version</version><sendTime>$DateFormatter.currentDateTimeAsXMLString()</sendTime><source>$hostAddress</source><destination resource="GBIF">foobar</destination><type>search</type></header>';
        $this->assertEquals(clear($should),clear($s->header()));
    }

    function testRecordPaging() {
        $s = SimpleDigir::create("foobar")->start(5)->limit(10);
        $xml  = '';
        $xml .= '<records limit="10" start="5">';
        $xml .= '<structure schemaLocation="http://digir.sourceforge.net/schema/conceptual/darwin/full/2003/1.0/darwin2full.xsd"/>';
        $xml .= '</records>';
        $this->assertEquals($xml,clear($s->records()));

        $s = SimpleDigir::create("foobar");
        $xml  = '';
        $xml .= '<records limit="999" start="0">';
        $xml .= '<structure schemaLocation="http://digir.sourceforge.net/schema/conceptual/darwin/full/2003/1.0/darwin2full.xsd"/>';
        $xml .= '</records>';
        $this->assertEquals($xml,clear($s->records()));
    }

    function testParser() {
        $xml = "<result><record><a>abc</a><b /><c>foo</c></record><record><a /><b>bar</b></record></result>";
        $recs = SimpleDigir::create("foobar")->parse($xml);
        $this->assertEquals("abc",$recs[0]->a);
        $this->assertEquals(null,$recs[0]->b);
        $this->assertEquals("foo",$recs[0]->c);
        $this->assertEquals(null,$recs[1]->a);
        $this->assertEquals("bar",$recs[1]->b);
    }
}
?>
