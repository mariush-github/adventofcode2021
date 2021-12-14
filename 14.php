<?php

$content = trim(file_get_contents('./input/14.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);
$lines = explode(chr(0x0A),$content);

$formula = $lines[0];
$rules = array();
for ($i=2;$i<count($lines);$i++) {
	list($match,$insert) = explode(' -> ',$lines[$i]);
	$rules[$match] = $insert;
}

function do_pass($formula) {
	global $rules;
	$formula_new = substr($formula,0,1);
	//echo "formula = :$formula:\n";
	for ($i=1;$i<strlen($formula);$i++) {
		$rule = substr($formula,$i-1,2);
		//echo "$rule\n";
		$formula_new .= $rules[$rule].substr($rule,1,1);
	}
	return $formula_new;
}

function do_score($formula) {
	$values = array();
	for ($i=0;$i<strlen($formula);$i++) {
		$c = substr($formula,$i,1);
		if (isset($values[$c])==false) $values[$c]=0;
		$values[$c]++;
	}
	//var_dump($values);
	sort($values);
	//var_dump($values);
	return ($values[count($values)-1]-$values[0]);
}


for ($i=1;$i<11;$i++) {
	$formula = do_pass($formula); 
	//echo "pass $i = $formula \n";
}

echo "14.01 Solution is : ".do_score($formula)."\n";

$formula = $lines[0];

function smart_pass($pairs) {
	global $rules;
	$p = array();
	foreach ($pairs as $pair => $total) {
		//$p[$pair] = 0;
		$pair1 = substr($pair,0,1).$rules[$pair];
		$pair2 = $rules[$pair].substr($pair,1,1);
		if (isset($p[$pair1])==FALSE) $p[$pair1]=0;
		if (isset($p[$pair2])==FALSE) $p[$pair2]=0;
		$p[$pair1]+=$total;
		$p[$pair2]+=$total;
	}
	return $p;
}

function smart_add($pairs) {
	$total = array();
	foreach ($pairs as $pair => $sum) {
		$c = substr($pair,1,1);
		if (isset($total[$c])==FALSE) $total[$c]=0;
		$total[$c]+=$sum;
	}
	return $total;
}

$pairs = array();
for ($i=1;$i<strlen($formula);$i++) { 
	$pair = substr($formula,$i-1,2);
	$pairs[$pair] = (isset($pairs[$pair])==false) ? 1 : ($pairs[$pair]+1); 
}
for ($i=1;$i<41;$i++) {
	$pairs = smart_pass($pairs);
	//echo "$i : ".json_encode($pairs)."\n";
}
//var_dump($pairs);
$total = smart_add($pairs);
$total[substr($formula,0,1)]++;
//var_dump($total);
sort($total);
var_dump($total);
echo "14.02 Solution is : ".($total[count($total)-1]-$total[0])."\n";


?>