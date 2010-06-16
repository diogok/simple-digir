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
        $xmlns  = "xmlns='http://digir.net/schema/protocol/2003/1.0'\n";
        $xmlns .= "\txmlns:xsd='http://www.w3.org/2001/XMLSchema'\n";
        $xmlns .= "\txmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'\n";
        $xmlns .= "\txmlns:digir='http://digir.net/schema/protocol/2003/1.0'\n";
        $xmlns .= "\txmlns:darwin='http://digir.net/schema/conceptual/darwin/2003/1.0'\n";
        $xmlns .= "\txmlns:dwc='http://digir.net/schema/conceptual/darwin/2003/1.0'\n";
        $xmlns .= "\txsi:schemaLocation='http://digir.net/schema/protocol/2003/1.0\n";
        $xmlns .= "\t\thttp://digir.sourceforge.net/schema/protocol/2003/1.0/digir.xsd\n";
        $xmlns .= "\t\thttp://digir.net/schema/conceptual/darwin/2003/1.0\n" ;
        $xmlns .= "\t\thttp://digir.sourceforge.net/schema/conceptual/darwin/2003/1.0/darwin2.xsd'";
        return $xmlns;
    }

    public function header() {
        $xml  = "\t<header>\n";
        $xml .= "\t\t<version>\$version</version>\n";
        $xml .= "\t\t<sendTime>\$DateFormatter.currentDateTimeAsXMLString()</sendTime>\n";
        $xml .= "\t\t<source>\$hostAddress</source>\n";
        $xml .= "\t\t<destination resource=\"GBIF\">".$this->url."</destination>\n";
        $xml .= "\t\t<type>search</type>\n";
        $xml .= "\t</header>\n";
        return $xml ;
    }

    public function filters() {
        $xml = "\t\t<filter>\n";
        foreach($this->filters as $f) {
            $xml .= "\t\t\t<".$f->operator.">\n";
            $xml .= "\t\t\t\t<dwc:".$f->field.">";
            $xml .= $f->term;
            $xml .= "</dwc:".$f->field.">\n";
            $xml .= "\t\t\t</".$f->operator.">\n";
        }
        $xml .= "\t\t</filter>";
        return $xml ;
    }

    public function records() {
        $xml  = "\t\t<records limit=\"".$this->limit."\" start=\"".$this->start."\">\n";
        $xml .= "\t\t\t<structure schemaLocation=\"http://digir.sourceforge.net/schema/conceptual/darwin/full/2003/1.0/darwin2full.xsd\"/>\n";
        $xml .= "\t\t</records>\n";
        return $xml ;
    }

    public function makeRequest() {
        $xml  = "<request ".$this->xmlns()." >\n";
        $xml .= $this->header();
        $xml .= "\t<search>\n";
        $xml .= $this->filters() ;
        $xml .= "\n".$this->records() ;
        $xml .= "\t\t<count>\$count</count>";
        $xml .= "\n\t</search>\n";
        $xml .= "</request>";
        return $xml;
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
        if(!empty($this->result)) return $this;
        $request = $this->makeRequest();
        $url = $this->url . "?request=".urlencode($request);
        $response = file_get_contents($url);
        if($response === false) return null;
        $this->result = $this->parse($response);
        return $this;
    }

    public function getResult() {
        if(empty($this->result)) {
            $this->call();
        }
        return $this->result;
    }

}

?>
