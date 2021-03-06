<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class LoadConfig extends AbstractController
{
    private BackendInterface $backend;

    private Configuration $config;

    private ItemSerializerInterface $itemSerializer;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     *
     * Deprecated: Add typehint when support for Symfony 3.4 ends
     */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        BackendInterface $backend,
        Configuration $config,
        ItemSerializerInterface $itemSerializer,
        $translator
    ) {
        $this->backend = $backend;
        $this->config = $config;
        $this->itemSerializer = $itemSerializer;
        $this->translator = $translator;
    }

    /**
     * Returns the configuration for content browser.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $sections = [];
        foreach ($this->backend->getSections() as $section) {
            $sections[] = $this->itemSerializer->serializeLocation($section);
        }

        $data = [
            'item_type' => $this->config->getItemType(),
            'sections' => $sections,
            'min_selected' => $this->config->getMinSelected(),
            'max_selected' => $this->config->getMaxSelected(),
            'has_tree' => $this->config->hasTree(),
            'has_search' => $this->config->hasSearch(),
            'has_preview' => $this->config->hasPreview(),
            'default_columns' => $this->config->getDefaultColumns(),
            'available_columns' => $this->getAvailableColumns(),
        ];

        return $this->json($data);
    }

    /**
     * Returns the list of available columns from configuration.
     *
     * @return array<array<string, string>>
     */
    private function getAvailableColumns(): array
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
