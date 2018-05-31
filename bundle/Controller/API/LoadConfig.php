<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;

final class LoadConfig extends Controller
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    private $itemSerializer;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $defaultLimit;

    public function __construct(
        BackendInterface $backend,
        ConfigurationInterface $config,
        ItemSerializerInterface $itemSerializer,
        TranslatorInterface $translator,
        $defaultLimit
    ) {
        $this->backend = $backend;
        $this->config = $config;
        $this->itemSerializer = $itemSerializer;
        $this->translator = $translator;
        $this->defaultLimit = (int) $defaultLimit;
    }

    /**
     * Returns the configuration for content browser.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke()
    {
        $data = [
            'item_type' => $this->config->getItemType(),
            'sections' => $this->itemSerializer->serializeLocations(
                $this->backend->getDefaultSections()
            ),
            'min_selected' => $this->config->getMinSelected(),
            'max_selected' => $this->config->getMaxSelected(),
            'has_tree' => $this->config->hasTree(),
            'has_search' => $this->config->hasSearch(),
            'has_preview' => $this->config->hasPreview(),
            'default_limit' => $this->defaultLimit,
            'default_columns' => $this->config->getDefaultColumns(),
            'available_columns' => $this->getAvailableColumns(),
        ];

        return new JsonResponse($data);
    }

    /**
     * Returns the list of available columns from configuration.
     *
     * @return array
     */
    private function getAvailableColumns()
    {
        $availableColumns = [];

        foreach ($this->config->getColumns() as $identifier => $columnData) {
            $availableColumns[] = [
                'id' => $identifier,
                'name' => $this->translator->trans($columnData['name'], [], 'ngcb'),
            ];
        }

        return $availableColumns;
    }
}
