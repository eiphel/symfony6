<?php
namespace App\Utils\File;

/**
 *
 * @author benoit
 *        
 */

class PathFile
{   
    /**
     * @var string
     */
    protected $pathFile;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $separator;

    /**
     * @var array
     */
    protected $pathInfo = array();

    /**
     * @var array
    */
    protected $parts = array();

    /**
     * Constructeur public
     *
     * @return PathFile
    */
    public function __construct(?string $urlOrPathFile = NULL)
    {        
        if (is_string($urlOrPathFile)) {
            $this->setUrlOrPathFile($urlOrPathFile);
        }
    }

    public function setUrlOrPathFile(string $urlOrPathFile)
    {
        $url = parse_url($urlOrPathFile);
        $path = $urlOrPathFile;

        if (isset($url['host'])) {
            $path = isset($url['path']) ? $url['path'] : NULL;
            $this->url = $this->normalizeUrl($urlOrPathFile);
        }

        $isWindows = strcasecmp(substr(PHP_OS, 0, 3), 'win') == 0 ?: false;  
        $this->separator = NULL !== $this->url ?  '/' : ($isWindows ? '\\' : '/');
        $this->pathFile  = $this->normalizePath($path);
        $this->parts     = explode($this->separator, ltrim($this->pathFile, $this->separator));
        $this->pathInfo  = pathinfo($this->pathFile);   

        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator() :?string
    {
        return $this->separator;
    }

    /**
     * @return string
     */
    public function getPathFile() :?string
    {
        return $this->pathFile;
    }

    /**
     * @return string
     */
    public function getUrl() :?string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getParts() : array
    {
        return $this->parts;
    }

    /**
     * @param $pos int
     * @return string|null
     */
    public function getSlug($pos)
    {
        if($pos < 0) {
            $nb = count($this->parts);
            $pos = $nb + $pos;
            if(isset($this->parts[$pos]))
                return $this->parts[$pos];
        }

        if(isset($this->parts[$pos]))
            return $this->parts[$pos];
    }

    /**
     * @param  $slug
     * @return string|null
     */
    public function getNext($slug)
    {
        if(($k = array_search($slug, $this->parts)) !== FALSE)
        {
            $k = $k +1;

            if(isset($this->parts[$k]))
                return $this->parts[$k];
        }
    }

    /**
     * @return string
     */    
    public function end()
    {
        return end($this->parts);
    }

    /**
     * @return string
     */    
    public function current()
    {
        return current($this->parts);
    }

    /**
     * @return string
     */    
    public function getSlice($a, $b = null)
    {
        // Si a est une chaîne récupère la position de la variable
        if(is_string($a))
        {
            if( ($k = array_search($a, $this->parts)) !== FALSE )
                $a = $k+1;
            else
                $a = 0;
        }

        // Si b est une chaîne récupère la position de la variable
        if(is_string($b))
        {
            if( ($k = array_search($b, $this->parts)) !== FALSE )
                $b = $k - $a;
            else
                $b = count($this->parts) - $a;
        }

        return implode($this->separator, array_slice($this->parts, $a, $b) );
    }

    /**
     * @return array
     */    
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * @return string
     */    
    public function getExtension()
    {
        return pathinfo($this->pathFile, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return pathinfo($this->pathFile, PATHINFO_FILENAME);
    }

    /**
     * @return string
     */    
    public function getBaseName()
    {
        return pathinfo($this->pathFile, PATHINFO_BASENAME);
    }

    /**
     * @return string
     */
    public function getDirName()
    {
        return pathinfo($this->pathFile, PATHINFO_DIRNAME);
    }

    /**
     * @param string $url
     * @return string
     */
    public function normalizeUrl(string $url) : string
    {
        $url_ = parse_url($url);

        $string = '';
        if (isset($url_['path'])) {
            $string.= $url_['path'];
        }

        if (isset($url_['query'])) {
            $string.= '?' .$url_['query'];
        }

        if (isset($url_['fragment'])) {
            $string.= '#' .$url_['fragment'];
        }

        return substr($url, 0, (-1 * strlen($string)));
    }

    /**
     * @param string $pathFile
     * @return string
     */
    protected function normalizePath(string $pathFile) : string
    {
        if ($this->separator == '/') {
            $pathFile = str_replace('\\', '/', $pathFile);
        } else {
            $pathFile = str_replace('/', '\\', $pathFile);
        }
        //return preg_replace('/^'.  preg_quote($this->separator, '/') .'|'.  preg_quote($this->separator, '/') .'$/i', '', $pathFile);
        return preg_replace('/(.+)('.  preg_quote($this->separator, '/') .')$/i', '$1', $pathFile);
    }
}