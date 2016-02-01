<?php
namespace VOP\Utils;

class Password {
   
    public static function validate_password($password, $correct_hash) {
        $PBKDF2_HASH_ALGORITHM = "sha256";
        $PBKDF2_ITERATIONS = 537;
        $salt = 'RaA6EnY4vSk66fr74IjNB/kR+/3IpwiF';

        $pbkdf2 = base64_decode($correct_hash);
        return Password::slow_equals(
                        $pbkdf2, Password::pbkdf2(
                                $PBKDF2_HASH_ALGORITHM, $password, $salt, (int) $PBKDF2_ITERATIONS, strlen($pbkdf2), true
                        )
        );
    }

    public static function create_hash($password) {
        
        $PBKDF2_HASH_ALGORITHM = "sha256";
        $PBKDF2_ITERATIONS = 537;
        $PBKDF2_HASH_BYTE_SIZE = 24;

        $salt = 'RaA6EnY4vSk66fr74IjNB/kR+/3IpwiF';
        
        return base64_encode(Password::pbkdf2(
                                $PBKDF2_HASH_ALGORITHM, $password, $salt, $PBKDF2_ITERATIONS, $PBKDF2_HASH_BYTE_SIZE, true
                        ));
    }

    public static function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false) {
         
        $algorithm = strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true))
            die('PBKDF2 ERROR: Invalid hash algorithm.');
        if ($count <= 0 || $key_length <= 0)
            die('PBKDF2 ERROR: Invalid parameters.');

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }
    
     
    public static function slow_equals($a, $b) {
        $diff = strlen($a) ^ strlen($b);
        for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }
     

}