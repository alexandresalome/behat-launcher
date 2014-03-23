<?php

namespace Alex\BehatLauncher\Extension\Behat;

use Alex\BehatLauncher\Configuration\PropertySet;
use Alex\BehatLauncher\Extension\AbstractExtension;

class BehatExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration(PropertySet $propertySet)
    {
        $propertySet
            ->addProperty('behat_properties', array('form_type' => 'behat_properties'))
            ->addProperty('behat_concurrency', array('form_type' => 'integer'))
            ->addProperty('behat_path', array('default' => 'bin/behat'))
            ->addProperty('behat_features_path', array('default' => 'features'))
            ->addProperty('behat_formats', array('form_type' => 'behat_formats'))
        ;
    }
}
