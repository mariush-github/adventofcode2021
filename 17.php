<?php

$range_x = [257,286];
$range_y = [57,101];

function calculate_trajectory($speed_x,$speed_y) {
	global $range_x,$range_y;
	$peak_y = 0;
	$continue = true;
	$x = 0;
	$y = 0;
	$sx = $speed_x;
	$sy = $speed_y;
	while ($continue==true) {
		$x += $sx;
		$y += $sy;
		$sx = ($sx>0) ? ($sx-1) : 0;
		$sy = $sy -1;
		if ($y>0) {
			if ($peak_y<$y) $peak_y = $y;
		}
		if (($x>=$range_x[0]) && ($x<=$range_x[1])) {
			if (($y<0) && (abs($y)>=$range_y[0]) && (abs($y)<=$range_y[1])) {
				return [true,$x,$y,$peak_y,$speed_x,$speed_y];
			}
		}
		if (($y<0) && ($x>$range_x[1])) return [false,$x,$y,$peak_y,$speed_x,$speed_y];
		if (($y<0) && (abs($y) > $range_y[1])) return [false,$x,$y,$peak_y,$speed_x,$speed_y];
	}

}

$best = 0;
$solutions = [];

for ($y= -$range_y[1];$y<=$range_y[1];$y++) {
	for ($x=1;$x<=$range_x[1];$x++) {
		$result = calculate_trajectory($x,$y);
		if ($result[0]==true) {
			array_push($solutions,$result);
			if ($best<$result[3]) $best = $result[3];
		}
	}
}
echo "17.01 Solution is $best\n";

echo "17.02 Number of trajectories is ".count($solutions)."\n";
?>