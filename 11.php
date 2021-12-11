<?php

$content = trim(file_get_contents('./input/11.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);
$lines = explode(chr(0x0A),$content);
$map = array();

function visualize_map() {
	global $map;
	for ($y=0;$y<=11;$y++) {
		for ($x=0;$x<=11;$x++) {
			echo str_pad($map[$y][$x],3,' ',STR_PAD_LEFT);
		}
		echo "\n";
	}
}

function update_map() {
	global $map;
	$cells = array();
	$total = 0;
	for ($y=1;$y<=10;$y++) {
		for ($x=1;$x<=10;$x++) {
			$map[$y][$x]++;
			if ($map[$y][$x]==10) array_push($cells,array($x,$y));
		}
	}
	while (count($cells)>0) {
		$cell = array_shift($cells);
		$x = $cell[0];
		$y = $cell[1];
		for ($j=$y-1;$j<=$y+1;$j++) {
			for ($i=$x-1;$i<=$x+1;$i++) {
				if ($map[$j][$i]<10) { $map[$j][$i]++; if ($map[$j][$i]==10) array_push($cells,array($i,$j)); }
			}
		}
	}
	//visualize_map();
	for ($y=1;$y<=10;$y++) {
		for ($x=1;$x<=10;$x++) {
			if ($map[$y][$x]==10) { $map[$y][$x] = 0; $total++; }
		}
	}
	return $total;
}


for ($y=0;$y<=11;$y++) {
	$map[$y]= array();
	$map[$y][0]=11;
	$map[$y][11]=11;
	for ($x=1;$x<=10;$x++) {
		$value = 0; if (($y==0) || ($y==11)) $value=11;
		$map[$y][$x]=11;
	}
}

for ($y=1;$y<=10;$y++) {
	$line = $lines[$y-1];
	for ($x=1;$x<=10;$x++) {
		$map[$y][$x]=intval(substr($line,$x-1,1));
	}
}
$sum = 0;
for ($i=1;$i<=100;$i++) {
	$value = update_map();  
	$sum += $value;
}
echo "11.01 Solution is $sum\n";
$step = 100;
$value = 0;
while ($value!=100) {
	$value = update_map();
	$step++;
}
echo "11.02 Solution is $step\n";
?>