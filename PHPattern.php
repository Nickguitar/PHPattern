#!/usr/bin/php
<?php

function fuzz($len){
	if($len == 0){
		echo cyan("[i] No length provided, using the max: 3767680.\n");
		$len = 3767680; // max
		sleep(1);
	}
	$len = (int)$len;
	$junk = '';
	foreach(range("A","z") as $chr1){
		foreach(range("A","z") as $chr2){
			for($i=10;$i<100;$i++){
				if(strlen($junk) >= $len) return substr($junk, 0, $len); // returns the exact $len
				$junk .= $chr1.$chr2.$i; // AA41, AA42,...BC84,BC85, etc...
			}
		}
	}
	if($len < 1211040) return $junk; //max length of the above $junk generation

	foreach(range("A","z") as $chr1){
		foreach(range("A","z") as $chr2){
			for($i=10;$i<100;$i++){
				if(strlen($junk) >= $len) return substr($junk, 0, $len);
				$junk .= $chr1.$i.$chr2; // A67A, A88A,...F99j, etc...
			}
		}
	}
	if($len >= 1211040 && $len < 2422080) return $junk;

	foreach(range("A", "z") as $chr1){
		foreach(range("A", "z") as $chr2){
			for($i=0;$i<10;$i++){
				for($j=0;$j<10;$j++){
					if(strlen($junk) >= $len) return substr($junk, 0, $len);
					$junk .= $i.$chr1.$chr2.$j; // 1AA1, 1AA2,...9Hc4, etc...
				}
			}
		}
	}
	return $junk;
}

function hex2ascii($hex) {
    $ascii = '';
    for($i=0;$i<strlen($hex);$i+=2) $ascii .= chr(hexdec(substr($hex,$i,2)));
    return $ascii;
}

function find_offset($offset, $len=0){
	if(!preg_match("/^[a-fA-F0-9]{8}$/", $offset)) die(red("[-] Wrong offset format (it must be hexadecimal, len=8!)"));
	$junk = fuzz($len); 
//	$junk = fuzz(0); //generate the biggest $junk (to avoid asking user for length)
	$offset_ascii = strrev(hex2ascii($offset)); //reverse to change endianess
	$pay_length = strpos($junk, $offset_ascii);
	if($pay_length > 0)
		return $pay_length;
	return false;
}

function green($str){
	return "\e[92m".$str."\e[0m";
}
function red($str){
	return "\e[91m".$str."\e[0m";
}
function yellow($str){
	return "\e[93m".$str."\e[0m";
}
function cyan($str){
	return "\e[96m".$str."\e[0m";
}

#banner

echo "
  _____  _    _ _____      _   _
 |  __ \| |  | |  __ \    | | | |
 | |__) | |__| | |__) |_ _| |_| |_ ___ _ __ _ __
 |  ___/|  __  |  ___/ _` | __| __/ _ \ '__| '_ \
 | |    | |  | | |  | (_| | |_| ||  __/ |  | | | |
 |_|    |_|  |_|_|   \__,_|\__|\__\___|_|  |_| |_|
                      ".green("0x7359")."  -  ".yellow("Nicholas Ferreira")."

 Generate patterns for buffer overflow PoC
 and retrieve the EIP address (if found)

";

#parsing

if(!isset($argv[1])) die(cyan("Usage: ".$argv[0]." [fuzz/find] [length/offset]"));

switch($argv[1]){
	case "fuzz":
		if(!isset($argv[2]))
			die(fuzz(0));
		else
			die(fuzz($argv[2]));
	break;

	case "find":
		if(!isset($argv[2]))
			die(cyan("Usage: ".$argv[0]." [fuzz/find] [length/offset]"));
		if(!isset($argv[3]))
			$offset = find_offset($argv[2], 0);
		else
			$offset = find_offset($argv[2], $argv[3]);

		if($offset)
			die("[+] You need ".green($offset)." bytes to reach EIP.");
		die(red("[-] No match found."));

	break;
	default:
		die(cyan("Usage: ".$argv[0]." [fuzz/find] [length/offset]"));
}
