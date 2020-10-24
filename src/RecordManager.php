<?php
class RecordManager
{

    public function get_record_as_str($record)
    {
        $record_bin = decbin($record);
        $record_bin_str = strval($record_bin);
        $record_bin_str_pad = str_pad($record_bin_str, 4, '0', STR_PAD_LEFT);

        $record_with_cross = str_replace("0", "×", $record_bin_str_pad);
        $record_with_cross_circle_invert = str_replace("1", "○", $record_with_cross);

        $record_with_cross_circle = mb_substr($record_with_cross_circle_invert, 3, 1) .
            mb_substr($record_with_cross_circle_invert, 2, 1) .
            mb_substr($record_with_cross_circle_invert, 1, 1) .
            mb_substr($record_with_cross_circle_invert, 0, 1);

        return $record_with_cross_circle;
    }
}
