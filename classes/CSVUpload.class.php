<?php

/**
 * CSVUpload
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
class CSVUpload
{
    const LIMIT_UPLOAD = 5; // Upload Limit (Default 5 = 5MB)

    /**
     * Constructor
     *
     * @param array $file File loaded via Ajax
     */

    function CSVUpload($file = null)
    {
        if (is_null($file)):
            exit;
        else:
            $name = $file['name'];
            $size = $file['size'];
            $type = $file['type'];
            $erro = $file['error'];
            $temp = $file['tmp_name'];

            // No error during load
            if ($erro == 0):
                // Checks if the file size is within the allowable limit
                if ($size < (self::LIMIT_UPLOAD * 1000000)):
                    // Picks up at the file extension
                    $extension = strrchr(strtolower(stripslashes($name)), '.');

                    // List of mime types supported
                    $mime_supported = array('text/comma-separated-values', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext');

                    // Checks whether the uploaded file is a spreadsheet
                    if ($extension == '.csv' && in_array($type, $mime_supported)):

                        // Converts all special characters
                        $name = self::normalize($name);

                        // Generates a new filename
                        $new_name = rand(0000, 9999) . basename($name);

                        // Move the file to the folder: storage
                        if (move_uploaded_file($temp, 'storage/' . $new_name)):
                            echo '{ status: "ok" , message: "<h3>Loading file: ' . $name . '</h3>" , file_name: "' . $new_name . '" }';
                        else:
                            echo '{ status: "error" , message: "<h3>Could not load file: ' . $name . '</h3>" }';
                        endif;
                    else:
                        echo '{ status: "error" , message: "<h3>File type not supported</h3>" }';
                    endif;
                else:
                    echo '{ status: "error" , message: "<h3>File size limit exceeded</h3>" }';
                endif;
            else:
                echo '{ status: "error" , message: "<h3>Could not load file</h3>" }';
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
