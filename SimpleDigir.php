<?php

class SimpleDigir {

    public $filters = array();
    public $url = "";
    public $limit = 999;
    public $start = 0;
    public $result = array();

    private function __construct($url) {
        $this->url = $url;
    }

    static public function create($url) {
        return new SimpleDigir($url);
    }

    public function addFilter($field,$operator,$term) {
        $filter = new StdClass ;
        $filter->operator = $operator ;
        $filter->field = $field ;
        $filter->term = $term ;
        $this->filters[] = $filter ;
        return $this;
    }

    public function start($start) {
        $this->start = $start ;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit ;
        return $this;
    }

    public function xmlns() {
        $xmlns = '';
        $xmlns .= "xmlns='http://digir.net/schema/protocol/2003/1.0'";
        $xmlns .= "xmlns:xsd='http://www.w3.org/2001/XMLSchema'";
        $xmlns .= "xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'";
        $xmlns .= "xmlns::digir='http://digir.net/schema/protocol/2003/1.0'";
        $xmlns .= "xmlns::darwin='http://digir.net/schema/conceptual/darwin/2003/1.0'";
        $xmlns .= "xmlns::dwc='http://digir.net/schema/conceptual/darwin/2003/1.0'";
        $xmlns .= "xsi:schemaLocation='http://digir.net/schema/protocol/2003/1.0 ";
        $xmlns .= " http://digir.sourceforge.net/schema/protocol/2003/1.0/digir.xsd ";
        $xmlns .= " http://digir.net/schema/conceptual/darwin/2003/1.0 " ;
        $xmlns .= " http://digir.sourceforge.net/schema/conceptual/darwin/2003/1.0/darwin2.xsd'";
        return $xmlns;
    }

    public function header() {
        return '<header>
                <version>$version</version>
                <sendTime>$DateFormatter.currentDateTimeAsXMLString()</sendTime>
                <source>$hostAddress</source>
                <destination resource="GBIF">'.$this->url.'</destination>
                <type>search</type>
                </header>';
    }

    public function filters() {
        $xml = '<filter>';
        foreach($this->filters as $f) {
            $xml .= '<'.$f->operator.">";
            $xml .= '<dwc:'.$f->field.'>';
            $xml .= $f->term;
            $xml .= '</dwc:'.$f->field.'>';
            $xml .= '</'.$f->operator.">";
        }
        $xml .= "</filter>";
        return $xml ;
    }

    public function records() {
        $xml  = '';
        $xml .= '<records limit="'.$this->limit.'" start="'.$this->start.'">';
        $xml .= '<structure schemaLocation="http://digir.sourceforge.net/schema/conceptual/darwin/full/2003/1.0/darwin2full.xsd"/>';
        $xml .= '</records>';
        return $xml ;
    }

    public function makeRequest() {
        $xml  = '<request '.$this->xmlns()." >";
        $xml .= $this->header();
        $xml .= '<search>';
        $xml .= $this->filters() ;
        $xml .= $this->records() ;
        $xml .= '<count>$count</count>';
        $xml .= '</search>';
        $xml .= '</request>';
        return str_replace("\n","",$xml);
    }

    public function parse($xml){
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $records = array();
        $found = $doc->getElementsByTagName("record");
        foreach($found as $item) {
            $rec = new StdClass ;
            foreach($item->childNodes as $dwc) {
                $name = $dwc->nodeName ;
                $value = $dwc->nodeValue;
                $rec->$name = $value;
            }
            $records[] = $rec;
        }
        return $records;
    }

    public function call() {
        $request = urlencode($this->makeRequest());
        $url = $this->url . "?request=".$request;
        $response = file_get_contents($url);
        if($response === false) return null;
        $this->result = $this->parse($response);
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

}

?>
