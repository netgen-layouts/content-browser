<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;

class ConfigController extends Controller
{
    /**
     * Returns the configuration for content browser.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfig()
    {
        $data = array(
            'item_type' => $this->config->getItemType(),
            'sections' => $this->itemSerializer->serializeLocations(
                $this->backend->getDefaultSections()
            ),
            'min_selected' => $this->config->getMinSelected(),
            'max_selected' => $this->config->getMaxSelected(),
            'has_tree' => $this->config->hasTree(),
            'has_search' => $this->config->hasSearch(),
            'has_preview' => $this->config->hasPreview(),
            'default_limit' => $this->getParameter('netgen_content_browser.browser.default_limit'),
            'default_columns' => $this->config->getDefaultColumns(),
            'available_columns' => $this->getAvailableColumns(),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns the list of available columns from configuration.
     *
     * @return array
     */
    private function getAvailableColumns()
    {
        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->get('translator');

        $availableColumns = array();

        foreach ($this->config->getColumns() as $identifier => $columnData) {
            $availableColumns[] = array(
                'id' => $identifier,
                'name' => $translator->trans($columnData['name'], array(), 'ngcb'),
            );
        }

        return $availableColumns;
    }
}
