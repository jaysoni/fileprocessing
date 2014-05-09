<?php
/**
	This file contain the script that will read data from text file "sample.txt" located in the same directory
*/

$filename = 'sample.txt'; /* Name of the file that we want to scrape data from into comma separated value file */
$fp = fopen($filename, 'r');
$startflag = false;
$readarray = array();
$index = 0;
$counter = 0;
$fileheader = array(
	'Running number',
	'Title',
	'Author',
	'Shelfmark',
	'Location code',
	'Barcode',	
	'Checkouts',
	'Location description',
	
);
while (($line = fgets($fp)) !== false) {
	$line = explode("\r\n", $line);	
	if (empty($line[0]) &&  empty($line[1])) {
		$startflag = true;
		continue;
	}
	
	if ($startflag) {
		if (strpos(strtolower($line[0]), strtolower('ITEMS ON RESERVE FOR')) > 0) {
			$startflag = false;
			$counter = 0;
			continue;
		}
		else {
			$readarray[$index][] = $line[0];
			if ($counter == 2) {
				$counter = -1;
				$startflag = false;
				$index++;
			}
			$counter++;
		}
	}
}
if ($readarray) {
	$counter = 0;
	$index = 0;
	$csvarray = array();
	foreach ($readarray as $key => $value) {
		foreach ($value as $item) {
			$csvarray[$index]['Running number'] = $index + 1;
			switch ($counter) {
				case 0 : 					
					$item = explode("    ", $item);
						$i = 0;
					foreach ($item as $b) {
						if (trim($b) != '') {
							if ($i == 0) {
								$csvarray[$index]['Title'] = trim($b);
							}
							elseif ($i == 1) {
								$csvarray[$index]['Author'] = trim($b);
							}							
							$i++;
						}
					}
					if (empty($csvarray[$index]['Author'])) {
						$csvarray[$index]['Author'] = '';
					}
					break;
				case 1 : 					
					$item = explode("  ", $item);
						$i = 0;
					foreach ($item as $b) {
						if (trim($b) != '') {
							if ($i == 0) {
								$csvarray[$index]['Shelfmark'] = trim($b);
							}
							elseif ($i == 1) {
								$csvarray[$index]['Location code'] = trim($b);
							}							
							$i++;
						}
					}					
					break;
				case 2 : 
					$item = explode("  ", $item);
						$i = 0;
					foreach ($item as $b) {					
						if (trim($b) != '') {
							if ($i == 0) {
								$csvarray[$index]['Barcode'] = trim($b);
							}
							elseif ($i == 1) {
								$csvarray[$index]['Checkouts'] = trim($b);
							}
							elseif ($i == 2) {
								$csvarray[$index]['Location description'] = trim($b);
							}
							$i++;
						}
					}					
					break;					
			}
			if ($counter == 2) {
				$index++;
				$counter = -1;
			}
			$counter++;
		}
	}	
	if (!empty($csvarray)) {
		$fp = fopen('file.csv', 'w');
		fputcsv($fp, $fileheader);
		fputcsv($fp, array());
		foreach ($csvarray as $row) {		
			 fputcsv($fp, $row);
		}
	}
	
}



