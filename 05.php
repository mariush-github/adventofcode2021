<?php

$content = trim(file_get_contents('./input/05.txt')," \n\r");
$content = str_replace(' -> ',',',$content);
$content = str_replace(' ','',$content);
$lines = explode(chr(0x0A),$content);

$map = array();

$x1 = 0;
$y1 = 0;
$x2 = 0;
$y2 = 0;
$inc = 0;
for ($i=0; $i<=1000000; $i++) {
	$map[$i] = 0;
}
	

foreach ($lines as $line) {
	$l = explode(',',$line);
	$x1 = intval($l[0]);
	$y1 = intval($l[1]);
	$x2 = intval($l[2]);
	$y2 = intval($l[3]);
	
	if ($y1==$y2) {
		$inc = ($x1<$x2) ? 1 : -1;
		$i=$x1; while ($i!=$x2+$inc) { $map[$y1*1000 + $i]++; $i+= $inc; }
	}
	if ($x1==$x2) {
		$inc = ($y1<$y2) ? 1 : -1;
		$i=$y1; while ($i!=$y2+$inc) { $map[$i*1000 + $x1]++; $i+= $inc; }
	}
}

$total = 0;
for ($i=0; $i<=1000000; $i++) {
	if ($map[$i] >1) $total++;
}
echo "05.01 Line intersection count : $total\n";
foreach ($lines as $line) {
	$l = explode(',',$line);
	$x1 = intval($l[0]);
	$y1 = intval($l[1]);
	$x2 = intval($l[2]);
	$y2 = intval($l[3]);
	
	
	if (($x1!=$x2) && ($y1!=$y2)) {
		$incx = ($x1<$x2) ? 1 : -1;
		$incy = ($y1<$y2) ? 1 : -1;
		$i=$x1;
		$j=$y1;
		while ($i!=$x2+$incx) { $map[$j*1000 + $i]++; $i += $incx; $j += $incy;}
	}
}	

$total = 0;
for ($i=0; $i<=1000000; $i++) {
	if ($map[$i] >1) $total++;
}

echo "05.02 Line intersection count : $total\n";
?>