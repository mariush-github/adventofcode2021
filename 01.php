<?php

$content = trim(file_get_contents('./input/01.txt')," \n\r");
$numbers = explode(chr(0x0A),$content);

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
