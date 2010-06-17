<?php

class SimpleDigir {

    public $filters = array();
    public $url = "";
    public $limit = 999;
    public $start = 0;
    public $result = array();
    public $resource = "";

    private function __construct($url) {
        $this->url = $url;
    }

    static public function create($url) {
        return new SimpleDigir($url);
    }

    public function parseResources($xml) {
        $dom = new DOMDocument;
        $dom->loadXML($xml);
        $tags = $dom->getElementsByTagName("resource");
        $recs = array();
        foreach($tags as $item) {
            $rec = new StdClass ;
            $rec->name = $item->getElementsByTagName("name")->item(0)->nodeValue;
            $rec->code = $item->getElementsByTagName("code")->item(0)->nodeValue;
            $recs[] = $rec;
        }
        return $recs;
    }

    public function setResource($rec) {
        $this->resource = $rec;
        return $this;
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
        $xml .= "\t\t<source>".getenv("SERVER_NAME")."</source>\n";
        $xml .= "\t\t<destination resource=\"".$this->resource."\">".$this->url."</destination>\n";
        $xml .= "\t\t<type>search</type>\n";
        $xml .= "\t</header>\n";
        return $xml ;
    }

    public function filters() {
        if(count($this->filters) < 1) {
            $this->addFilter("ScientificName","like","%");
        }
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
                $name = $dwc->localName ;
                if(!is_null($name) && strlen($name) >=1 ) {
                    $value = $dwc->nodeValue;
                    $rec->$name = $value;
                }
            }
            $records[] = $rec;
        }
        return $records;
    }

    public function call() {
        if(!empty($this->result)) return $this;
        if($this->resource == "") {
        } else if($this->resource == "*") { 
             $this->result = $this->parseResources(file_get_contents($this->url));
        } else {
            $url = $this->url . "?request=".urlencode($this->makeRequest());
            $this->result = $this->parse(file_get_contents($url));
        }
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
