<?php
namespace App\Utils\Filter;

class Slug implements FilterInterface
{
	/**
	 * var string
	 */		
	protected $characterSet;
	
	/**
	 * var int
	 */		
	protected $maxLength;	
	
	public function __construct(Array $args = null)
	{
		$this->characterSet = isset($args['character_set']) ? $args['character_set'] : 'UTF-8';
		$this->maxLength    = isset($args['max_length'])    ? $args['max_length']    : 220;
	}
	
	/**
	 * Convertit une chaîne en slug 
	 *
	 * @param  string $string
	 * @return string
	 */
    public function filter($string) : string
    {
		$slug = preg_replace("`\[.*\]`U","", $string);
		$slug = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-', $slug);
		$slug = htmlentities($slug, ENT_COMPAT, $this->characterSet);
		$slug = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i","\\1", $slug);
		$slug = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $slug);
		$slug = strtolower(trim($slug));
		$slug = preg_replace('/^-+|-+$/i', '', $slug);
		return substr($slug, 0, $this->maxLength);
	}   
}
