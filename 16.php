<?php

$buffer = trim(file_get_contents('./input/16.txt')," \n\r");

$buffer_binary = '';
$buffer_offset = 0;
$buffer_size = strlen($buffer)*4;

for ($i=0;$i<strlen($buffer);$i++) { $buffer_binary .= str_pad(base_convert(substr($buffer,$i,1),16,2),4,'0',STR_PAD_LEFT); }

class clsPacket {
	public $packet_version;
	public $packet_type;
	public $packet_value;
	private $subpackets;
	private $subpackets_count;
	
	private $buffer;
	private $buffer_offset;
	private $buffer_size;
	
	function __construct() {
		$this->packet_version=0;
		$this->packet_type = 0;
		$this->packet_value = 0;
		$this->subpackets = [];
		$this->subpackets_count = 0;
	}
	public function parse($buffer,$offset,$depth=0) {
		$this->buffer = $buffer;
		$this->buffer_offset = $offset;
		$this->buffer_size = strlen($buffer);
		$this->buffer_start = $offset;
		
		$this->packet_version = $this->read_bits10(3);
		$this->packet_type = $this->read_bits10(3);
		if ($this->packet_type==4) {
			$continue = true;
			$value_binary = '';
			while ($continue==true) {
				$continue = ($this->read_bits(1)=='0') ? false : true;
				$value_binary .= $this->read_bits(4);
			}
			$this->packet_value = base_convert($value_binary,2,10);
		}
		
		if ($this->packet_type!=4) {
			$mode = $this->read_bits(1);
			$bits = 0;
			$subs = 0;
			if ($mode==0) { $bits = $this->read_bits10(15); }
			if ($mode==1) { $bits = $this->read_bits10(11); }
			$i=0;
			while ($bits>0) {
				$this->subpackets[$i] = new clsPacket();
				//echo "will parse ".substr($this->buffer,$this->buffer_offset,16)."\n"; 
				$bits_parsed = $this->subpackets[$i]->parse($this->buffer,$this->buffer_offset,$depth+1);
				$this->buffer_offset += $bits_parsed;
				if ($mode==0) $bits = $bits-$bits_parsed;
				if ($mode==1) $bits = $bits-1;
				//array_push($this->subpackets,$subpacket);
				/* debug */ //echo str_pad(' ',$depth,' ')."parsed $bits_parsed bits, subpacket $i  ".$this->subpackets[$i]->display();
				$i++;
			}
			$this->subpackets_count = $i;
		}
		/*
		
    Packets with type ID 0 are sum packets - their value is the sum of the values of their sub-packets. If they only have a single sub-packet, their value is the value of the sub-packet.
    Packets with type ID 1 are product packets - their value is the result of multiplying together the values of their sub-packets. If they only have a single sub-packet, their value is the value of the sub-packet.
    Packets with type ID 2 are minimum packets - their value is the minimum of the values of their sub-packets.
    Packets with type ID 3 are maximum packets - their value is the maximum of the values of their sub-packets.
    Packets with type ID 5 are greater than packets - their value is 1 if the value of the first sub-packet is greater than the value of the second sub-packet; otherwise, their value is 0. These packets always have exactly two sub-packets.
    Packets with type ID 6 are less than packets - their value is 1 if the value of the first sub-packet is less than the value of the second sub-packet; otherwise, their value is 0. These packets always have exactly two sub-packets.
    Packets with type ID 7 are equal to packets - their value is 1 if the value of the first sub-packet is equal to the value of the second sub-packet; otherwise, their value is 0. These packets always have exactly two sub-packets.
		*/
		if (($this->packet_type==0) || ($this->packet_type==1)) {
			$this->packet_value = $this->subpackets[0]->packet_value;
			for ($i=1;$i<$this->subpackets_count;$i++) {
				if ($this->packet_type==0) $this->packet_value = $this->packet_value + $this->subpackets[$i]->packet_value;
				if ($this->packet_type==1) $this->packet_value = $this->packet_value * $this->subpackets[$i]->packet_value;
			}
		}
		if (($this->packet_type==2) || ($this->packet_type==3)) {
			$val = $this->subpackets[0]->packet_value;
			for ($i=1;$i<$this->subpackets_count;$i++) {
				if ($this->packet_type==2) { if ($this->subpackets[$i]->packet_value<$val) $val = $this->subpackets[$i]->packet_value; }
				if ($this->packet_type==3) { if ($this->subpackets[$i]->packet_value>$val) $val = $this->subpackets[$i]->packet_value; }
			}
			$this->packet_value = $val;
		}
		if ($this->packet_type==5) { 
			$this->packet_value = ($this->subpackets[0]->packet_value>$this->subpackets[1]->packet_value) ? 1 : 0;
		}
		if ($this->packet_type==6) { 
			$this->packet_value = ($this->subpackets[0]->packet_value<$this->subpackets[1]->packet_value) ? 1 : 0;
		}
		if ($this->packet_type==7) { 
			$this->packet_value = ($this->subpackets[0]->packet_value==$this->subpackets[1]->packet_value) ? 1 : 0;
		}
			
		/* debug */ // echo str_pad(' ',$depth,' ')."Parsed packet, bit count = ".($this->buffer_offset-$offset)." ".$this->display()."\n";
		return ($this->buffer_offset-$offset);
	}
	
	public function display() {
		return  " version=".$this->packet_version." type=".$this->packet_type." value=".$this->packet_value." subpackets = ".$this->subpackets_count."\n";
	}
	
	public function versionsum() {
		$sum = $this->packet_version;
		//echo "packet vsum = $sum ";
		if ($this->subpackets_count>0) {
			for ($i=0;$i<$this->subpackets_count;$i++) {
				$num = $this->subpackets[$i]->versionsum();
				//echo " + $num";
				$sum+= $num;
			}
		}
		//echo " = $sum \n";
		return $sum;
	}
	private function read_bits($bits=1) {
		
		$bits_left = $this->buffer_size - $this->buffer_offset;
		if ($bits_left<=0) return '';
		$amount = ($bits_left<$bits) ? $bits_left : $bits;
		$chunk = substr($this->buffer,$this->buffer_offset,$amount);
		$this->buffer_offset += $amount;
		return  $chunk;
	}
	private function read_bits10($bits=1) {
		$chunk = $this->read_bits($bits);
		return ($chunk=='') ? 0 : base_convert($chunk,2,10);
	}
}


/* debug */ echo substr($buffer,0,32)."\n".substr($buffer_binary,0,32)."\n";

$packets = [];

$buffer_offset = 0;
$buffer_size = strlen($buffer_binary);

$vsum = 0;
while ($buffer_size-$buffer_offset > 7) {
	$packet = new clsPacket();
	$bits_parsed = $packet->parse($buffer_binary,$buffer_offset);
	$buffer_offset +=$bits_parsed;
	$vsum += $packet->versionsum();
	array_push($packets,$packet);
	
}
echo "16.01 Solution is: ".$vsum."\n";
echo "16.02 Solution is: ".$packets[0]->packet_value;
?>