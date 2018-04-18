<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Swagger\Annotations as SWG;

/**
 * Class ImagesController
 * @package App\Controller
 * @Version("v1")
 */
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
     * @Rest\View()
     * @Rest\Get("/images", name="get_images")
     * @SWG\Get(
     *     tags={"Image"},
     *     summary="Gets the all image",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful")
     * )
     */
    public function getImages()
    {
        return $this->imageRepository->findAll();
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/images", name="post_images")
     * @ParamConverter(
     *     "image",
     *     converter="fos_rest.request_body",
     *     options={"deserializationContext"={"groups"={"Deserialize"}}}
     * )
     * @SWG\Post(
     *     tags={"Image"},
     *     summary="Add a new image resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="201", description="Returned when resource created"),
     * )
     *
     * @param Image $image
     * @return \FOS\RestBundle\View\View
     */
    public function postImages(Image $image)
    {
        $this->persistImage($image);

        return $this->view($image, Response::HTTP_CREATED)->setHeader(
            'Location',
            $this->generateUrl(
                'images_upload_put',
                ['image' => $image->getId()]
            )
        );
    }

    /**
     * @Rest\View()
     * @Rest\Put("/images/{image}/upload", name="put_image_upload")
     * @SWG\Put(
     *     tags={"Image"},
     *     summary="Edit the image",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when resource update"),
     *     @SWG\Response(response="400", description="Returned when invalid date posted")
     * )
     *
     * @param Image|null $image
     * @param Request $request
     * @return Response
     */
    public function putImageUpload(?Image $image, Request $request)
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
        $newFileName = md5(uniqid()) . '.' . $extensionGuesser->guess($mimeType);

        // Copy the temp file to the final uploads directory
        copy($tmpFilePatch, $this->imageDirectory . DIRECTORY_SEPARATOR . $newFileName);

        $image->setUrl($this->imageBaseUrl . $newFileName);

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