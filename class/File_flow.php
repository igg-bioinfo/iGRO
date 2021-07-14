<?php

class File_flow {

    //--------------------------------VARIABLES
    private $request_method = '';
    private $filename = '';
    private $dir_base = '';
    private $dir_temp = '';
    private $chunk = '';
    private $message = '';
    private $is_error = false;
    public $extensions_allowed = [];
    public $size_bytes_max = 5;
    public $url_base = '';

    const ACTION_UPLOAD = 1;
    const ACTION_DELETE = 2;

    private $flowIdentifier = NULL;
    private $flowFilename = NULL;
    private $flowChunkNumber = NULL;
    private $flowChunkSize = NULL;
    private $flowTotalSize = NULL;

    //--------------------------------CONSTRUCT
    function __construct($folder, $filename = NULL) {
        $this->filename = $filename;
        $this->url_base = GLOBALS::$DOMAIN_URL . GLOBALS::$URL_RELATIVE . 'docs/' . $folder;
        $this->dir_base = GLOBALS::$PHYSICAL_PATH . GLOBALS::$PATH_RELATIVE . 'docs\\' . str_replace('/', '\\', $folder);
    }

    //--------------------------------PRIVATE
    private function set_variable($variable) {
        $temp = Security::sanitize($this->request_method, $variable);
        if ($temp . '' == '') {
            $this->message = "Variable doesn't exist";
            $this->is_error = true;
        } else {
            $this->{$variable} = $temp;
        }
    }

    function create_from_chunks() {
        $total_files = 0;
        foreach (scandir($this->dir_temp) as $file) {
            if (stripos($file, $this->flowFilename) !== false) {
                $total_files++;
            }
        }
        if ($total_files * $this->flowChunkSize >= ($this->flowTotalSize - $this->flowChunkSize + 1)) {
            if (($fp = fopen($this->dir_base . '/' . $this->flowFilename, 'w')) !== false) {
                for ($i = 1; $i <= $total_files; $i++) {
                    fwrite($fp, file_get_contents($this->dir_temp . '/' . $this->flowFilename . '.part' . $i));
                }
                fclose($fp);
            } else {
                $this->message = 'cannot create the destination file';
                $this->is_error = true;
                return false;
            }
            if (rename($this->dir_temp, $this->dir_temp . '_UNUSED')) {
                if (File::remove_dir_recursively($this->dir_temp . '_UNUSED')) {
                    $this->message = $this->flowFilename;
                }
            } else {
                File::remove_dir_recursively($this->dir_temp);
            }
        }
    }

    private function upload() {
        if (empty($_FILES)) {
            $this->message = 'No file to upload was found';
            $this->is_error = true;
            return;
        }
        foreach ($_FILES as $file) {
            if ($file['error'] != 0) {
                $this->message = 'Error in uploading the file';
                $this->is_error = true;
                continue;
            }

            $extension = isset(pathinfo($file['name'])['extension']) ? pathinfo($file['name'])['extension'] : NULL;
            if (!isset($extension) || !in_array($extension, $this->extensions_allowed)) {
                $this->message = 'Extension not allowed';
                $this->is_error = true;
                return;
            }
            if ($this->flowTotalSize > $this->size_bytes_max) {
                $this->message = 'Maximum file size allowed is ' . File::get_size($this->size_bytes_max);
                $this->is_error = true;
                return;
            }

            if (!is_dir($this->dir_temp)) {
                if (!mkdir($this->dir_temp, 0777, true)) {
                    $this->message = 'Error in creating folder';
                    $this->is_error = true;
                    return;
                }
            }

            if (!move_uploaded_file($file['tmp_name'], $this->chunk)) {
                $this->message = 'Error saving chunk';
                $this->is_error = true;
            } else {
                $this->create_from_chunks();
            }
        }
    }

    private function delete() {
        $objects = glob($this->dir_base . '/' . $this->filename . '.*', GLOB_BRACE);
        foreach ($objects as $object) {
            if (!unlink($object)) {
                $this->message = 'File cannot be deleted';
                $this->is_error = true;
            }
            break;
        }
    }

    //--------------------------------PUBLIC
    public function do_action($action) {
        $this->is_error = false;
        $this->request_method = Security::sanitize(INPUT_SERVER, 'REQUEST_METHOD') ? INPUT_POST : INPUT_GET;
        switch ($action) {
            case self::ACTION_DELETE:
                $this->delete();
                break;
            case self::ACTION_UPLOAD:
                $this->set_variable('flowIdentifier');
                $this->set_variable('flowFilename');
                $this->set_variable('flowChunkNumber');
                $this->set_variable('flowChunkSize');
                $this->set_variable('flowTotalSize');
                $this->dir_temp = $this->dir_base . '/' . $this->flowIdentifier;
                $this->chunk = $this->dir_temp . '/' . $this->flowFilename . '.part' . $this->flowChunkNumber;
                $this->upload();
                break;
        }
    }

    public function message_and_exit() {
        if ($this->message . '' == '') {
            exit;
        }
        if ($this->is_error) {
            header("HTTP/1.0 404 Not Found");
        } else {
            header("HTTP/1.0 200 Ok");
        }
        echo $this->message;
        exit;
    }

    public function scan($specific_file = '') {
        if (!is_dir($this->dir_base)) {
            if (!mkdir($this->dir_base, 0777, true)) {
                $this->message = 'Error in creating folder';
                $this->is_error = true;
                return [];
            }
        }
        $objects = scandir($this->dir_base);
        $files = [];
        foreach ($objects as $object) {
            if ($object != "." && $object != ".." && filetype($this->dir_base . "/" . $object) != "dir") {
                $info = pathinfo($this->dir_base . "/" . $object);
                if ($specific_file != '' && $info['filename'] != $specific_file) {
                    continue;
                };
                $files[$info['filename']] = $object;
            }
        }
        return $files;
    }

}
