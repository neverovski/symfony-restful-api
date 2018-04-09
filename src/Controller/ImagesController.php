<?php

namespace App\Controller;

use App\Entity\Image;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ImagesController extends AbstractController
{
    use ControllerTrait;

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
        $manager = $this->getDoctrine()->getManager();

        $manager->persist($image);
        $manager->flush();

        return $this->view($image, Response::HTTP_CREATED)->setHeader(
            'Location',
            $this->generateUrl(
                'Images_upload_put',
                ['images' => $image->getId()]
            )
        );
    }
}