<?php

namespace App\Controller;

use App\Utils\File\UniqFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload')]
class UploadController extends AbstractController
{
    #[Route('/images', name: 'upload_images')]
    public function index(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
           throw new \Exception('Something went wrong !');
        }

        $files = (array) $request->files->get('files');
        
        $data = [];

        foreach($files as $file) {

            if (!in_array(($extension = $file->guessClientExtension()), ['jpg','png','gif'])) {
                continue;
            }
 
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension;

            $UniqFile = new UniqFile;

            $fileName = $UniqFile->uniq($this->getParameter('tmp_directory') . '/' . $fileName);

            $fileName = pathinfo($fileName, PATHINFO_BASENAME);

            $file->move($this->getParameter('tmp_directory'), $fileName);

            $data['files']['name'][] = $fileName;
        }

       return $this->json($data);
    }
}
