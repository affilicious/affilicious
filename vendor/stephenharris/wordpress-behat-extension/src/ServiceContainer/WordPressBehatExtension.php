<?php

namespace StephenHarris\WordPressBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class WordPressBehat
 *
 * @package StephenHarris\WordPressBehat\ServiceContainer
 */
class WordPressBehatExtension implements ExtensionInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'wordpress';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        $extensionManager->activateExtension('SensioLabs\Behat\PageObjectExtension');
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        //TODO This over-rides the config file, what if the end user wanted to add their own namespaces?
        //How can we place nice?
        $pages = array( 'StephenHarris\WordPressBehatExtension\Context\Page' );
        $container->setParameter('sensio_labs.page_object_extension.namespaces.page', $pages);
        $elements = array( 'StephenHarris\WordPressBehatExtension\Context\Page\Element' );
        $container->setParameter('sensio_labs.page_object_extension.namespaces.element', $elements);
    }

  /**
   * {@inheritdoc}
   */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path')
                    ->defaultValue(__DIR__ . 'vendor')
                ->end()
                ->arrayNode('symlink')
                    ->children()
                        ->scalarNode('from')
                            ->defaultValue('.')
                        ->end()
                        ->scalarNode('to')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('flush_database')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('db')
                            ->defaultValue('wordpress')
                        ->end()
                        ->scalarNode('username')
                            ->defaultValue('root')
                        ->end()
                        ->scalarNode('password')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mail')
                    ->children()
                        ->scalarNode('directory')
                            ->defaultValue(getenv('WORDPRESS_FAKE_MAIL_DIR'))
                        ->end()
                        ->scalarNode('divider')
                            ->defaultValue('%%===================%%')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadContextInitializer($container);
        $container->setParameter('wordpress.parameters', $config);
    }

    /**
     * Register a Context Initializer service for the behat
     *
     * @param ContainerBuilder $container the service will check for WordPressContext contexts
     */
    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(
            'StephenHarris\WordPressBehatExtension\Context\Initializer\WordPressContextInitializer',
            array(
                '%wordpress.parameters%',
                '%mink.parameters%',
            )
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('behat.wordpress.service.wordpress_context_initializer', $definition);
    }
}
