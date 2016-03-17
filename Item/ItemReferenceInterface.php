<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ItemReferenceInterface
{
    public function getId();

    public function setId($id);

    public function getParentId();

    public function setParentId($parentId);

    public function getName();

    public function setName($name);
}
