<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\Configuration;

use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\NetgenContentBrowserExtension;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        $extension = new NetgenContentBrowserExtension();

        return new Configuration($extension->getAlias());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testConfiguration()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'min_selected' => 3,
                        'max_selected' => 5,
                        'location_template' => 'template.html.twig',
                        'default_columns' => array('id', 'parent_id'),
                        'categories' => array(
                            'types' => array('type'),
                        ),
                        'children' => array(
                            'types' => array('type2'),
                            'include_category_types' => false,
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'trees' => array(
                'default' => array(
                    'root_locations' => array(42),
                    'min_selected' => 3,
                    'max_selected' => 5,
                    'location_template' => 'template.html.twig',
                    'default_columns' => array('id', 'parent_id'),
                    'categories' => array(
                        'types' => array('type'),
                    ),
                    'children' => array(
                        'types' => array('type2'),
                        'include_category_types' => false,
                    ),
                ),
            ),
            'adapters' => array(
                'ezpublish' => array(
                    'image_fields' => array('image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testMinimalConfiguration()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'categories' => array(
                            'types' => array('type'),
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'trees' => array(
                'default' => array(
                    'root_locations' => array(42),
                    'min_selected' => 1,
                    'max_selected' => 0,
                    'location_template' => 'NetgenContentBrowserBundle:ezpublish:location.html.twig',
                    'default_columns' => array('name', 'type', 'visible'),
                    'categories' => array(
                        'types' => array('type'),
                    ),
                ),
            ),
            'adapters' => array(
                'ezpublish' => array(
                    'image_fields' => array('image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyRootLocations()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testNegativeMinSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'min_selected' => -5,
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testNullMinSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'min_selected' => null,
                        'max_selected' => 5,
                        'location_template' => 'template.html.twig',
                        'default_columns' => array('id', 'parent_id'),
                        'categories' => array(
                            'types' => array('type'),
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'trees' => array(
                'default' => array(
                    'root_locations' => array(42),
                    'min_selected' => 0,
                    'max_selected' => 5,
                    'location_template' => 'template.html.twig',
                    'default_columns' => array('id', 'parent_id'),
                    'categories' => array(
                        'types' => array('type'),
                    ),
                ),
            ),
            'adapters' => array(
                'ezpublish' => array(
                    'image_fields' => array('image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testNegativeMaxSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'max_selected' => -5,
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testNullMaxSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'min_selected' => 3,
                        'max_selected' => null,
                        'location_template' => 'template.html.twig',
                        'default_columns' => array('id', 'parent_id'),
                        'categories' => array(
                            'types' => array('type'),
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'trees' => array(
                'default' => array(
                    'root_locations' => array(42),
                    'min_selected' => 3,
                    'max_selected' => 0,
                    'location_template' => 'template.html.twig',
                    'default_columns' => array('id', 'parent_id'),
                    'categories' => array(
                        'types' => array('type'),
                    ),
                ),
            ),
            'adapters' => array(
                'ezpublish' => array(
                    'image_fields' => array('image'),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyLocationTemplate()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'location_template' => '',
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyDefaultColumns()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'default_columns' => array(),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyCategoryTypes()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'categories' => array(
                            'types' => array(),
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array('image'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyEzPublishAdapterImageFields()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'categories' => array(
                            'types' => array('type'),
                        ),
                    ),
                ),
                'adapters' => array(
                    'ezpublish' => array(
                        'image_fields' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }
}
