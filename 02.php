<?php

$content = trim(file_get_contents('./input/02.txt')," \n\r");
$commands = explode(chr(0x0A),$content);

$x = 0;
$y = 0;
$verb = '';
$units = 0;

foreach ($commands as $command) {
	list($verb,$units) = explode(' ',$command);
	if ($verb=='forward') $x += intval($units);
	if ($verb=='down') $y += intval($units);
	if ($verb=='up') $y -= intval($units);
}

echo "02.01 x=$x y=$y result=".($x*$y)."\n";

$x = 0;
$y = 0;
$aim = 0;
$verb = '';
$units = 0;

foreach ($commands as $command) {
	list($verb,$units) = explode(' ',$command);
	if ($verb=='forward') { $x += intval($units); $y+= $aim*intval($units); }
	if ($verb=='down') $aim += intval($units);
	if ($verb=='up') $aim -= intval($units);
}

echo "02.02 x=$x y=$y result=".($x*$y)."\n";

?>


$counter = 0;
for ($i=1;$i<count($numbers);$i++) {
	$counter += ($numbers[$i]>$numbers[$i-1]) ? 1 : 0;
}
echo "01.01 solution : $counter \n";

$previous = $numbers[0]+$numbers[1]+$numbers[2];
$counter = 0;
for ($i=2;$i<count($numbers)-1;$i++) {
	$sum = $previous - $numbers[$i-2] + $numbers[$i+1];
	$counter += ($sum>$previous) ? 1 : 0;
	$previous = $sum;
}
echo "01.02 solution : $counter \n";
?>
