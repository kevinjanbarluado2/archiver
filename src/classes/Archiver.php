
<?php
/*
* @Author: Kevin Jan Barluado 
* @Date: 2022-03-03 22:53:50 
* @Github: https://github.com/kevinjanbarluado2 
 */

class Archiver
{
    private $_files = array(), $_zip;
    public int $passwordLength = 8;
    public string $dir;
    public bool $secured = true;

    public function __construct(string $path)
    {
        if ($path == null) {
            echo json_encode(array("status" => "failed", "message" => 'Undefined path'));
            http_response_code(404);
            die();
        }
        $this->dir = str_replace('/', '\\', $path);
        if (!realpath($this->dir) and !is_dir(realpath($this->dir))) {
            echo json_encode(array("status" => "failed", "message" => 'Directory not found'));
            http_response_code(404);
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

    public function store($savePath = null, $zipFile)
    {
        $zipFile = (is_null($zipFile)) ? "Archived.zip" : $zipFile;
        try {
            if (count($this->_files) && $savePath . $zipFile) {
                foreach ($this->_files as $index => $file) {

                    if (!file_exists($file)) {
                        unset($this->_files[$index]);
                    }
                }
            }

            if (!realpath($savePath) and !is_dir(realpath($savePath))) {
                echo json_encode(array("status" => "failed", "message" => 'Directory to save file not found'));
                http_response_code(404);
                die();
            }

            if ($this->_zip->open($savePath . $zipFile, file_exists($savePath . $zipFile) ? ZipArchive::OVERWRITE : ZipArchive::CREATE)) {
                $pass = $this->passwordGenerate($this->passwordLength);

                //fetch password to be called in zipEmail

                $this->zipPass = $pass;
                foreach ($this->_files as $index => $file) {
                    $newFile = explode("\\", $file);
                    $file = end($newFile);
                    $x = explode("/", $file);
                    $fileName = end($x);
                    $this->_zip->addfile($this->dir . $file,  $fileName);
                    //secured=true by default
                    if ($this->secured) {
                        $this->_zip->setEncryptionName($file, ZipArchive::EM_AES_256, $pass);
                    }
                }

                $this->_zip->close();
                echo json_encode(array("status" => "success", "key" => $pass, "link" => [$savePath . $zipFile, $savePath, $zipFile]));
                http_response_code(201);
            } else {
                echo json_encode(array("status" => "failed"));
                http_response_code(404);
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "failed", "message" => $e->getMessage()));
            http_response_code(404);
        }
    }

    public function passwordGenerate(int $chars = 8): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }
}
