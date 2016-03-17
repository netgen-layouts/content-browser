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

        $data = array(
            'sections' => $this->serializeItems($this->backend->getSections()),
            'min_selected' => $this->config['min_selected'],
            'max_selected' => $this->config['max_selected'],
            'default_columns' => $defaultColumns,
            'available_columns' => $availableColumns,
        );

        return new JsonResponse($data);
    }
}
