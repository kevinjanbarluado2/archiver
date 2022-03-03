<?php

class Archiver
{
    private $_files = array(), $_zip;
    public string $zipPass;
    public int $passwordLength = 8;
    public string $dir;

    public function __construct($path)
    {
        $this->dir = str_replace('/', '\\', $path);
        if (!realpath($this->dir) and !is_dir(realpath($this->dir))) {
            echo json_encode(array("status" => "failed", "message" => 'Invalid directory'));
            die();
        }
        $this->_zip = new ZipArchive;
    }

    public function add($input)
    {

        if (is_array($input)) {
            $inputArray = array_map(fn ($name) => $this->dir . $name, $input);
            $this->_files = array_merge($this->_files, $inputArray);
        } else {
            $this->_files[] =  $this->dir . $input;
        }
    }

    public function store($location = null)
    {
        
        try {
            if (count($this->_files) && $location) {
                foreach ($this->_files as $index => $file) {

                    if (!file_exists($file)) {
                        unset($this->_files[$index]);
                    }
                }
            }

            if ($this->_zip->open($location, file_exists($location) ? ZipArchive::OVERWRITE : ZipArchive::CREATE)) {
                $pass = $this->passwordGenerate($this->passwordLength);
                //fetch password to be called in zipEmail
                $this->zipPass = $pass;
                foreach ($this->_files as $index => $file) {
                    $newFile = explode("\\", $file);
                    $file = end($newFile);

                    $x = explode("/", $file);
                    $fileName = end($x);

                    $this->_zip->addfile($this->dir . $file,  $fileName);
                    $this->_zip->setEncryptionName($file, ZipArchive::EM_AES_256, $pass);
                }

                $this->_zip->close();
                echo json_encode(array("status" => "success", "key" => $pass));
            } else {
                echo json_encode(array("status" => "failed"));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
        }
    }

    public function passwordGenerate($chars = 8): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }
}
