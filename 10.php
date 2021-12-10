<?php

$content = trim(file_get_contents('./input/10.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);

$lines = explode(chr(0x0A),$content);

function validate_syntax($text) {
	$starters = '([{<';
	$finishers = ')]}>';
	$match = array('('=>')','['=>']','{'=>'}','<'=>'>');
	$s = substr($text,0,1);
	for ($i=1;$i<strlen($text);$i++) {
		$prev = substr($s,strlen($s)-1,1);
		$c = substr($text,$i,1);
		if (strpos($starters,$c)!==FALSE) $s .=$c;
		if (strpos($finishers,$c)!==FALSE) {
			if ($match[$prev]!=$c) {
				echo $text.' - Expected '.$match[$prev].', but found '.$c.' instead.'."\n";
				return $c;
			} else {
				$s = substr($s,0,strlen($s)-1);
			}
		}
		
	}
	if ($s!='') { echo $text." - incomplete\n"; return 'i'; }
	return '';
}

function autocomplete_text($text) {
	$match = array('('=>')','['=>']','{'=>'}','<'=>'>');
	$s = $text;
	$complete = '';
	while ($s!='') {
		$prev = $s;
		$curr = '';
		while ($prev!=$curr) {
			$s = str_replace('()','',$s);
			$s = str_replace('{}','',$s);
			$s = str_replace('[]','',$s);
			$s = str_replace('<>','',$s);
			$prev = $curr;
			$curr = $s;
		}
		if (strlen($s)>0) {
			$c = substr($s,strlen($s)-1,1);
			$complete = $complete.$match[$c];
			$s.=$match[$c];
		}
	}
	return $complete;
}

function calculate_sum($text) {
	$sum = 0;
	$values = array(')'=>1,']'=>2,'}'=>3,'>'=>4);
	for ($i=0;$i<strlen($text);$i++) {
		$c = substr($text,$i,1);
		$sum = $sum*5+$values[$c];
	}
	return $sum;
}

$incomplete = array();
$sum = 0;
foreach ($lines  as $line) {
	//echo $line."\n";
	$value = validate_syntax($line);
	if ($value!='') {
		if ($value==')') $sum += 3;
		if ($value==']') $sum += 57;
		if ($value=='}') $sum += 1197;
		if ($value=='>') $sum += 25137;
	}
	if ($value=='i') array_push($incomplete,$line);
}
echo "10.01 Solution is ".$sum."\n";
$scores = array();
foreach ($incomplete as $inco) {
	$v =  autocomplete_text($inco);
	$score = calculate_sum($v);
	//echo $score."\n";
	array_push($scores,$score);
}
sort($scores);
//var_dump($scores);
echo "10.02 Solution is ".$scores[intval(count($scores)/2)]."\n";
?>
