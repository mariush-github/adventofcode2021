<?php

$content = trim(file_get_contents('./input/13.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);
$lines = explode(chr(0x0A),$content);

$width = -1;
$height = -1;
$i=0;
$line = trim($lines[0]);
$page = array();
$folds = array();

function visualize_page($w=0,$h=0) {
	global $page,$width,$height;
	for ($y=0;$y<(($h!=0) ? $h : $height);$y++) {
		for ($x=0;$x<(($w!=0) ? $w : $width);$x++) {
			echo isset($page[$y][$x])==TRUE ? $page[$y][$x] : '.';
		}
		echo "\n";
	}
	echo "\n";
}
function count_dots($w=0,$h=0) {
	global $page,$width,$height;
	$total = 0;
	for ($y=0;$y<(($h!=0) ? $h : $height);$y++) {
		for ($x=0;$x<(($w!=0) ? $w : $width);$x++) {
			$total += isset($page[$y][$x])==TRUE ? (($page[$y][$x]=='#') ? 1 : 0) : 0;
		}
	}
	return $total;
}

while ($line!='') {
	//echo $line ."\n";
	$numbers = explode(',',$lines[$i]);
	$x = intval($numbers[0]); $y = intval($numbers[1]);
	if (isset($page[$y])==FALSE) $page[$y] = array();
	$page[$y][$x] = '#';
	$i++;
	$line = trim($lines[$i]);
}
$i++;
while ($i<count($lines)) {
	$line = str_replace('fold along ','',$lines[$i]);
	list($xy,$offset) = explode('=',$line); $offset = intval($offset);
	if ($xy=='x') { $width = ($width<$offset) ? $offset : $width; }
	if ($xy=='y') { $height = ($height<$offset) ? $offset : $height; }
	array_push($folds,array($xy,$offset));
	$i++;
}
$width = $width*2+1;
$height = $height*2+1;

echo " width = $width height=$height dots=".count($page)." folds=".count($folds)."\n";
//visualize_page();
$firstfold=true;

$solution1 = 0;

foreach ($folds as $fold) {
	$direction = $fold[0];
	$offset = $fold[1];
	if ($direction=='y') {
		$yy = $offset+1;
		for ($y=$offset-1;$y>=0;$y--) {
			for ($x=0;$x<$width;$x++) { 
				if (isset($page[$y][$x])==FALSE) $page[$y][$x]='.';
				$page[$y][$x] = isset($page[$yy][$x])==TRUE ? (($page[$yy][$x]=='#') ? '#' : $page[$y][$x]) : $page[$y][$x];
			}
			$yy++;
		}
	$height = $offset;
	//visualize_page();
	}
	if ($direction=='x') {
		$xx = $offset+1;
		for ($x=$offset-1;$x>=0;$x--) {
			for ($y=0;$y<$height;$y++) {
				if (isset($page[$y][$x])==FALSE) $page[$y][$x]='.';
				$page[$y][$x] = isset($page[$y][$xx])==TRUE ? (($page[$y][$xx]=='#') ? '#' : $page[$y][$x]) : $page[$y][$x];
			}
			$xx++;
		}
	$width = $offset;
	//visualize_page();
	}
	if ($firstfold==true) {
		$solution = count_dots();
		$firstfold=false;
	}
}

echo "13.01 Number of dots is ".$solution."\n";
echo "13.02 Solution is:\n";
visualize_page();

?>