<?php

namespace VOP\Utils;

class UniqueUid {
    
    public static function generateUid($length){        
        $token = "";        
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz0123456789";       
        //$codeAlphabet .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[mt_rand(0, strlen($codeAlphabet) - 1)];
        }
        return $token;        
    }
       
}

?>
