<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
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
        $availableColumns = array();
        foreach ($this->config['columns'] as $identifier => $columnData) {
            $availableColumns[] = array(
                'id' => $identifier,
                'name' => $this->get('translator')->trans($columnData['name']),
            );
        }

        array_unshift(
            $availableColumns,
            array(
                'id' => 'name',
                'name' => $this->get('translator')->trans('netgen_content_browser.columns.name'),
            )
        );

        $defaultColumns = $this->config['default_columns'];
        array_unshift($defaultColumns, 'name');

        $sections = array();
        foreach ($this->config['root_items'] as $itemId) {
            try {
                $sections[] = $this->valueLoader->load($itemId);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $data = array(
            'value_type' => $this->config['value_type'],
            'sections' => $this->itemSerializer->serializeValues($sections),
            'min_selected' => $this->config['min_selected'],
            'max_selected' => $this->config['max_selected'],
            'default_limit' => $this->config['default_limit'],
            'default_columns' => $defaultColumns,
            'available_columns' => $availableColumns,
        );

        return new JsonResponse($data);
    }
}
