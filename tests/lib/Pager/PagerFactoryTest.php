<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Pager\PagerFactory;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;

final class PagerFactoryTest extends TestCase
{
    /**
     * @var \Pagerfanta\Adapter\AdapterInterface
     */
    private $adapterMock;

    /**
     * @var \Netgen\ContentBrowser\Pager\PagerFactory
     */
    private $pagerFactory;

    public function setUp(): void
    {
        $this->adapterMock = $this->createMock(AdapterInterface::class);

        $this->adapterMock
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(500));

        $this->pagerFactory = new PagerFactory(25, 100);
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\PagerFactory::__construct
     * @covers \Netgen\ContentBrowser\Pager\PagerFactory::buildPager
     *
     * @dataProvider buildPagerProvider
     */
    public function testBuildPager(int $page, ?int $limit, int $currentPage, int $maxPerPage): void
    {
        $pager = $this->pagerFactory->buildPager(
            $this->adapterMock,
            $page,
            $limit
        );

        $this->assertInstanceOf(Pagerfanta::class, $pager);
        $this->assertTrue($pager->getNormalizeOutOfRangePages());
        $this->assertSame($maxPerPage, $pager->getMaxPerPage());
        $this->assertSame($currentPage, $pager->getCurrentPage());
    }

    public function buildPagerProvider(): array
    {
        return [
            [5, 20, 5, 20],
            [0, 20, 1, 20],
            [1, 20, 1, 20],
            [-5, 20, 1, 20],
            [5, null, 5, 25],
            [5, 0, 5, 100],
            [5, -20, 5, 100],
            [5, 100, 5, 100],
            [5, 150, 5, 100],
        ];
    }
}
