<?php

namespace Alex\BehatLauncher\Form;

use Alex\BehatLauncher\Form\Type\FeaturesType;
use Alex\BehatLauncher\Form\Type\RunType;
use Symfony\Component\Form\AbstractExtension;

class BehatLauncherExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadTypes()
    {
        return array(
            new FeaturesType(),
            new RunType(),
        );
    }
}
