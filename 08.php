<?php

$content = trim(file_get_contents('./input/08.txt')," \n\r");
$lines = explode(chr(0x0A),$content);

$debug = false;

$entries = array();
foreach ($lines as $line) {
	list($input,$output) = explode(' | ',trim($line));
	$input = explode(' ',$input);
	$output = explode(' ',$output);
	array_push($entries,array('in'=>$input,'out'=>$output));
}
// first solution
$unique = array();
foreach ($entries as $entry) {
	foreach ($entry['out'] as $e) {
		if (strlen($e)==2 || strlen($e)==3 || strlen($e) == 4 || strlen($e)==7 ) $unique[$e]= (isset($unique[$e])==false) ? 1 : ($unique[$e]+1);
	}
}
echo "08.01 Digits with 2,3,4 or 7 segments : ".array_sum($unique)."\n";

// sort inputs of each line, by number of segments 

function sort_by_length($a,$b) {
	if ($a==$b) return 0;
	return strlen($a)<strlen($b) ? -1 : 1;
}
foreach ($entries as $index=>$entry) {
	usort($entries[$index]['in'],'sort_by_length');
	//echo json_encode($entries[$index]['in'])."\n";
	
}

function sets_merge($a,$b) { // ab, bc -> abc 
	$out=$a;for($i=0;$i<strlen($b);$i++) { if (strpos($out,substr($b,$i,1))===FALSE) $out.= substr($b,$i,1); }
	return $out;
}
function sets_in_both($a,$b) {// abc, be -> b
	$set = (strlen($a)> strlen($b)) ? $a : $b;
	$look= (strlen($a)> strlen($b)) ? $b : $a;
	$out = ''; for ($i=0;$i<strlen($set);$i++) { $c=substr($set,$i,1); if (strpos($look,$c)!==FALSE) $out .=$c; }
	return $out;
};
function sets_not_both($a,$b) {// abc, be -> b
	$set = $a.$b;
	$unique = array(); for ($i=0;$i<strlen($set);$i++) {$c=substr($set,$i,1); if (isset($unique[$c])==false) $unique[$c]=0;$unique[$c]++;}
	$out = ''; foreach ($unique as $c => $count) { if ($count<2) $out.=$c;}
	return $out;
};


function convert_letters_to_number($letters,$segments) {
	$segs = array();
	//echo "letters=$letters\n";
	if (strlen($letters)<1) return 0;
	for ($i=0;$i<strlen($letters);$i++) {
		$character = substr($letters,$i,1);
		foreach ($segments as $idx => $char) {
			if ($char==$character) array_push($segs,$idx);
		}
	}
	sort($segs);

	// could probably be done in a much clever way, but oh well... 
	$code = implode('.',$segs);
	if ($code=='0.1.2.3.4.5') return 0;
	if ($code=='1.2') return 1;
	if ($code=='0.1.3.4.6') return 2;
	if ($code=='0.1.2.3.6') return 3;
	if ($code=='1.2.5.6') return 4;
	if ($code=='0.2.3.5.6') return 5;
	if ($code=='0.2.3.4.5.6') return 6;
	if ($code=='0.1.2') return 7;
	if ($code=='0.1.2.3.4.5.6') return 8;
	if ($code=='0.1.2.3.5.6') return 9;
	return 0;
}

$sum = 0;

// random sample that caused me problems 
//
//  "ed","ced","bedf","gebcd","bdacg","gecbf","dfcage","cbegaf","bdfegc","adcbefg"]
//
//    ccccc
//   ff   ed
//	 ff   ed
//     bbb 
//   aa   ed
//   aa   ed
//    ggggg


foreach ($entries as $entry) {
	if ($debug) echo json_encode($entry['in'])."\n\n\n";  // for debugging only
	$segments = ['x','x','x','x','x','x','x'];
	// segment 0 (top) can be determined from digits 1 and 7 which are the only ones with 2 and 3 segments.
	// the segment that's not in 1 will be the top segment 
	$segments[0] = sets_not_both($entry['in'][1],$entry['in'][0]);
	if ($debug) echo json_encode($segments)."\n";
	// we can combine 4 with the top segment to get 9 without the bottom segment. 
	// 9 is one of the three digits with 6 segments (0,6,9) but only 9 will have only one segment different
	$merged = sets_merge($entry['in'][2],$segments[0]); 
	for ($i=6;$i<9;$i++) { $test = $entry['in'][$i]; $c = sets_not_both($test,$merged); if ($debug) echo "test $test against $merged = $c\n"; if (strlen($c)==1) $segments[3] = $c; }
	if ($debug) echo json_encode($segments)."\n";
	// now we can combine 4 with top and bottom segment to get our 9. Difference between 8 and 9 is bottom left segment 
	$merged = sets_merge($entry['in'][2],$segments[0]);
	$merged = sets_merge($merged,$segments[3]);
	$segments[4] = sets_not_both($merged,$entry['in'][9]);
	if ($debug) echo json_encode($segments)."\n";
	// digits 2, 3, 5 all have 5 segments. All 3 have center segments common and we have top and bottom segments so we can remove those from the three digits. 
	$d = array();
	for ($i=0;$i<3;$i++) {
		$d[$i] = $entry['in'][$i+3];
		$d[$i] = str_replace($segments[0],'',$d[$i]); // remove top segment 
		$d[$i] = str_replace($segments[3],'',$d[$i]); // remove bottom segment 
	}
	
	for ($i=0;$i<3;$i++) {
		$c = substr($d[0],$i,1); if ((strpos($d[1],$c)!==FALSE) && (strpos($d[2],$c)!==FALSE)) $segments[6] = $c;
	}
	if ($debug) echo json_encode($segments)."\n";
	// so now we have center segment, we substract 1 and this segment from 4 and get top left segment 
	$merged = $entry['in'][0].$segments[6];
	$segments[5] = sets_not_both($merged,$entry['in'][2]);
	if ($debug) echo json_encode($segments)."\n";
	// going back to the digits with 5 segments : 2 , 3, 5 we can remove center segments 
	// Digit 3 will have both right side segments so we can't use it. 
	// Digit 2 will have top right segment, Digit 5 will have bottom right segment.
	
	for ($i=0;$i<3;$i++) {
		$digit = $entry['in'][3+$i];
		$digit = str_replace(array($segments[0],$segments[3],$segments[6]),'',$digit);
		$one_a = $entry['in'][0]; $one_b = substr($entry['in'][0],1,1).substr($entry['in'][0],0,1);
		if (($digit==$one_a) || ($digit==$one_b)) { // this is 3, don't care about it
		} else {
			// if it's 2, remove bottom left segment, end up just with top right segment 
			if (strpos($digit,$segments[4])!==FALSE) $segments[1] = str_replace($segments[4],'',$digit); 
			// if it's 5, remove top left segment, end up just with top right segment 
			if (strpos($digit,$segments[5])!==FALSE) $segments[2] = str_replace($segments[5],'',$digit); 
		}
	}
	if ($debug) echo json_encode($segments)."\n";
	
	$value = convert_letters_to_number($entry['out'][0],$segments) * 1000;
	$value+= convert_letters_to_number($entry['out'][1],$segments) * 100;
	$value+= convert_letters_to_number($entry['out'][2],$segments) * 10;
	$value+= convert_letters_to_number($entry['out'][3],$segments);
	echo $value."\n";
	$sum = $sum+$value;
}

echo "08.02 The total amount is $sum.\n";
?>
