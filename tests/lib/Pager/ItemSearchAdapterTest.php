<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Pager;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use PHPUnit\Framework\TestCase;

final class ItemSearchAdapterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Pager\ItemSearchAdapter
     */
    private $adapter;

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->adapter = new ItemSearchAdapter($this->backendMock, 'text');
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::__construct
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getNbResults
     */
    public function testGetNbResults(): void
    {
        $this->backendMock
            ->expects(self::once())
            ->method('searchCount')
            ->with(self::identicalTo('text'))
            ->willReturn(3);

        self::assertSame(3, $this->adapter->getNbResults());
    }

    /**
     * @covers \Netgen\ContentBrowser\Pager\ItemSearchAdapter::getSlice
     */
    public function testGetSlice(): void
    {
        $this->backendMock
            ->expects(self::once())
            ->method('search')
            ->with(
                self::identicalTo('text'),
                self::identicalTo(5),
                self::identicalTo(10)
            )
            ->willReturn([1, 2, 3]);

        self::assertSame([1, 2, 3], $this->adapter->getSlice(5, 10));
    }
}
