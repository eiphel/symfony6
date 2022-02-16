<?php

namespace App\Entity\Block;

use App\Repository\Block\ParagraphRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParagraphRepository::class)]
class Link extends Block
{

}