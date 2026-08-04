<?php

namespace App\Controller;

use App\Security\CsrfProtectedTrait;
use App\Service\FileDeleter;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UploadController extends AbstractController
{
    use CsrfProtectedTrait;

    private const EXTENSION_BY_MIME_TYPE = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'video/mp4' => 'mp4',
    ];

    // Matches only filenames this controller itself generates.
    private const SAFE_FILENAME_PATTERN = '/^[a-f0-9]{32}\.(jpg|png|mp4)$/';

    #[Route('/doUpload', name: 'do-upload', methods: ['POST'])]
    public function index(Request $request, FileUploader $uploader, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $allowed_size_mb = 2;
        $file = $request->files->get('file');

        if (empty($file)) {
            return new Response('No file specified', Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $finfo = \finfo_open();
        $file_type = finfo_buffer($finfo, file_get_contents($file), FILEINFO_MIME_TYPE);
        \finfo_close($finfo);

        if (!isset(self::EXTENSION_BY_MIME_TYPE[$file_type])) {
            return new Response('Error : Incorrect file type', Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        if ($file->getSize() > $allowed_size_mb * 1024 * 1024) {
            return new Response('Error : Exceeded size', Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        // Never trust the client-supplied filename as a filesystem path (path traversal).
        $safeName = bin2hex(random_bytes(16)).'.'.self::EXTENSION_BY_MIME_TYPE[$file_type];
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/images/';
        $uploader->upload($uploadDir, $file, $safeName);

        return new JsonResponse(['filename' => $safeName]);
    }

    #[Route('/doDelete', name: 'do-delete', methods: ['POST'])]
    public function deleteFile(Request $request, FileDeleter $deleter, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->assertValidCsrfToken($request);
        $data = json_decode($request->getContent(), true);
        $filename = is_array($data) ? ($data['filename'] ?? '') : '';

        if (!is_string($filename) || !preg_match(self::SAFE_FILENAME_PATTERN, $filename)) {
            return new Response('Invalid filename', Response::HTTP_BAD_REQUEST);
        }

        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/images/';

        try {
            $deleter->delete($uploadDir, $filename);
        } catch (\InvalidArgumentException $e) {
            return new Response('Invalid path', Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    #[Route('/showfiles', name: 'showfiles', methods: ['GET'])]
    public function showFiles(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $finder = new Finder();
        $data = [];
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/images/';
        foreach ($finder->files()->in($uploadDir) as $file) {
            $data[] = $file->getRelativePathname();
        }
        $json = json_encode($data);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($json, JSON_PRETTY_PRINT));

        return $response;
    }
}
