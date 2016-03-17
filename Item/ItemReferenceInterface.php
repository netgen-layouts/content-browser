<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use JsonSerializable;

interface ItemReferenceInterface extends JsonSerializable
{
    public function getId();

    public function setId($id);

    public function getParentId();

    public function setParentId($parentId);

    public function getName();

    public function setName($name);
}
