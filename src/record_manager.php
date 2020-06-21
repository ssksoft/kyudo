<?php
class RecordManager
{

    public function get_record_as_str($record)
    {
        // $record = 4;
        $record_bin = decbin($record);
        $record_bin_str = strval($record_bin);
        $record_bin_str_pad = str_pad($record_bin_str, 4, '0', STR_PAD_LEFT);
        $current_record_bin_str = substr($record_bin_str_pad, 0, 1);
        echo ($record_bin);
        echo ("_");
        echo ($record_bin_str);
        echo ("_");
        echo ($current_record_bin_str);
        // echo (strval("0"));
        if ($current_record_bin_str == "0") {
            return "×";
        } else {
            return "〇";
        }
    }
}
