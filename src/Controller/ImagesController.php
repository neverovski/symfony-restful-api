<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class ImagesController extends AbstractController
{
    use ControllerTrait;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var string
     */
    private $imageDirectory;
    /**
     * @var string
     */
    private $imageBaseUrl;

    /**
     * ImagesController constructor.
     * @param ImageRepository $imageRepository
     * @param string $imageDirectory
     * @param string $imageBaseUrl
     */
    public function __construct(
        ImageRepository $imageRepository,
        string $imageDirectory,
        string $imageBaseUrl
    )
    {
        $this->imageRepository = $imageRepository;
        $this->imageDirectory = $imageDirectory;
        $this->imageBaseUrl = $imageBaseUrl;
    }

    /**
     * @ParamConverter(
     *     "image",
     *     converter="fos_rest.request_body",
     *     options={"deserializationContext"={"groups"={"Deserialize"}}}
     * )
     * @Rest\NoRoute()
     */
    public function postImagesAction(Image $image)
    {
        $this->persistImage($image);

        return $this->view($image, Response::HTTP_CREATED)->setHeader(
            'Location',
            $this->generateUrl(
                'Images_upload_put',
                ['images' => $image->getId()]
            )
        );
    }

    /**
     * @Rest\NoRoute()
     */
    public function putImageUploadAction(?Image $image, Request $request)
    {
        if (null === $image) {
            throw new NotFoundHttpException();
        }

        // Read the image content from request body
        $content = $request->getContent();
        // Create the temporary upload file (deleted after request finishes)
        $tmpFile = tmpfile();
        // Get the temporary file name
        $tmpFilePatch = stream_get_meta_data($tmpFile)['uri'];
        // Write image content to the temporary file
        file_put_contents($tmpFilePatch, $content);

        // Get the file mime-type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpFilePatch);

        // Check if it's really an image (never trust client set mime-type!)
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            throw new UnsupportedMediaTypeHttpException(
                'File uploaded is not a valid png/jpeg/gif image'
            );
        }

        // Guess the extension based on mime-type
        $extensionGuesser = ExtensionGuesser::getInstance();
        // Generate a new random filename
        $newFileName = md5(uniqid()).'.'.$extensionGuesser->guess($mimeType);

        // Copy the temp file to the final uploads directory
        copy($tmpFilePatch, $this->imageDirectory.DIRECTORY_SEPARATOR.$newFileName);

        $image->setUrl($this->imageBaseUrl.$newFileName);

        $this->persistImage($image);

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @param Image|null $image
     */
    public function persistImage(?Image $image): void
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($image);
        $manager->flush();
    }
}