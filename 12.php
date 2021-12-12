<?php

$content = trim(file_get_contents('./input/12.txt')," \n\r");
$content = str_replace(chr(0x0D),'',$content);
$lines = explode(chr(0x0A),$content);


$paths = array();
$solutions = array();

$addednodes='';
foreach ($lines as $line) {
	list($a,$b) = explode('-',$line);
	array_push($paths,array($a,$b));
	array_push($paths,array($b,$a));
}


// recursive search until we end up with 'end';

function find_chains($path,$method = 1, $depth=0) {
	global $paths;
	global $solutions;
	$havetwice=false;
	$small='';
	if ($depth>100) die(); // something went really wrong if this is the case
	$lastnode = $path[count($path)-1];
	//echo "find_chains ".implode('-',$path)."\n";
	// track the small caves used so far, because we can't go through them twice
	foreach($path as $node) {
		if (ctype_lower($node)==true) {
			if ($method==1) $small .= ':'.$node.':';
			if ($method==2) {
				if (strpos($small,':'.$node.':')!==FALSE) {
					$havetwice=true;
				} else {
					$small .= ':'.$node.':';
				}
			}
		}
	}
	// find every path that has the last node in the solution found so far 
	$jumpto = array();
	foreach ($paths as $nextpath) {
		//echo "testing ".$nextpath[0].'-'.$nextpath[1]."\n";
		if ($nextpath[0] == $lastnode) { // possible path
			$goodpath=true;
			if (ctype_lower($nextpath[1])==true) { 
				if ($method==1) {
					if (strpos($small,':'.$nextpath[1].':')!==FALSE) $goodpath=false; 
				}
				if ($method==2) {
					if (strpos($small,':'.$nextpath[1].':')!==FALSE) {
						// if already used, only allow if we don't have a small cave twice already in path
						if (($havetwice==TRUE) || ($nextpath[1]=='start')) $goodpath=false; 
					}
				}
			}
			if ($goodpath==true) array_push($jumpto,$nextpath[1]);
		}
	}
	foreach ($jumpto as $jump) {
		$newpath = $path; array_push($newpath, $jump);
		if ($jump=='end') {
			$sol = implode('-',$newpath); $solutions[$sol]=1;
		} else {
			$ret = find_chains($newpath,$method,$depth+1);
		}
	}
}
$solutions = array();
foreach ($paths as $path) {
	if ($path[0]=='start') $result = find_chains($path,1);
}
echo "12.01 There are ".count($solutions)." paths possible.\n";

$solutions = array();
foreach ($paths as $path) {
	if ($path[0]=='start') $result = find_chains($path,2);
}
echo "12.02 There are ".count($solutions)." paths possible.\n";

?>
