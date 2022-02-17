<?php
namespace App\Doctrine\Type;

class GenderEnumType extends AbstractEnumType
{
    protected $name = 'enum_gender';
    protected $values = array('M', 'F');
}