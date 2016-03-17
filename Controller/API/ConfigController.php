<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;

class ConfigController extends Controller
{
    public function getConfig()
    {
        $config = $this->get('netgen_content_browser.current_config');
        $backend = $this->get('netgen_content_browser.current_backend');

        $availableColumns = array();
        foreach ($config['columns'] as $identifier => $columnData) {
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

        $defaultColumns = $config['default_columns'];
        array_unshift($defaultColumns, 'name');

        $data = array(
            'sections' => $this->serializeItems($backend->getSections()),
            'min_selected' => $config['min_selected'],
            'max_selected' => $config['max_selected'],
            'default_columns' => $defaultColumns,
            'available_columns' => $availableColumns,
        );

        return new JsonResponse($data);
    }
}
