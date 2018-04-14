<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class CacheKernel extends HttpCache implements \FOS\HttpCache\SymfonyCache\CacheInvalidation
{
    use \FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;

    /**
     * CacheKernel constructor.
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param null $cacheDir
     */
    public function __construct(\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, $cacheDir = null)
    {
        parent::__construct($kernel, $cacheDir);

        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\CustomTtlListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\PurgeListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\RefreshListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\UserContextListener());

        if (isset($options['debug']) && $options['debug']) {
            $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\DebugListener());
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fetch(\Symfony\Component\HttpFoundation\Request $request, $catch = false)
    {
        return parent::fetch($request, $catch);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function invalidate(\Symfony\Component\HttpFoundation\Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        $response = new \Symfony\Component\HttpFoundation\Response();

        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(200, 'Purged');
        } else {
            $response->setStatusCode(404, 'Not found');
        }

        return $response;
    }
}