<?php

/**
 * CSV-Parser is a parser that reads CSV files and assembles as a result of 
 * analyzing a data set to save in a database
 * 
 * @category CSV
 * @package CSV-Parser
 * @author Giovanni Ramos <giovannilauro@gmail.com>
 * @copyright 2012-2014, Giovanni Ramos
 * @since 2012-09-27 
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 *
 * */
class CSVParser
{
    const CSV_LENGTH = 4096;
    const CSV_DELIMITER = ';';

    private $_data = array();
    private $_rows = array();
    private $_columns = array();
    private $_separator = array();
    private $_header = null;
    private $_dataset = null;
    private $_start = 0;
    private $_end = 0;

    /**
     * Prepares the spreadsheet for reading fields
     * 
     * @param string $file
     * @return string
     */
    public function execute($file = null)
    {
        if (($handle = @fopen($file, "r")) === FALSE) {
            exit('<h3>The file "' . $file . '" does not exist!!</h1>');
        }

        $data = array();

        $this->csv_range($this->_start, $this->_end);

        $headline = fgetcsv($handle, self::CSV_LENGTH, self::CSV_DELIMITER);
        foreach ($headline as $title) {
            $data[0][] = $title = mb_strtoupper(preg_replace('~[^[:alnum:]\s]~', '', $title));

            // With a filter of columns
            if (is_array($this->get_csv_headers())) {
                if ($this->filter_heading($title)) {
                    $data[1][] = $title;
                }
            } else {
                $data[1][] = $title;
            }
        }

        $lines = null;
        $index = 1;
        while (($fileline = fgetcsv($handle, self::CSV_LENGTH, self::CSV_DELIMITER)) !== FALSE) {
            if (
            // With onset and end
                    (($this->_start != 0 && $this->_end != 0) && $index >= $this->_start && $index <= $this->_end) ||
                    // With onset and no end
                    (($this->_start != 0 && $this->_end == 0) && $index <= $this->_start) ||
                    // Without start and without an end
                    ( $this->_start == 0 && $this->_end == 0)
            ) {
                if (!isset($data[1])) {
                    continue;
                }

                foreach ($data[1] as $k => $v) {
                    $n = array_search($data[1][$k], $data[0]);
                    $v = trim($fileline[$n]);
                    $v = iconv('ISO-8859-1', 'UTF-8', $v);
                    $v = str_replace('""', '"', $v);
                    $v = preg_replace("~[\s]+~", " ", $v);
                    $v = preg_replace("~^\"(.*)\"$~sim", "$1", $v);

                    $lines[$data[1][$k]] = empty($v) ? null : $v;
                }
                $this->cvs_rows($lines);
            }
            $index++;
        }
        fclose($handle);

        $dataRows = $this->get_cvs_rows();
        $dataHeaders = $this->get_csv_headers();
        $dataColumns = $this->get_dba_columns();
        $totalRows = count($dataRows);
        $totalHeaders = count($dataHeaders);

        $html = '<table class="table table-striped table-bordered table-condensed table-hover">';
        $html.= '<thead>';
        $html.= '<tr>';
        $html.= '<th>&nbsp;</th>';
        foreach ($dataHeaders as $header) {
            $html.= '<th>';
            foreach ($header as $title) {
                $html.= $title . ' ';
            }
            $html.= '</th>';
        }
        $html.= '</tr>';
        $html.= '</thead>';

        $html.= '<tbody>';
        for ($i = 1; $i <= $totalRows; $i++) {
            $html.= '<tr>';
            $html.= '<th>' . ($i) . '</th>';
            for ($j = 1; $j <= $totalHeaders; $j++) {
                $values = null;
                foreach ($dataHeaders[$j - 1] as $k => $v) {
                    $values.= ($k > 0) ? $this->_separator[$j - 1] : null;
                    $values.= isset($dataRows[$i - 1][$v]) ? $dataRows[$i - 1][$v] : null;
                    $values = trim($values);
                }
                $html.= '<td>' . $values . '</td>';

                $this->_data[$dataColumns[$j - 1]] = $values;
            }
            $html.= '</tr>';

            $this->_dataset[] = $this->_data;
        }
        $html.= '</tbody>';
        $html.= '</table>';

        return $html;
    }

    /**
     * Sets the start and end of data capture
     * 
     * @param int $start
     * @param int $end
     */
    public function csv_range($start = 0, $end = 0)
    {
        $this->_start = (int) $start;
        $this->_end = (int) $end;
    }

    /**
     * Stores spreadsheet rows
     * 
     * @param array $rows
     */
    private function cvs_rows($rows = null)
    {
        $this->_rows[] = $rows;
    }

    /**
     * Returns spreadsheet rows
     * 
     * @return array
     */
    private function get_cvs_rows()
    {
        return $this->_rows;
    }

    /**
     * Sets the headers of the spreadsheet to be stored
     * 
     * @param string $header
     * @param string $separator
     * @return \CSVParser
     */
    public function csv_headers($header = null, $separator = null)
    {
        $header = mb_strtoupper($header, "UTF-8");
        $header = preg_replace('~[^[:alnum:],\s]~', '', $header);
        $header = preg_split("~[,\s]+~", $header);

        $this->_header[] = $header;
        $this->_separator[] = !is_null($separator) ? $separator : null;

        return $this;
    }

    /**
     * Returns the header of the spreadsheet
     * 
     * @return array
     */
    private function get_csv_headers()
    {
        return $this->_header;
    }

    /**
     * Sets a column in the database with a the preset value
     * 
     * @param string $column
     * @param string $value
     * @return \CSVParser
     */
    public function dba_fusion($column = null, $value = null)
    {
        $this->_data[$column] = $value;

        return $this;
    }

    /**
     * Sets a column in the database that will store the data from a spreadsheet column
     * 
     * @param string $column
     * @return \CSVParser
     */
    public function dba_column($column = null)
    {
        $this->_columns[] = $column;

        return $this;
    }

    /**
     * Returns the columns defined in the database to receive the spreadsheet values
     * 
     * @return array
     */
    private function get_dba_columns()
    {
        return $this->_columns;
    }

    /**
     * Returns a dataset to save in the database
     * 
     * @return array
     */
    public function get_dataset()
    {
        return $this->_dataset;
    }

    /**
     * Filter containing the user-defined titles
     * 
     * @param string $title
     * @return boolean
     */
    private function filter_heading($title = null)
    {
        return $this->in_multiarray($title, $this->_header);
    }

    /**
     * Checks if a value exists in an multidimensional array
     * 
     * @param string $needle
     * @param array $haystack
     * @return boolean
     */
    private function in_multiarray($needle, $haystack)
    {
        $top = sizeof($haystack) - 1;
        $bottom = 0;

        while ($bottom <= $top) {
            if ($haystack[$bottom] == $needle) {
                return true;
            } else
            if (is_array($haystack[$bottom])) {
                if ($this->in_multiarray($needle, ($haystack[$bottom]))) {
                    return true;
                }
            }
            $bottom++;
        }

        return false;
    }

    /**
     * Magic method
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
    }

}