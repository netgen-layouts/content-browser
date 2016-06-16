<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder\Converter\Stubs;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;

class ContentTypeServiceStub
{
    public function loadContentType($contentTypeId)
    {
        return new ContentType(
            array(
                'identifier' => 'type1',
                'fieldDefinitions' => array(),
            )
        );
    }
}
