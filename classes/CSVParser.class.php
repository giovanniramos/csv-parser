<?php

/**
 * CSVParser
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
class CSVParser
{
    const CSV_LENGTH = 4096;
    const CSV_DELIMITER = ';';

    private $_data = null;
    private $_table = null;
    private $_query = null;
    private $_rows = array();
    private $_result = array();
    private $_columns = array();
    private $_separator = array();
    private $_limit = 0;
    private $_offset = 0;
    private $_header;

    public function csv_import($filepath = null)
    {
        if (($handle = @fopen($filepath, "r")) === FALSE)
            exit('<h3>The file "' . $filepath . '" does not exist!!</h1>');

        $data = array();

        $this->csv_range($this->_limit, $this->_offset);

        $headline = fgetcsv($handle, self::CSV_LENGTH, self::CSV_DELIMITER);
        foreach ($headline as $title):
            $data[0][] = $title = strtoupper(preg_replace('/[^[:alnum:]\s]/', '', $title));

            // With a filter of columns
            if (is_array($this->get_csv_headers())):
                if ($this->filterHeading($title)):
                    $data[1][] = $title;
                endif;
            else:
                $data[1][] = $title;
            endif;
        endforeach;


        $rows = null;
        $index = 1;
        while (($fileline = fgetcsv($handle, self::CSV_LENGTH, self::CSV_DELIMITER)) !== FALSE):
            if (
            // With limit and offset
            (($this->_limit != 0 && $this->_offset != 0) && $index >= $this->_limit && $index <= $this->_offset) ||
            // With limit and without offset
            (($this->_limit != 0 && $this->_offset == 0) && $index <= $this->_limit) ||
            // No limits and no offset
            ( $this->_limit == 0 && $this->_offset == 0)
            ) :
                foreach ($data[1] as $k => $v):
                    $n = array_search($data[1][$k], $data[0]);
                    $v = trim($fileline[$n]);
                    $v = iconv('ISO-8859-1', 'UTF-8', $v);
                    $v = mb_strtoupper($v, "UTF-8");
                    $v = str_replace('""', '"', $v);
                    $v = preg_replace("~[\s]+~", " ", $v);
                    $v = preg_replace("~^\"(.*)\"$~sim", "$1", $v);

                    $rows[$data[1][$k]] = $v == '' ? '&nbsp;' : $v;
                endforeach;
                $this->cvs_rows($rows);
            endif;
            $index++;
        endwhile;
        fclose($handle);

        $this->cvs_data($data);

        return $this;
    }

    public function csv_table()
    {
        echo '
		<style type="text/css" media="screen">
		table { background: #000; }
		th { background: #555; color: #FFF; padding:2px 8px;}
		td { background: #FFF; }
		</style>
		';

        $dataRows = $this->get_cvs_rows();
        $dataHeaders = $this->get_csv_headers();
        $dataColumns = $this->get_sql_columns();
        $totalRows = count($dataRows);
        $totalHeaders = count($dataHeaders);


        $html = '<table border="0" cellspacing="1" cellpadding="3" id="csv">';
        $html.= '<tr>';
        $html.= '<th class="heading">&nbsp;</th>';
        foreach ($dataHeaders as $th => $header):
            $html.= '<th>';
            $html.= '<input name="' . $header[0] . '" id="' . $header[0] . '" value="1" type="checkbox" class="check" style="padding:10px !important;" />';
            $html.= '<label for="' . $header[0] . '">';
            foreach ($header as $title):
                $html.= $title . '<br />';
            endforeach;
            $html.= '</label>';
            $html.= '</th>';
        endforeach;
        $html.= '</tr>';


        for ($i = 1; $i <= $totalRows; $i++):
            $html.= '<tr ' . ($i == 1 ? 'class="first"' : null) . '>';
            $html.= '<th class="heading">&nbsp;&nbsp;' . ($i) . '</th>';
            for ($j = 1; $j <= $totalHeaders; $j++):
                $values = null;
                foreach ($dataHeaders[$j - 1] as $k => $v):
                    $values.= ($k > 0) ? $this->_separator[$j - 1] : null;
                    //$values.= $dataRows[$i - 1][@$v];
                $v;
                endforeach;
                $html.= '<td>' . $values . '</td>';

                $this->_result[$dataColumns[$j - 1]] = $values;
            endfor;
            $html.= '</tr>';

            $this->result($this->_result);
        endfor;
        $html.= '</table>';

        $this->_table = $html;
    }

    public function execute()
    {
        $this->csv_table();

        return $this;
    }

    public function get_csv_table()
    {
        return $this->_table;
    }

    public function csv_headers($header = null, $separator = null)
    {
        if (is_null($header))
            return;

        $header = strtoupper($header);
        $header = preg_replace('/[^[:alnum:],\s]/', '', $header);
        $header = preg_split("~[,\s]+~", $header);

        $this->_header[] = $header;
        $this->_separator[] = !is_null($separator) ? $separator : null;

        return $this;
    }

    public function get_csv_headers()
    {
        return $this->_header;
    }

    public function filterHeading($header = null)
    {
        $header = $this->in_multiarray($header, $this->_header);

        return $header;
    }

    public function csv_range($start = 0, $offset = 0)
    {
        $this->_limit = (int) $start;
        $this->_offset = (int) $offset;
    }

    public function cvs_data($data = null)
    {
        $this->_data = $data;
    }

    public function cvs_rows($rows = null)
    {
        $this->_rows[] = $rows;
    }

    public function get_cvs_rows()
    {
        return $this->_rows;
    }

    public function sql_column($column = null)
    {
        $this->_columns[] = $column;

        return $this;
    }

    public function get_sql_columns()
    {
        return $this->_columns;
    }

    public function result($values = null)
    {
        $this->_query[] = $values;
    }

    public function get_result()
    {
        return $this->_query;
    }

    public function sql_fusion($column = null, $header = null)
    {
        if (is_null($column) || is_null($header))
            return;

        $header = mb_strtoupper($header, "UTF-8");
        $this->_result[$column] = $header;
    }

    public function in_multiarray($needle, $haystack)
    {
        $top = sizeof($haystack) - 1;
        $bottom = 0;

        while ($bottom <= $top):
            if ($haystack[$bottom] == $needle)
                return true;
            else
            if (is_array($haystack[$bottom]))
                if ($this->in_multiarray($needle, ($haystack[$bottom])))
                    return true;
            $bottom++;
        endwhile;

        return false;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->_result)):
            return $this->_result[$key];
        endif;
    }

}

?>