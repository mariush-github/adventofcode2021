<?php

$content = trim(file_get_contents('./input/04.txt')," \n\r");
$lines = explode(chr(0x0A),$content);

$numbers = explode(",",$lines[0]);

$games = array(); // holds all bingo tickets, a matrix for each one. 
$solutions = array();  

$linesCount = count($lines);
$gamesCount = -1;

$linesOffset = 2;

// empty matrix. 
// Make it on purpose larger by one in both directions because we reuse this
// to keep track which numbers were detected and keep count of how many numbers on
// each line and columns were found on the extra row and column 

$em = array();
for ($y=0;$y<=5;$y++) {
	$em[$y]=array();
	for ($x=0;$x<=5;$x++) {
		$em[$y][$x] = 0;
	}
}
	
while ($linesOffset <= $linesCount) {
	$gamesCount++;
	$game = $em;
	$solution = $em;
	
	for ($y=0;$y<5;$y++) {
		$line = trim(str_replace('  ',' ',$lines[$linesOffset+$y]));
		$values = explode(' ',$line);
		for ($x=0;$x<5;$x++) { $game[$y][$x] = intval($values[$x]); }
	}
	$games[$gamesCount] = $game;
	$solutions[$gamesCount] = $solution;
	$linesOffset +=6; // jump to next bingo ticket
}

echo "Loaded ".(count($games))." bingo tickets.\n";

function check_number($number,$gameid) {
	global $games,$solutions;
	for ($y=0;$y<5;$y++) {
		for ($x=0;$x<5;$x++) {
			if ($games[$gameid][$y][$x]==$number) {
				$solutions[$gameid][$y][$x] = 1;
				$solutions[$gameid][$y][5]++;
				$solutions[$gameid][5][$x]++;
			}
		}
	}
}

function ticket_won($gameid) {
	global $solutions;
	for ($i=0;$i<5;$i++) {
		if ($solutions[$gameid][$i][5]==5) return TRUE;
		if ($solutions[$gameid][5][$i]==5) return TRUE;
	}
	return FALSE;
}

$firstSolutionFound = false;
$firstID = 0;
$firstSum = 0;
$firstNum = 0;
$lastID = 0;
$lastSum = 0;
$lastNum = 0;

foreach ($numbers as $number) {
	for ($i=0;$i<count($games);$i++) {
		$won = ticket_won($i); // if the ticket was already won, no point adding numbers to it
		if ($won==false) {
			$result = check_number($number,$i);
			$won = ticket_won($i);
			if ($won==true) {
				$sum = 0;
				for ($y=0;$y<5;$y++) {
					for ($x=0;$x<5;$x++) {
						if ($solutions[$i][$y][$x]==0) $sum += $games[$i][$y][$x];
					}
				}

				if ($firstSolutionFound==false) {
					$firstSolutionFound = true;
					$firstID  = $i;
					$firstNum = $number;
					$firstSum = $sum;
				}
				$lastID  = $i;
				$lastNum = $number;
				$lastSum = $sum;
			}
		}
	}
}
echo "04.01\n\nFirst winning ticket : $firstID \n";
echo "Sum : $firstSum  Number : $firstNum Result = ".($firstSum*$firstNum)."\n";

echo "04.02\n\nLast winning ticket : $lastID \n";
echo "Sum : $lastSum  Number : $lastNum Result = ".($lastSum*$lastNum)."\n";
?>
