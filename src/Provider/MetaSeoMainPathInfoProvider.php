<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Provider;

use Toro\SeoBundle\Model\MetaSeoInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class MetaSeoMainPathInfoProvider implements MetaSeoProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var array
     */
    private $ignoreAttrs;

    /**
     * @var array
     */
    private $queryCache = [];

    public function __construct(RepositoryInterface $repository, array $ignoreAttrs)
    {
        $this->repository = $repository;
        $this->ignoreAttrs = $ignoreAttrs;
    }

    /**
     * @param Request $request
     * @return MetaSeoInterface|null
     */
    public function getMetaFromRequest(Request $request): ?MetaSeoInterface
    {
        if (isset($this->queryCache[$this->getCacheKey($request)])) {
            $meta = $this->queryCache[$this->getCacheKey($request)];
        } else {
            $meta = $this->getMetaByPathInfo($request) ?: $this->getMetaByRouteAndParams($request);
            $this->queryCache[$this->getCacheKey($request)] = $meta;
        }

        return $meta;
    }

    /**
     * @param Request $request
     * @return MetaSeoInterface|null
     */
    private function getMetaByPathInfo(Request $request): ?MetaSeoInterface
    {
        $pathInfo = $request->getPathInfo();
        $localePath = '/' .$request->getLocale() . '/';
        if (0 === strpos($pathInfo, $localePath)) {
            $pathInfo = str_replace($localePath, '/', $pathInfo);
        }

        return $this->repository->findOneByPathInfo($pathInfo);
    }

    /**
     * @param Request $request
     * @return MetaSeoInterface|null
     */
    private function getMetaByRouteAndParams(Request $request): ?MetaSeoInterface
    {
        $meta = null;
        $routeName = $request->attributes->get('_route');
        if (empty($routeName)) {
            return null;
        }

        $parameters = array_filter(
            (array) $request->attributes->get('_route_params'),
            function ($key) {
                return !in_array($key, $this->ignoreAttrs);
            },
            ARRAY_FILTER_USE_KEY
        );

        $metas = $this->repository->findBy([
            'routeName' => $routeName
        ]);

        if (1 === count($metas) && empty($metas[0]->getParameters())) {
            return $metas[0];
        }

        $countMatch = 0;
        /** @var MetaSeoInterface $metaSeo */
        foreach ($metas as $metaSeo) {
            $matchedParams = array_intersect($metaSeo->getParameters(), $parameters);

            if (0 === $count = count($matchedParams)) {
                continue;
            }

            if ($countMatch > $count) {
                continue;
            }

            $countMatch = $count;
            $meta = $metaSeo;
        }

        return $meta;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getCacheKey(Request $request)
    {
        return md5($request->getPathInfo() . $request->attributes->get('_route') . json_encode($request->attributes->get('_route_params')));
    }
}
