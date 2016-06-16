<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder\Converter\Stubs;

use eZ\Publish\API\Repository\Values\Content\Section;

class SectionServiceStub
{
    public function loadSection($sectionId)
    {
        return new Section(
            array(
                'name' => 'Item section',
            )
        );
    }
}
