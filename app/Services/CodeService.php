<?php

namespace App\Services;

class CodeService
{
    public static function generate($model, $prefix, $field)
    {
        $data = $model::select($field)->orderBy('id', 'DESC')->first();

        $prefixLength = strlen($prefix);
        $lastPart = $data ? intval(substr($data->$field, $prefixLength)) : 0;

        $number = $prefix;
        $number .= substr("00000", 0, -strlen($lastPart + 1));
        $number .= $lastPart + 1;
        return $number;
    }

}