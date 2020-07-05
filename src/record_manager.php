<?php
class RecordManager
{

    public function get_record_as_str($record)
    {
        // $record = 4;
        $record_bin = decbin($record);
        $record_bin_str = strval($record_bin);
        $record_bin_str_pad = str_pad($record_bin_str, 4, '0', STR_PAD_LEFT);

        $record_with_cross = str_replace("0", "×", $record_bin_str_pad);
        $record_with_cross_circle = str_replace("1", "〇", $record_with_cross);

        return $record_with_cross_circle;
    }
}
