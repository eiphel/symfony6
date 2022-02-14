<?php
namespace App\Utils\File;

class UniqFile
{   
    public function uniq(string $pathFile) : string
    {
       if (file_exists($pathFile)) {
            $helper = $this->getPathFileHelper()
                ->setUrlOrPathFile($pathFile);

            $fileName = $helper->getFileName();
            $extension = $helper->getExtension();
  
            $num = $this->getNum($fileName);
            $num = NULL === $num ? 1 : $num + 1;

            $pathFile = $helper->getDirName() . '/'
                        . $this->getFileName($fileName)
                        . '-' . $num
                        . ($extension ? '.' . $extension : '');
            
            return $this->uniq($pathFile);
        }

        return $pathFile;
    }

    public function getFileName(string $fileName) : string
    {
        if (strrchr($fileName, '-')) {
            $string = substr(strrchr($fileName, '-'), 1);
            if (preg_match("/^[0-9]+$/", $string)) {
                return substr($fileName, 0, -1 * strlen(strrchr($fileName, '-')));
            }
        }
        return $fileName;
    }

    public function getNum(string $fileName) : ?int
    {
        if (strrchr($fileName, '-')) {
            $string = substr(strrchr($fileName, '-'), 1);
            if (preg_match("/^[0-9]+$/", $string)) {
                return (int) $string;
            }
        }

        return NULL;
    }

    protected function getPathFileHelper() : PathFile
    {
        return new PathFile();
    }
}