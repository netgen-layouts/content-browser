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
        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->get('translator');

        $availableColumns = array(
            array(
                'id' => 'name',
                'name' => $translator->trans('columns.name', array(), 'ngcb'),
            ),
        );

        foreach ($this->config['columns'] as $identifier => $columnData) {
            $availableColumns[] = array(
                'id' => $identifier,
                'name' => $translator->trans($columnData['name'], array(), 'ngcb'),
            );
        }

        $sections = array();
        foreach ($this->config['sections'] as $sectionId) {
            try {
                $sections[] = $this->itemRepository->load($sectionId, $this->config['value_type']);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $data = array(
            'value_type' => $this->config['value_type'],
            'sections' => $this->itemSerializer->serializeItems($sections),
            'min_selected' => $this->config['min_selected'],
            'max_selected' => $this->config['max_selected'],
            'default_limit' => $this->getParameter('netgen_content_browser.browser.default_limit'),
            'default_columns' => array_merge(array('name'), $this->config['default_columns']),
            'available_columns' => $availableColumns,
        );

        return new JsonResponse($data);
    }
}
