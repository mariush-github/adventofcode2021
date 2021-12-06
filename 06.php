<?php
//$content = '3,4,3,1,2'; // test case 
$content = trim(file_get_contents('./input/06.txt')," \n\r");
$fishes = explode(',',$content);
foreach($fishes as $id=>$fish) { $fishes[$id] = intval($fish); }

$day = 1 ;

while($day!=81) {
	$fishCount = count($fishes);
	for ($i=0;$i<$fishCount;$i++) {
		if ($fishes[$i]==0) {
			$fishes[$i]=6;
			array_push($fishes,8);
		} else {
			$fishes[$i]--;
		}
	}
	$day++;
}

echo "06.01 Fishes after 80 days: ". count($fishes)."\n";

// did it "brute force" above just in case needed to track days for all fishes
// can be more clever now on part 2

$fishAges = array(); 
for($i=0;$i<9;$i++) { $fishAges[$i]=0; }
$fishes = explode(',',$content);
foreach($fishes as $id=>$fish) { $fishAges[intval($fish)]++; }

$day = 1;
while ($day!=257) {
	$reset6 = $fishAges[0]; // remember how many fish with age 0 get reset to 6
	for ($i=1;$i<9;$i++) {
		$fishAges[$i-1] += $fishAges[$i];
		$fishAges[$i] = 0;
	}
	$fishAges[8]  = $reset6; // each fish with 0 age makes up a new fish with age 8
	$fishAges[0] -= $reset6; // the fish with age 0 at start respawn with age 6
	$fishAges[6] += $reset6; // so add the amount we tracked at start of day
	$day++;
}
echo "06.02 Fishes after 256 days: ". array_sum($fishAges)."\n";
?>