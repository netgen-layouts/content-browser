<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Builder\Converter\Stubs;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\User\UserReference;
use eZ\Publish\API\Repository\Values\ValueObject;
use Closure;

class RepositoryStub implements Repository
{
    public function sudo(Closure $callback)
    {
        return $callback($this);
    }

    public function getCurrentUser()
    {
    }

    public function getCurrentUserReference()
    {
    }

    public function setCurrentUser(UserReference $user)
    {
    }

    public function hasAccess($module, $function, UserReference $user = null)
    {
    }

    public function canUser($module, $function, ValueObject $object, $targets = null)
    {
    }

    public function getContentService()
    {
        return new ContentServiceStub();
    }

    public function getContentLanguageService()
    {
    }

    public function getContentTypeService()
    {
        return new ContentTypeServiceStub();
    }

    public function getLocationService()
    {
    }

    public function getTrashService()
    {
    }

    public function getSectionService()
    {
        return new SectionServiceStub();
    }

    public function getSearchService()
    {
    }

    public function getUserService()
    {
    }

    public function getURLAliasService()
    {
    }

    public function getURLWildcardService()
    {
    }

    public function getObjectStateService()
    {
    }

    public function getRoleService()
    {
    }

    public function getFieldTypeService()
    {
    }

    public function beginTransaction()
    {
    }

    public function commit()
    {
    }

    public function rollback()
    {
    }

    public function commitEvent($event)
    {
    }
}
