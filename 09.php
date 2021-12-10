<?php

$content = trim(file_get_contents('./input/09.txt')," \n\r");
$content = str_replace(array("\r","\n", ' '),'',$content);

// test 
$width = 10;
$height= 5;
// comment these for test mode
$width=100;
$height=100;

// build a frame around the map and mark all the 9 in the map
// basically build a black and white bitmap

$edges = array();
for ($y=0;$y<=$height+1;$y++) {
	$edges[$y]= array();
	$edges[$y][0] = 1;
	$edges[$y][$width+1] = 1;
}
for ($x=0; $x<=$width+1; $x++) {
	$edges[0][$x] = 1;
	$edges[$height+1][$x] = 1;
}

function count_pixels($startx,$starty) {
	global $edges;
	$pixels = array();
	array_push($pixels, array($startx,$starty));
	$edges[$starty][$startx]=1;
	for ($i=0;$i<count($pixels);$i++) {
		$x = $pixels[$i][0];
		$y = $pixels[$i][1];
		if ($edges[$y][$x-1]==0) { array_push($pixels,array($x-1,$y));$edges[$y][$x-1]=1; }
		if ($edges[$y][$x+1]==0) { array_push($pixels,array($x+1,$y));$edges[$y][$x+1]=1; }
		if ($edges[$y-1][$x]==0) { array_push($pixels,array($x,$y-1));$edges[$y-1][$x]=1; }
		if ($edges[$y+1][$x]==0) { array_push($pixels,array($x,$y+1));$edges[$y+1][$x]=1; }
	}
	return count($pixels);
}

$lows = array();
for ($y=0;$y<$height;$y++) {
	for ($x=0;$x<$width;$x++) {
		$number = intval(substr($content,$y*$width+$x,1));
		//
		$edges[$y+1][$x+1] = ($number==9) ? 1 : 0;
		$n = array();
		if ($x!=0) array_push($n,intval(substr($content,$y*$width+$x-1,1)));
		if ($x!=$width-1) array_push($n,intval(substr($content,$y*$width+$x+1,1)));
		if ($y!=0) array_push($n,intval(substr($content,($y-1)*$width+$x,1)));
		if ($y!=$height-1) array_push($n,intval(substr($content,($y+1)*$width+$x,1)));
		$lowest = true;
		foreach ($n as $nr) {
			if ($nr<=$number) $lowest=false;
		}
		if ($lowest==true) {
			array_push($lows,array($x,$y,$number));
		}
	}
}
$sum = 0;
foreach ($lows as $low) { $sum += $low[2]+1; }

echo "09.01 Solution is : $sum\n";

// let's see the edges 
/* kinda hard to show a 100x100 in console, so uncomment if test mode only

for ($y=0;$y<=$height+1;$y++) {
	for ($x=0;$x<=$width+1;$x++) {
		$pixel = $edges[$y][$x]; 
		echo $pixel;
	}
	echo "\n";
}
echo "\n\n"; 
*/

$pixelcounts = array();
foreach ($lows as $low) {
	$value = count_pixels($low[0]+1,$low[1]+1);
	$pixelcounts[$low[0]."-".$low[1]] = $value;
}
arsort($pixelcounts);
echo "09.02 Solution is : ".array_shift($pixelcounts)*array_shift($pixelcounts)*array_shift($pixelcounts)."\n";
?>
