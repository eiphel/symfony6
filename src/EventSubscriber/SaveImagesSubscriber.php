<?php

namespace App\EventSubscriber;

use App\Utils\File\PathFile;
use App\Utils\File\UniqFile;
use App\Utils\Filter\Slug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class SaveImagesSubscriber implements EventSubscriberInterface
{
    public function __construct(private ParameterBagInterface $parameter, private RequestStack $requestStack)
    {

    }

    public function onFormPostSetData(FormEvent $event)
    {
        $data = $event->getData();
        $images = [];

        foreach($data->getImages() as $image) {
            array_push($images, $image->getName());
        }

        $session = $this->requestStack->getSession();
        $session->set('images', $images);
    }

    public function onFormPostSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
       
        if ($form->isValid()) {

            $Slug = new Slug();
            $Uniq = new UniqFile();

            $tmpDir = $this->parameter->get('tmp_directory');
            $uploadsDir = $this->parameter->get('images_directory');
            
            // move images from tmp to uploads
            foreach($data->getImages() as $image) {

                $name = $image->getName();
                $from = $tmpDir . '/' . $name;

                if ($image->getId()
                    || !$name
                    || !file_exists($from)
                    ) {
                    continue;
                }

                $PathFile = new PathFile($name);
                $name = $PathFile->getFileName();
                $name = $Uniq->getFileName($name);
                $extension = $PathFile->getExtension();
                $to = $uploadsDir . '/' . $Slug->filter($name) . '.' . $extension;
                $to = $Uniq->uniq($to);

                if (rename($from, $to)) {
                    $image->setName((new PathFile($to))->getBaseName());
                }
            }

            // remove images
            $session = $this->requestStack->getSession();
            $images = $session->get('images', []);
            foreach($form->get('removed_images')->getData() as $image) {
                $pathFile = $uploadsDir . '/' . $image;
                if (in_array($image, $images) && file_exists($pathFile)) {
                    unlink($pathFile);
                } else {
                    throw new \Exception('Something went wrong !');
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onFormPostSubmit',
            FormEvents::POST_SET_DATA => 'onFormPostSetData'
        ];
    }
}
