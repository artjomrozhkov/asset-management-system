<?php
extension_loaded('xsl') or die('XSL extension not loaded');

$xml = new DOMDocument;
$xml->load('index.xml');

$xsl = new DOMDocument;
$xsl->load('index.xsl');

$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl);

echo $proc->transformToXML($xml);

$xml = simplexml_load_file('index.xml');
$json = json_encode($xml, JSON_PRETTY_PRINT);

file_put_contents('index.json', $json);