<?php

namespace App\Http\Controllers;

class decController
{
    /* 10进制转64进制 */
	public static function dec2s4($dec) {
	    $base = '0123456789_-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $result = '';

	    do {
	        $result = $base[$dec % 64] . $result;
	        $dec = intval($dec / 64);
	    } while ($dec != 0);

	    return $result;
	}

	/* 64进制转10进制 */
	public static function s42dec($sixty_four) {
	    $base_map = array ('0' => 0,'1' => 1,'2' => 2,'3' => 3,'4' => 4,'5' => 5,'6' => 6,'7' => 7,'8' => 8,'9' => 9,'_' => 10,'-' => 11,'a' => 12,'b' => 13,'c' => 14,'d' => 15,'e' => 16,'f' => 17,'g' => 18,'h' => 19,'i' => 20,'j' => 21,'k' => 22,'l' => 23,'m' => 24,'n' => 25,'o' => 26,'p' => 27,'q' => 28,'r' => 29,'s' => 30,'t' => 31,'u' => 32,'v' => 33,'w' => 34,'x' => 35,'y' => 36,'z' => 37,'A' => 38,'B' => 39,'C' => 40,'D' => 41,'E' => 42,'F' => 43,'G' => 44,'H' => 45,'I' => 46,'J' => 47,'K' => 48,'L' => 49,'M' => 50,'N' => 51,'O' => 52,'P' => 53,'Q' => 54,'R' => 55,'S' => 56,'T' => 57,'U' => 58,'V' => 59,'W' => 60,'X' => 61,'Y' => 62,'Z' => 63);
	    $result = 0;
	    $len = strlen($sixty_four);

	    for ($n = 0; $n < $len; $n++) {
	        $result *= 64;
	        $result += $base_map[$sixty_four{$n}];
	    }

	    return $result;
	}
}
