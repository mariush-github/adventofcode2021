<?php

$content = trim(file_get_contents('./input/03.txt')," \n\r");
$numbers = explode(chr(0x0A),$content);

$r = array();
for ($i=0;$i<12;$i++) { $r[$i] = array(); $r[$i]['0'] = 0; $r[$i]['1'] = 0; }

foreach ($numbers as $number) {
	for ($i=0;$i<12;$i++) { $c = substr($number,$i,1); $r[$i][$c]++; }
}

$gamma = '';
$epsilon = ''; 

for ($i=0;$i<12;$i++) {
	$least = $r[$i]['0'] < $r[$i]['1'] ? '0' : '1';
	$most  = $r[$i]['0'] > $r[$i]['1'] ? '0' : '1';
	
	$gamma .= $most;
	$epsilon .= $least;
}

$gamma_dec = base_convert($gamma,2,10);
$epsilon_dec = base_convert($epsilon,2,10);

$result = $gamma_dec * $epsilon_dec;

echo "03.01 gamma=$gamma epsilon=$epsilon gd=$gamma_dec ed=$epsilon_dec result=$result\n";

//  which : 0 = oxygen, 1 = co2

function find_value($numbers, $which = 0) {
	$continue = true;
	$input = $numbers;
	$output = array();
	$offset = 0;
	$sum = array();
	
	while ($continue) {
		$sum['0'] = 0;
		$sum['1'] = 0;
		$output = array();
		foreach ($input as $number) {
			$c = substr($number,$offset,1);
			$sum[$c]++;
		}
		$keep = '0';
		if ($which == 0) { // oxygen
			if ($sum['0']<=$sum['1']) $keep = '1';
			if ($sum['0'] >$sum['1']) $keep = '0';
		}
		if ($which == 1) { // co2
			if ($sum['0'] >$sum['1']) $keep = '1';
			if ($sum['0']<=$sum['1']) $keep = '0';
		}
		foreach ($input as $number) {
			$c = substr($number,$offset,1);
			if ($c==$keep) array_push($output,$number);
		}
		$input = $output;
		$offset++;
		if ($offset==12) $continue = false;
		if (count($output)==1) $continue = false;
	}
	//var_dump($input);
	return $input[0];
}

$oxygen = find_value($numbers,0);
$co2 = find_value($numbers,1);

$odec = base_convert($oxygen,2,10);
$cdec = base_convert($co2,2,10);

$result = $odec * $cdec;

echo "03.02 oxygen=$oxygen ($odec) co2=$co2 ($cdec) result=".($odec*$cdec)."\n";

?>
