<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller extends BaseController
{
    /**
     * Initializes the controller by setting the container and performing basic access checks.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function initialize(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function buildPager(AdapterInterface $adapter, Request $request)
    {
        $maxLimit = $this->getParameter('netgen_content_browser.browser.max_limit');
        $defaultLimit = $this->getParameter('netgen_content_browser.browser.default_limit');

        $currentPage = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', $defaultLimit);

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $maxLimit ? $limit : $maxLimit);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }
}
