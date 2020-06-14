<?php
class RecordManager
{

    public function get_record_as_str($record)
    {
        $record_bin = decbin($record);
        $record_str = strval($record_bin);
        return $record_str;
    }
}
