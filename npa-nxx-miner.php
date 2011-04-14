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

$min_npa = 200;
$max_npa = 989;
$headers = array();

$fp = fopen('npa-nxx.csv', 'w');
// npa enumeration, avoiding N9X (most, if not all of these are unused expansion codes)
for ($npa = $min_npa; $npa <= $max_npa; $npa += substr($npa,1,2) == 89 ? 11 : 1) {
	$xml = simplexml_load_string(file_get_contents("http://www.telcodata.us/query/queryareacodexml.html?npa={$npa}"));

	if (empty($headers) && $xml->exchangedata) {
		foreach ($xml->exchangedata as $rec) {
			foreach ($rec->children() as $col) {
				$headers[] = $col->getName();
			}
			fputcsv($fp, $headers);
			break;
		}
	}

	foreach ($xml->exchangedata as $rec) {
		fputcsv($fp, (array)$rec);
	}
}
fclose($fp);
?>