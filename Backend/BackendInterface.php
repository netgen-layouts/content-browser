<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

interface BackendInterface
{
    public function getSections();

    public function loadItem($itemId);

    public function getChildren(array $params = array());

    public function getChildrenCount(array $params = array());

    public function search(array $params = array());
}
