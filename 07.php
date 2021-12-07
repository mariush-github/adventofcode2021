<?php
//$content = '16,1,2,0,4,2,7,1,2,14'; // test case 
$content = trim(file_get_contents('./input/07.txt')," \n\r");
$crabs = explode(',',$content);

$min = 999999;
$max = 0;
foreach($crabs as $id=>$crab) { 
	$crabs[$id] = intval($crab); 
	if ($crabs[$id] < $min) $min = $crabs[$id];
	if ($crabs[$id] > $max) $max = $crabs[$id];
}

echo "Loaded ".count($crabs)." crabs, min=$min max=$max\n";

function calculate_distance($target,$formula=1) {
	global $crabs;
	$sum = 0;
	foreach ($crabs as $crab) {
		$diff = ($target>$crab) ? $target-$crab : $crab-$target;
		if ($formula==1) $sum += $diff;
		if ($formula==2) $sum += intval($diff * ($diff+1) / 2);
	}
	return $sum;
}

$bestDistance1 = 99999999;
$bestDistanceValue1 = -1;
$bestDistance2 = 99999999;
$bestDistanceValue2 = -1;

for ($i=$min;$i<=$max;$i++) {
	$result1 = calculate_distance($i,1);
	$result2 = calculate_distance($i,2);
	if ($result1 < $bestDistance1) {
		$bestDistance1=$result1;
		$bestDistanceValue1 = $i;
	}
	if ($result2 < $bestDistance2) {
		$bestDistance2=$result2;
		$bestDistanceValue2 = $i;
	}
	
}

echo "07.01 Best to align at $bestDistanceValue1, it will cost fuel: $bestDistance1\n";
echo "07.02 Best to align at $bestDistanceValue2, it will cost fuel: $bestDistance2\n";
?>