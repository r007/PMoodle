<?php
/*
 *
 * @copyright &copy; 2006 The Open University
 * @author d.t.le@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package synch
 * This is a simple class to generate globally unique ids
 */


class uuid {

    static function getUniqueID() {

        // The field names refer to RFC 4122 section 4.1.2

        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
                        mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
                        mt_rand(0, 65535), // 16 bits for "time_mid"
                        mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
                        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
                           // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
                           // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
                           // 8 bits for "clk_seq_low"
                        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );

    }

    static public function getUniqueID2() {

       // The field names refer to RFC 4122 section 4.1.2

       $uuid = sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
           mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
           mt_rand(0, 65535), // 16 bits for "time_mid"
           mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
           bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
               // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
               // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
               // 8 bits for "clk_seq_low"
           mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
       );

       $uuid = 'id' . str_replace('-', '', $uuid);

       return $uuid;

    }

}

?>
