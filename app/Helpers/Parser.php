<?php

namespace App\Helpers;
class Parser
{
    public function queryparser($parser)
    {
        $arr = array();
        for ($i = 0; $i < count($parser); $i++) {
            if ($parser[$i]['Operation'] == "EQ") {
                $arr [] = [
                    $parser[$i]['PropertyName'], "=", $parser[$i]['PropertyValue']
                ];
            } else if ($parser[$i]['Operation'] == "CT") {
                $arr [] = [
                    $parser[$i]['PropertyName'], "like", '%' . $parser[$i]['PropertyValue'] . '%'
                ];
            } else if ($parser[$i]['Operation'] == "NE") {
                $arr [] = [
                    $parser[$i]['PropertyName'], "!=", $parser[$i]['PropertyValue']
                ];
            } else if ($parser[$i]['Operation'] == "GT") {
                $arr [] = [
                    $parser[$i]['PropertyName'], ">=", $parser[$i]['PropertyValue']
                ];
            }

        }
        return $arr;
    }

    public function PdfTypeControl($type)
    {
        $valid = false;
        $ext = array("pdf");
        if (!in_array($type, $ext)) {
            $valid = false;
        } else {
            $valid = true;
        }
        return $valid;
    }

    public function TextTypeControl($type)
    {
        $valid = false;
        $ext = array("txt", "doc", "docx");
        if (!in_array($type, $ext)) {
            $valid = false;
        } else {
            $valid = true;
        }
        return $valid;
    }

    public function ExcelTypeControl($type)
    {
        $valid = false;
        $ext = array("xlsx", "csv", "xls");
        if (!in_array($type, $ext)) {
            $valid = false;
        } else {
            $valid = true;
        }
        return $valid;
    }

    public function ImageTypeControl($type)
    {
        $valid = false;
        $ext = array("png", "jpg", "jpeg");
        if (!in_array($type, $ext)) {
            $valid = false;
        } else {
            $valid = true;
        }
        return $valid;
    }
}
