<?php

namespace matpoppl\Translate;

class XliffSource
{
    public function supports($filename)
    {
        $doc = new \DOMDocument();
        $doc->load($filename);
        return $doc->schemaValidate('https://docs.oasis-open.org/xliff/xliff-core/v2.0/os/schemas/xliff_core_2.0.xsd');
    }
    
    public function load($filename)
    {
        $ret = [];
        $xml = simplexml_load_file($filename);
        foreach ($xml->file as $file) {
            foreach ($file->unit as $unit) {
                foreach ($unit->segment as $segment) {
                    $msgid = '' . $segment->source;
                    $value = '' . $segment->target;
                    
                    $ret[$msgid] = $value;
                }
            }
        }
        return $ret;
    }
}
