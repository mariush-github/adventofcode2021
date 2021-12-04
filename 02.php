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
