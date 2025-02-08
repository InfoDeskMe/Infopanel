<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use App\Service\FileDeleter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use function finfo_open;
use function finfo_close;

class UploadController extends AbstractController
{
    
    #[Route('/doUpload', name: 'do-upload')]
    public function index(Request $request, string $uploadDir, FileUploader $uploader, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $allowed_file_types = ['image/jpeg', 'image/png', 'video/mp4'];
        $allowed_size_mb = 2; 
        $file = $request->files->get('file');
        $file_size = $_FILES['file']['size'];

        if (empty($file))
        {
            return new Response("No file specified",
               Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $finfo = finfo_open();
        $file_type = finfo_buffer($finfo, file_get_contents($file), FILEINFO_MIME_TYPE);
        if(!in_array($file_type, $allowed_file_types)) {
            exit('Error : Incorrect file type');
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        } else if ($file_size > $allowed_size_mb*1024*1024) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
            exit('Error : Exceeded size');
            
        } else {
            $filename = $file->getClientOriginalName();
            $uploader->upload($uploadDir, $file, $filename);
            $response = new Response();
            return $response;
        }
    }

    #[Route('/doDelete', name: 'do-delete')]
    public function deleteFile(Request $request, string $uploadDir, FileDeleter $deleter, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $filename = $request->getContent();
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images/';
        echo $filename;
        $deleter->delete($uploadDir, $filename);
        $response = new Response();
        return $response;
    }

    #[Route('/showfiles', name: 'showfiles')]
    public function showFiles(Request $request, string $uploadDir): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $finder = new Finder();
        $data = array();
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images/';
        foreach ($finder->files()->in('../public/uploads/images') as $file) {
           $data[] = $file->getRelativePathname();
        }
        $json = json_encode($data);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json, JSON_PRETTY_PRINT ));
        return $response;
    }

}
