<?php
/**
 * Copyright (c) 2011, Leon Sorokin
 * All rights reserved.
 * 
 * npa-nxx-miner.php
 * a stupid-simple NPA-NXX database > CSV miner
 * when you dont need extensive info and like to
 * save $249.95 - http://www.area-codes.com/area-code-database.asp
 */

ini_set("max_execution_time", 3600);

$sources = array(
	'telcodata'	=> array(
		'url'		=> 'http://www.telcodata.us/query/queryareacodexml.html?npa=',
		'nodeName'	=> 'exchangedata'
	),
	'localcallingguide'	=> array(
		'url'		=> 'http://www.localcallingguide.com/xmlprefix.php?npa=',
		'nodeName'	=> 'prefixdata'
	),
);

// select data source
$src		= 'localcallingguide';
$url		= $sources[$src]['url'];
$nodeName	= $sources[$src]['nodeName'];

$min_npa = 200;
$max_npa = 989;

$fp = fopen("npa-nxx-{$src}.csv", 'w');
// npa enumeration, avoiding N9X (most, if not all of these are unused expansion codes)
for ($npa = $min_npa; $npa <= $max_npa; $npa += substr($npa,1,2) == 89 ? 11 : 1) {
	$xml = simplexml_load_string(file_get_contents($url . $npa));
	if (!($recs = &$xml->$nodeName)) continue;
	if (!$headers) $headers = fputcsv($fp, array_keys((array)$recs[0]));
	foreach ($recs as $rec) fputcsv($fp, (array)$rec);
}
fclose($fp);
?>