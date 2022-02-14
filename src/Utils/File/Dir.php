<?php
namespace App\Utils\File;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 *
 * @author benoit
 *
 */
class Dir
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var int
     */
    protected $chmod = 0755;

    /**
     * @var string
     */
    protected $separator;

    /**
     * @var boolean
     */
    protected $isWindows;

    /**
     * Dir constructor.
     * @param null $dir
     */
    public function __construct($dir = NULL)
    {
        $this->isWindows = strcasecmp(substr(PHP_OS, 0, 3), 'win') == 0 ?: false;
        $this->separator = $this->isWindows ? '\\' : '/';
        $this->setDir($dir);
    }

    /**
     * @param array $options
     * @return Dir
     */
    public function setOptions(Array $options)
    {
        foreach($options as $key => $value)
        {
            $method = 'set' . ucfirst($key);
            if(method_exists($this, $method))
            {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Détermine le chmod
     *
     * @param  int
     * @return Dir
     */
    public function setChMod($chmod)
    {
        $this->chmod = $chmod;

        return $this;
    }

    /**
     * @return number
     */
    public function getChMod()
    {
        return $this->chmod;
    }

    /**
     * @param string $dir
     * @return Dir
     */
    public function setDir($dir)
    {
        // normalise le chemin selon l'os
        $this->dir = $this->isWindows ? str_replace('/', '\\', $dir) : str_replace('\\', '/', $dir);
        return $this;
    }

    /**
     * Crée un répertoire
     *
     * @return true|false|void
     */
    public function mkDir()
    {
        if(!is_dir($this->dir)) return mkdir($this->dir, $this->chmod);
    }

    /**
     * Crée répertoires/sous-répertoires
     *
     * @return boolean true|false
     */
    public function mkDirs()
    {
        $dirs = explode($this->separator, $this->dir);
        $path = '';

        for ($i = 0; $i < count($dirs); $i++)
        {
            if(empty($dirs[$i])) continue;

            $path .= ($i == 0 && $this->isWindows) ? $dirs[$i] : $this->separator . $dirs[$i];

            if(!is_dir($path) && !is_file($path)) {
                mkdir($path, $this->chmod);
            }
        }

        return is_dir($path);
    }

    /**
     * Applique un chmod
     *
     * @return boolean true|false
     */
    public function chMod()
    {
        return chmod($this->dir, $this->chmod);
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return basename($this->dir);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return explode($this->separator, $this->dir);
    }

    /**
     * Supprime le contenu d'un répertoire
     * 
     * @throws \Exception
     */
    public function clear()
    {
        $this->delete($this->dir);
    }

    /**
     * Supprime un répertoire et son contenu
     * 
     * @throws \Exception
     */
    public function remove()
    {
        $this->delete($this->dir);
        rmdir($this->dir);
    }

    /**
     * @param $dir
     * @throws \Exception
     */
    protected static function delete($dir)
    {
        if (!is_dir($dir)) {
            throw new \Exception('Invalid directory');
        }

        $it    = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }
}