<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder\Converter\Stubs;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;

class ContentServiceStub
{
    public function loadContentInfo($contentId)
    {
        return new ContentInfo();
    }

    public function loadContentByContentInfo(ContentInfo $contentInfo, array $languages = null, $versionNo = null, $useAlwaysAvailable = true)
    {
        return new Content(
            array(
                'versionInfo' => new VersionInfo(),
            )
        );
    }
}
