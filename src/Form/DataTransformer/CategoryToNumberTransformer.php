<?php
namespace App\Form\DataTransformer;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, private RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * Transforms an object (category) to a string (number).
     *
     * @param  Category|null $category
     */
    public function transform($category) : string
    {
        if (null === $category) {
            return '';
        }

        if ($category instanceof Category) {
            $category = $category->getId();
        }

        return (string) $category;
    }

    /**
     * Transforms a string (number) to an object (category).
     *
     * @param  string $categoryNumber
     * @throws TransformationFailedException if object (category) is not found.
     */
    public function reverseTransform($categoryNumber): ?Category
    {
        // no category number? It's optional, so that's ok
        if (!$categoryNumber) {
            return null;
        }

        $category = $this->entityManager
            ->getRepository(Category::class)
            // query for the issue with this id
            ->find($categoryNumber)
        ;

        if (null === $category) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $categoryNumber
            ));
        }

        return $category;
    }
}