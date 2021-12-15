<?php

$part = 1;  // change this to 2 to get solution to part 2

$content = trim(file_get_contents('./input/15.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);
$lines = explode(chr(0x0A),$content);

$width = strlen($lines[0]);
$height = count($lines);

$matrix = [ [0,1,2,3,4], [1,2,3,4,5], [2,3,4,5,6] , [3,4,5,6,7] , [4,5,6,7,8] ];
$map = array();
if ($part==2) {
	$tiles = [];
	for ($i=0;$i<9;$i++) $tiles[$i] = $lines;
	for ($y=0;$y<$height;$y++) {
		// very lazy mass flipping of numbers instead of converting each char to numbers and so on.
		$tiles[1][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[1][$y]);
		$tiles[2][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[2][$y]);
		$tiles[3][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[3][$y]);
		$tiles[4][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[4][$y]);
		$tiles[5][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[5][$y]);
		$tiles[6][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[6][$y]);
		$tiles[7][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[7][$y]);
		$tiles[8][$y] = str_replace(['1','2','3','4','5','6','7','8','9'],['A','B','C','D','E','F','G','H','I'],$tiles[8][$y]);
		
		$tiles[1][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['2','3','4','5','6','7','8','9','1'],$tiles[1][$y]);
		$tiles[2][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['3','4','5','6','7','8','9','1','2'],$tiles[2][$y]);
		$tiles[3][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['4','5','6','7','8','9','1','2','3'],$tiles[3][$y]);
		$tiles[4][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['5','6','7','8','9','1','2','3','4'],$tiles[4][$y]);
		$tiles[5][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['6','7','8','9','1','2','3','4','5'],$tiles[5][$y]);
		$tiles[6][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['7','8','9','1','2','3','4','5','6'],$tiles[6][$y]);
		$tiles[7][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['8','9','1','2','3','4','5','6','7'],$tiles[7][$y]);
		$tiles[8][$y] = str_replace(['A','B','C','D','E','F','G','H','I'],['9','1','2','3','4','5','6','7','8'],$tiles[8][$y]);
	}

	$map = [];
	for ($j=0;$j<5;$j++) {
		for ($y=0;$y<$height;$y++) {
			$line = $tiles[$matrix[$j][0]][$y].$tiles[$matrix[$j][1]][$y].$tiles[$matrix[$j][2]][$y].$tiles[$matrix[$j][3]][$y].$tiles[$matrix[$j][4]][$y];
			array_push($map,$line);
		}
	}
	$content = implode('',$map);
	$width = $width*5;
	$height = $height*5;
}

$data = str_replace(chr(0x0A),'',$content);

function get_depth($x,$y) {
	global $data,$width,$height;
	if (($x<0) || ($y<0) || ($x>=$width) || ($y>=$height)) return 10;
	return intval(substr($data,$y*$width+$x,1));
}

function was_used($x,$y,$path) {
	for ($i=count($path)-1;$i>0;$i--) {
		if (($path[$i][0]==$x) && ($path[$i][1]==$y)) return TRUE;
	}
	return FALSE;
}

$paths = array();
$solutions = array();

$directions = [[0,-1], [-1,0], [0,1],[1,0]]; // up, left, down, right

$paths[0] = [];
array_push($paths[0],[0,2,0,0]); // LENGTH, COUNT, LAST_X, LAST_Y
array_push($paths[0],[0,0]);

$lengths = []; // cache the lengths for each point
$lengths[0] = 0; 

$level = 0;
while (count($paths)>0) {
	
	$newpaths = [];
	
	while (count($paths)>0) {
		$path = array_shift($paths);
		list($path_length,$path_count,$x,$y) = $path[0];
		if (isset($lengths[$y*1024+$x])==true) {
			if ($lengths[$y*1024+$x]<$path_length) { 
			// ignore this one because there's another path reaching this point that's shorter
			$path_length = -1;
			}
		}	

		if ($path_length!=-1) {
			if (($x==$width-1) && ($y==$height-1)) {
				array_push($solutions,$path);
			} else {
				foreach ($directions as $dir) {
					$a = $x+$dir[0];
					$b = $y+$dir[1];
					$valid = true;
					if (($a<0) || ($b<0) || ($a>=$width) || ($b>=$height)) $valid=false;
					if ($valid==true) {
						if (was_used($a,$b,$path)==FALSE) {  // can't go twice through the path
							$newpath = $path; 
							$newpath[0][0] = $newpath[0][0] + get_depth($a,$b);
							$newpath[0][1]++;
							$newpath[0][2]=$a;
							$newpath[0][3]=$b;
							array_push($newpath,[$a,$b]);
							$can_add = true;
							if (isset($lengths[$b*1024+$a])==true) {
								// only add to the array if don't have a path to this point ord
								// the current path has a higher length
								if ($newpath[0][0]>=$lengths[$b*1024+$a]) $can_add=false;
							}
							if ($can_add==true) {
								array_push($newpaths,$newpath);
								$lengths[$b*1024+$a] = $newpath[0][0];
							}
						}
					}
				}
			}
		}
	}
	$paths = $newpaths;
	/*
	echo "pass $level\n";
	foreach ($paths as $path) {
		//echo json_encode($path)."\n";
	}
	$level++;
	//if ($level==10) die();
	*/
}


function compare_shortlength($a,$b) {
    if ($a[0][0] == $b[0][0]) {
        return 0;
    }
    return ($a[0][0] < $b[0][0]) ? -1 : 1;
}
usort($solutions,"compare_shortlength");

/* 
foreach ($solutions as $sol) {
	echo json_encode($sol)."\n";
} 
*/

/* 
for ($y = 0;$y<$height;$y++) {
 for ($x=0;$x<$width;$x++) {
  $is_node = false;
  for ($j=1;$j<count($solutions[0]);$j++) {
	if (($solutions[0][$j][0]==$x) && ($solutions[0][$j][1]==$y)) $is_node=true;
  }	  
  echo ($is_node==true)? '*' : ' ';
  echo get_depth($x,$y).' ';
 }
 echo "\n";
} 
*/
if ($part==1) echo "15.01 Solution is: ".$solutions[0][0][0]."\n";

if ($part==2) echo "15.02 Solution is: ".$solutions[0][0][0]."\n";

?>