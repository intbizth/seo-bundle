<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;
use Toro\SeoBundle\Model\MetaSeoInterface;
use Toro\SeoBundle\Model\MetaSeoTranslationInterface;

class ClearMetaSeoCache implements EventSubscriber
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    private function clearCache(LifecycleEventArgs $args)
    {
        if (!$args->getObject() instanceof MetaSeoInterface && !$args->getObject() instanceof MetaSeoTranslationInterface) {
            return;
        }

        $this->cacheItemPool->clear();
    }
}
