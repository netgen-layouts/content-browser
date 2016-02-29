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
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testMissingTrees()
    {
        $config = array(array());

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidTrees()
    {
        $config = array(array('trees' => 'trees'));

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testEmptyTrees()
    {
        $config = array(array('trees' => array()));

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testMissingRootLocations()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidRootLocations()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => 'root',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
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
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidRootLocation()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array('42'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidMinSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'min_selected' => '42',
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
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidMaxSelected()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'max_selected' => '42',
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
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
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals($config, $expectedConfig);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidLocationTemplate()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'location_template' => 42,
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
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
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidDefaultColumns()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'default_columns' => 'columns',
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
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testInvalidDefaultColumn()
    {
        $config = array(
            array(
                'trees' => array(
                    'default' => array(
                        'root_locations' => array(42),
                        'default_columns' => array('column'),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid($config);
    }
}
