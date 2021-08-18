<?php

class File {

    public $id = 0;
    public $name = '';
    public $extension = '';
    public $filename = '';
    public $fullname = '';
    public $fullurl = '';
    public $size_bytes = 0;
    private $fullname_post = 'docs';
    public static $is_encrypted = false;
    public static $encrypted_key = '';
    public static $encrypted_type = 0;
    public static $encrypted_min_link_life = 0;
    public static $error = '';
    public static $path_physical = '';
    public static $path_url = '';
    public static $size_bytes_max = 5;
    public static $extensions_allowed = [];
    private static $base_folder = "docs";

    const ENCRYPTED_TYPE_TICKET = 1;
    const ENCRYPTED_TYPE_INFORMED_CONSENT = 2;
    const ENCRYPTED_TYPE_PROJECT_DOC = 3;
    const ENCRYPTED_SUFFIX = '_enc';
    const ENCRYPTED_URL_ID = 'efid';
    const ENCRYPTED_URL_TYPE = 'eftid';
    const ENCRYPTED_URL_DATE = 'efdt';
    const SEP_PHYSICAL = "\\";
    const SEP_URL = "/";
    const ONE_KB = 1024;
    const ONE_MB = 1048576;
    const ERROR_POST_NAME = 60;
    const ERROR_WRITE_FOLDER = 61;
    const ERROR_EXTENSION = 62;
    const ERROR_OVERWRITE = 63;
    const ERROR_SIZE = 64;
    const ERROR_UPLOAD = 65;
    const ERROR_ENCRYPTION = 66;
    const ERROR_ENCRYPTION_SAVE = 67;
    const ERROR_DELETE = 68;
    const ERROR_DECRYPTION = 69;

    //-----------------------------------------------------CONSTRUCT
    function __construct($filename = '', $path_from_base = '', $is_encrypted = NULL) {
        if (isset($is_encrypted)) {
            self::set_is_encrypted($is_encrypted);
        }
        self::$error = '';
        if ($path_from_base != '') {
            self::set_paths($path_from_base);
        }
        if ($filename != '') {
            $this->fullname = self::$path_physical . $filename;
            if ($this->exists()) {
                $this->set_all();
            }
        }
    }

    function exists() {
        if ($this->fullname != '' && file_exists($this->fullname)) {
            return true;
        }
        return false;
    }

    private function set_all($fullname = '') {
        if ($fullname != '') {
            $this->fullname = $fullname;
        }
        $this->name = pathinfo($this->fullname, PATHINFO_FILENAME);
        $this->set_extension($this->fullname);
        $this->set_size_bytes($this->fullname);
        $this->set_fullname();
    }

    public function delete() {
        if (self::$is_encrypted) {
            $sql = "DELETE FROM file_encrypted WHERE id_file = ? AND file_encrypted_type = ?; ";
            $params = [$this->id, File::$encrypted_type];
            Database::edit($sql, $params);
        }
        return $this->fullname != '' && unlink($this->fullname);
    }

    //-----------------------------------------------------PATHS: PHYSICAL & URL
    static function set_paths($path_from_docs) {
        self::$path_physical = GLOBALS::$PHYSICAL_PATH . GLOBALS::$PATH_RELATIVE . self::$base_folder . self::SEP_PHYSICAL . self::check_path($path_from_docs, true);
        try {
            if (!file_exists(self::$path_physical)) {
                mkdir(self::$path_physical, 0777, true);
            }
        } catch (Exception $e) {
            self::set_error(self::ERROR_WRITE_FOLDER);
        }
        self::$path_url = GLOBALS::$DOMAIN_URL . GLOBALS::$URL_RELATIVE . self::$base_folder . self::SEP_URL . self::check_path($path_from_docs, false);
    }

    private static function check_path($path, $is_physical) {
        $sep = $is_physical ? self::SEP_PHYSICAL : self::SEP_URL;
        if ($path != '') {
            $path = str_replace($is_physical ? self::SEP_URL : self::SEP_PHYSICAL, $sep, $path);
            if (Strings::startsWith($path, $sep)) {
                $path = substr($path, strlen($sep));
            }
            if (!Strings::endsWith($path, $sep)) {
                $path .= $sep;
            }
        }
        return $path;
    }

    private function set_fullname() {
        $this->filename = $this->name . '.' . $this->extension;
        $this->fullname = self::$path_physical . $this->filename;
        $this->fullurl = self::$path_url . $this->filename;
    }

    //-----------------------------------------------------ENCRYPTION
    static function set_is_encrypted($is_encrypted) {
        self::$is_encrypted = $is_encrypted; // && extension_loaded('mcrypt');
    }

    public function get_full_encrypted_url() {
        global $oProjectCommon;
        URL::changeable_var_add(self::ENCRYPTED_URL_ID, $this->id);
        URL::changeable_var_add(self::ENCRYPTED_URL_TYPE, File::$encrypted_type);
        URL::changeable_var_add(self::ENCRYPTED_URL_DATE, Date::object_to_screen(new DateTime(), true));
        return URL::create_url('file');
    }

    public function encrypt($fullname) {
        if (self::$is_encrypted) {
            $contents = Encryption::encrypt_file($fullname, self::$encrypted_key);
            if (!$contents) {
                self::set_error(self::ERROR_ENCRYPTION);
                return false;
            }
            if (!file_put_contents($this->fullname, $contents)) {
                self::set_error(self::ERROR_ENCRYPTION);
                return false;
            }
            if (!$this->save_encrypted_file()) {
                self::set_error(self::ERROR_ENCRYPTION_SAVE);
                return false;
            }
            return true;
        }
        return false;
    }

    public function decrypt() {
        $contents = false;
        if (self::$is_encrypted) {
            $contents = Encryption::decrypt_file($this->fullname, File::$encrypted_key);
        }
        return $contents;
    }

    private function save_encrypted_file() {
        $sql = "INSERT INTO file_encrypted (id_file, file_name, file_encrypted_type) VALUES (?, ?, ?); ";
        $params = [$this->id, $this->filename, File::$encrypted_type];
        Database::edit($sql, $params);
        return true;
    }

    //-----------------------------------------------------SIZE
    function get_size_string() {
        $size_string = '';
        if ($this->size_bytes <= self::ONE_KB) {
            $size_string = $this->size_bytes . ' bytes';
        } else if ($this->size_bytes <= self::ONE_MB) {
            $size_string = self::get_size_kb($this->size_bytes) . ' kb';
        } else if ($this->size_bytes <= self::ONE_MB * 1000) {
            $size_string = self::get_size_MB($this->size_bytes) . ' MB';
        } else {
            $size_string = self::get_size_GB($this->size_bytes) . ' GB';
        }
        return $size_string;
    }

    static function get_size($size_bytes) {
        $size_string = '';
        if ($size_bytes <= self::ONE_KB) {
            $size_string = $size_bytes . ' bytes';
        } else if ($size_bytes < self::ONE_MB) {
            $size_string = self::get_size_kb($size_bytes) . ' kb';
        } else if ($size_bytes < self::ONE_MB * 1000) {
            $size_string = self::get_size_MB($size_bytes) . ' MB';
        } else {
            $size_string = self::get_size_GB($size_bytes) . ' GB';
        }
        return $size_string;
    }

    static function get_size_kb($size_byte) {
        return round($size_byte / self::ONE_KB, 2);
    }

    static function get_size_MB($size_byte) {
        return round($size_byte / self::ONE_MB, 2);
    }

    static function get_size_GB($size_byte) {
        return round($size_byte / (self::ONE_MB * 1000), 2);
    }

    static function get_size_bytes_by_MB($size_MB) {
        return $size_MB * self::ONE_MB;
    }

    function check_size() {
        return $this->size_bytes <= self::$size_bytes_max;
    }

    private function set_size_bytes($fullname = '') {
        if ($fullname != '') {
            $this->fullname = $fullname;
        }
        $this->size_bytes = filesize($this->fullname);
    }

    //-----------------------------------------------------EXTENSION
    function check_extension() {
        return count(self::$extensions_allowed) == 0 || in_array($this->extension, self::$extensions_allowed);
    }

    private function set_extension($fullname = '') {
        if ($fullname != '') {
            $this->fullname = $fullname;
        }
        $this->extension = strtolower(pathinfo($this->fullname, PATHINFO_EXTENSION));
    }

    //-----------------------------------------------------UPLOAD
    function prepare_upload($input_name, $new_name_without_extension = '', $is_overwritable = false) {
        self::$error = '';
        if (!isset($_FILES[$input_name])) {
            return false;
        }
        $post_file = $_FILES[$input_name];
        if (!isset($post_file) || $post_file["error"] === UPLOAD_ERR_NO_FILE) {
            return false;
        }
        if ($post_file["error"] !== UPLOAD_ERR_OK) {
            self::set_error($post_file["error"]);
            return false;
        }
        $this->fullname_post = $post_file["tmp_name"];
        $this->set_extension($post_file["name"]);
        $this->set_size_bytes($post_file["tmp_name"]);
        $this->name = ($new_name_without_extension == '' ? pathinfo(basename($post_file["name"]), PATHINFO_FILENAME) : $new_name_without_extension);
        $this->set_fullname();
        if (!$this->check_extension()) {
            self::set_error(self::ERROR_EXTENSION);
            return false;
        }
        if (!$is_overwritable && $this->exists()) {
            self::set_error(self::ERROR_OVERWRITE);
            return false;
        }
        if (!$this->check_size()) {
            self::set_error(self::ERROR_SIZE);
            return false;
        }
        return true;
    }

    function upload() {
        self::$error = '';
        if (self::$is_encrypted) {
            $this->encrypt($this->fullname_post);
        } else if (!move_uploaded_file($this->fullname_post, $this->fullname)) {
            self::set_error(self::ERROR_UPLOAD);
            return false;
        }
        return true;
    }

    function get_temp() {
        return $this->fullname_post;
    }

    //-----------------------------------------------------ERROR MANAGER
    static function has_error() {
        return self::$error != '';
    }

    static function set_error($error_number) {
        self::$error = self::get_error($error_number);
        Error_log::$code = $error_number;
        Error_log::$file = 'class\File.php';
        Error_log::$message = self::$error;
        Error_log::set('FILE', false);
    }

    static function get_error($error_number) {
        $error_text = '';
        switch ($error_number) {
            case UPLOAD_ERR_INI_SIZE: //1
                $error_text = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE: //2
                $error_text = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL: //3
                $error_text = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE: //4
                $error_text = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR: //6
                $error_text = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE: //7
                $error_text = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION: //8
                $error_text = "A PHP extension stopped the file upload";
                break;
            case self::ERROR_POST_NAME:
                $error_text = "Post name uncorrect";
                break;
            case self::ERROR_WRITE_FOLDER:
                $error_text = "Unable to write in the directory";
                break;
            case self::ERROR_EXTENSION:
                $error_text = "Extension not supported. Only: ." . implode(', .', self::$extensions_allowed);
                break;
            case self::ERROR_OVERWRITE:
                $error_text = "File already exists";
                break;
            case self::ERROR_SIZE:
                $error_text = "Your file is too large. File should be less than " . self::get_size_MB(self::$size_bytes_max) . " MB";
                break;
            case self::ERROR_UPLOAD:
                $error_text = "There was an error uploading your file";
                break;
            case self::ERROR_ENCRYPTION:
                $error_text = "There was an error encrypting file";
                break;
            case self::ERROR_ENCRYPTION_SAVE:
                $error_text = "There was an error saving encrypted file";
                break;
            case self::ERROR_DELETE:
                $error_text = "There was an error deleting file";
                break;
            case self::ERROR_DECRYPTION:
                $error_text = "There was an error decrypting file";
                break;

            default:
                $error_text = "Error not managed";
                break;
        }
        return $error_text;
    }

    //-----------------------------------------------------STATIC
    static function remove_dir_recursively($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        if (!self::remove_dir_recursively($dir . "/" . $object)) {
                            return false;
                        }
                    } else {
                        if (!unlink($dir . "/" . $object)) {
                            return false;
                        }
                    }
                }
            }
            reset($objects);
            return rmdir($dir);
        }
        return true;
    }

}
