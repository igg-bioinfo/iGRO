<?php

class File_exts {

    const TXT = 'txt';
    const DOC = 'doc';
    const DOCX = 'docx';
    const XLS = 'xls';
    const XLSX = 'xlsx';
    const CSV = 'csv';
    const PPT = 'ppt';
    const PPTX = 'pptx';
    const PNG = 'png';
    const JPG = 'jpg';
    const JPEG = 'jpeg';
    //const TIFF = 'tiff';
    //const TIF = 'tif';
    const BMP = 'bmp';
    const GIF = 'gif';
    const PDF = 'pdf';
    const ZIP = 'zip';
    const RAR = 'rar';
    const Z7 = '7z';
    const CUM = 'cum';
    const GROUP_TXT = [self::TXT, self::DOC, self::DOCX];
    const GROUP_OFFICE = [self::DOC, self::DOCX, self::XLS, self::XLSX, self::CSV, self::PPT, self::PPTX];
    const GROUP_IMAGE = [self::PNG, self::JPG, self::JPEG, self::BMP, self::GIF]; //, self::TIFF, self::TIF,
    const GROUP_ARCHIVE = [self::ZIP, self::RAR];
    const GROUP_OTHER = [self::PDF];
    const GROUP_ALL = [self::TXT, self::DOC, self::DOCX, self::XLS, self::XLSX, self::CSV, self::PPT, self::PPTX
        , self::PNG, self::JPG, self::JPEG, self::BMP, self::GIF
        , self::PDF, self::ZIP, self::RAR];

    public static function get_all_except($start_array, $excluded_array = []) {
        if (count($start_array) == 0) {
            $start_array = self::GROUP_ALL;
        }
        $exts = [];
        foreach ($start_array as $ext) {
            if (!in_array($ext, $excluded_array)) {
                $exts[] = $ext;
            }
        }
        return $exts;
    }

    public static function get_content_type($extension) {
        $content_type = '';
        switch ($extension) {
            case File_exts::TXT:
                $content_type = 'text/plain';
                break;
            case File_exts::CSV:
                $content_type = 'text/csv';
                break;
            case File_exts::DOC:
                $content_type = 'application/msword';
                break;
            case File_exts::DOCX:
                $content_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case File_exts::XLS:
                $content_type = 'application/vnd.ms-excel';
                break;
            case File_exts::XLSX:
                $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case File_exts::PPT:
                $content_type = 'application/vnd.ms-powerpoint';
                break;
            case File_exts::PPTX:
                $content_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                break;
            case File_exts::GIF:
                $content_type = 'image/gif';
                break;
//            case File_exts::TIFF:
//            case File_exts::TIF:
//                $content_type = 'image/tiff';
                break;
            case File_exts::BMP:
                $content_type = 'image/bmp';
                break;
            case File_exts::PNG:
                $content_type = 'image/png';
                break;
            case File_exts::JPG:
            case File_exts::JPEG:
                $content_type = 'image/jpeg';
                break;
            case File_exts::PDF:
                $content_type = 'application/pdf';
                break;
            case File_exts::ZIP:
                $content_type = 'application/zip';
                break;
            case File_exts::RAR:
                $content_type = 'application/x-rar-compressed';
                break;
            case File_exts::Z7:
                $content_type = 'application/x-7z-compressed';
                break;
        }
        return $content_type;
    }

}
