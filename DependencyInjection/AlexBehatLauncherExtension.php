<?php

namespace Alex\BehatLauncherBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AlexBehatLauncherExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('dev.xml');
        $loader->load('form.xml');


        $container->setAlias('behat_launcher.dbal_connection', $config['connection_id']);

        $ref = $container->getDefinition('behat_launcher.project_list');
        $projects = array();
        foreach ($config['projects'] as $name => $project) {
            $props = array();
            foreach ($project['properties'] as $propName => $property) {
                $prop = new Definition('Alex\BehatLauncherBundle\Behat\ProjectProperty');
                $prop
                    ->addMethodCall('setName', array($propName))
                    ->addMethodCall('setConfig', array($property['config']))
                    ->addMethodCall('setFormType', array($property['form_type']))
                    ->addMethodCall('setFormOptions', array($property['form_options']))
                ;
                $props[] = $prop;
            }

            $projects[] = new Definition('Alex\BehatLauncherBundle\Behat\Project', array(
                $name,
                $project['path'],
                $props
            ));
        }

        $ref->replaceArgument(0, $projects);
    }
}
