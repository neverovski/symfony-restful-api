<?php

namespace App\Controller\Pagination;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    private const KEY_LIMIT = 'limit';
    private const KEY_PAGE = 'page';
    private const DEFAULT_LIMIT = 10;
    private const DEFAULT_PAGE = 1;
    /**
     * @var ManagerRegistry
     */
    private $doctrineRegistry;

    /**
     * Pagination constructor.
     * @param ManagerRegistry $doctrineRegistry
     */
    public function __construct(ManagerRegistry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * @param Request $request
     * @param string $entityName
     * @param array $criteria
     * @param string $countMethod
     * @param array $countMethodParameters
     * @param string $route
     * @param array $routeParameters
     * @return PaginatedRepresentation
     */
    public function paginate(
        Request $request,
        string $entityName,
        array $criteria,
        string $countMethod,
        array $countMethodParameters,
        string $route,
        array $routeParameters
    ): PaginatedRepresentation
    {
        $limit = $request->get(self::KEY_LIMIT, self::DEFAULT_LIMIT);
        $page = $request->get(self::KEY_PAGE, self::DEFAULT_PAGE);
        $offset = ($page - 1) * $limit;

        $repository = $this->doctrineRegistry->getRepository($entityName);
        $resource = $repository->findBy($criteria, null, $limit, $offset);
        if (!method_exists($repository, $countMethod)) {
            throw new \InvalidArgumentException("Entity repository method $countMethod does not exist");
        }

        $resourceCount = $repository->{$countMethod}(...$countMethodParameters);
        $pageCount = (int)ceil($resourceCount / $limit);
        $collection = new CollectionRepresentation($resource);

        return new PaginatedRepresentation(
            $collection,
            $route,
            $routeParameters,
            $page,
            $limit,
            $pageCount
        );
    }

}