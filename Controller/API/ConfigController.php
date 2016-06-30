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
        $data = array(
            'value_type' => $this->config['value_type'],
            'sections' => $this->itemSerializer->serializeLocations(
                $this->getSections()
            ),
            'min_selected' => $this->config['min_selected'],
            'max_selected' => $this->config['max_selected'],
            'default_limit' => $this->getParameter('netgen_content_browser.browser.default_limit'),
            'default_columns' => $this->config['default_columns'],
            'available_columns' => $this->getAvailableColumns(),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns the list of available columns from configuration.
     *
     * @return array
     */
    protected function getAvailableColumns()
    {
        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->get('translator');

        $availableColumns = array();

        foreach ($this->config['columns'] as $identifier => $columnData) {
            $availableColumns[] = array(
                'id' => $identifier,
                'name' => $translator->trans($columnData['name'], array(), 'ngcb'),
            );
        }

        return $availableColumns;
    }

    /**
     * Returns the sections specified in config, or default ones if config list is empty.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    protected function getSections()
    {
        if (empty($this->config['sections'])) {
            return $this->itemRepository->getDefaultSections($this->config['value_type']);
        }

        $sections = array();
        foreach ($this->config['sections'] as $sectionId) {
            try {
                $sections[] = $this->itemRepository->loadLocation(
                    $sectionId,
                    $this->config['value_type']
                );
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        return $sections;
    }
}
