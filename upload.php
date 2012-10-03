<?php

/**
 * Upload
 * 
 * @category CSV
 * @package CSVParser
 * @author Giovanni Ramos <giovannilauro@gmail.com>
 * @copyright 2012, Giovanni Ramos
 * @since 2012-09-27 
 * @version 1.0
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 *
 * */
class Upload
{
    // Upload Limit (Default 5 = 5MB)
    const LIMIT_UPLOAD = 5;

    // Constructor
    function Upload()
    {
        if (!isset($_FILES['uploadfile'])):
            exit;
        else:
            $csv_file = $_FILES['uploadfile'];

            $name = $csv_file['name'];
            $size = $csv_file['size'];
            $erro = $csv_file['error'];
            $temp = $csv_file['tmp_name'];

            // Upload OK
            if ($erro == 0):
                // List of allowed extensions
                $allowedExtensions = array('.xls', '.xlsx', '.csv');

                // Picks up at the file extension
                $extension = strrchr(strtolower(stripslashes($name)), '.');

                // Checks whether the uploaded file is a spreadsheet
                if (in_array($extension, $allowedExtensions)):
                    // Checks if the file size is within the allowable limit
                    if ($size < (self::LIMIT_UPLOAD * 1000000)):
                        $name = self::normalize($name);
                        $name_rand = rand(0000, 9999) . basename($name);

                        // Move the file to the folder: storage
                        if (move_uploaded_file($temp, 'storage/' . $name_rand)):
                            echo '{ status: "ok", fileName: "' . $name_rand . '" }';
                        else:
                            echo '{ status: "error" }';
                        endif;
                    endif;
                else:
                    echo '{ status: "error" }';
                endif;
            else:
                echo '{ status: "error" }';
            endif;
        endif;
    }

    /**
     * Converts a string of special characters
     *
     * @param string $term The input string
     * @return string Returns the converted string
     */
    function normalize($term)
    {
        if (is_array($term)):
            foreach ($term as $value)
                return normalize($value);
        endif;

        $chars = array(
            'a' => '/à|á|â|ã|ä|å|æ/',
            'e' => '/è|é|ê|ë/',
            'i' => '/ì|í|î|ĩ|ï/',
            'o' => '/ò|ó|ô|õ|ö|ø/',
            'u' => '/ù|ú|û|ű|ü|ů/',
            'A' => '/À|Á|Â|Ã|Ä|Å|Æ/',
            'E' => '/È|É|Ê|Ë/',
            'I' => '/Ì|Í|Î|Ĩ|Ï/',
            'O' => '/Ò|Ó|Ô|Õ|Ö|Ø/',
            'U' => '/Ù|Ú|Û|Ũ|Ü|Ů/',
            '_' => '/`|´|\^|~|¨|ª|º|©|®/',
            'c' => '/ć|ĉ|ç/',
            'C' => '/Ć|Ĉ|Ç/',
            'd' => '/ð/',
            'D' => '/Ð/',
            'n' => '/ñ/',
            'N' => '/Ñ/',
            'r' => '/ŕ/',
            'R' => '/Ŕ/',
            's' => '/š/',
            'S' => '/Š/',
            'y' => '/ý|ŷ|ÿ/',
            'Y' => '/Ý|Ŷ|Ÿ/',
            'z' => '/ž/',
            'Z' => '/Ž/',
        );

        return preg_replace($chars, array_keys($chars), $term);
    }

}

// Starting upload
new Upload();
