<?php

namespace AmeliaBooking\Application\Services\Cache;

use AmeliaBooking\Domain\Entity\Cache\Cache;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Common\Container;
use AmeliaBooking\Infrastructure\Repository\Cache\CacheRepository;
use InvalidArgumentException;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class CacheApplicationService
 *
 * @package AmeliaBooking\Application\Services\Cache
 */
class CacheApplicationService
{
    private $container;

    /**
     * CacheApplicationService constructor.
     *
     * @param Container $container
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws ContainerValueNotFoundException
     * @throws QueryExecutionException
     */
    public function getCacheByName($name)
    {
        /** @var CacheRepository $cacheRepository */
        $cacheRepository = $this->container->get('domain.cache.repository');

        /** @var Cache $cache */
        $cache = ($data = explode('_', $name)) && isset($data[0], $data[1]) ?
            $cacheRepository->getByIdAndName($data[0], $data[1]) : null;

        if ($cache && $cache->getData()) {
            return json_decode($cache->getData()->getValue(), true);
        }

        return '';
    }
}
