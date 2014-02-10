<?php

namespace Alex\BehatLauncherBundle\Form\Type;

use Alex\BehatLauncherBundle\Behat\Project;
use Alex\BehatLauncherBundle\Behat\ProjectList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeaturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['features'] as $name => $content) {
            $sanitizedName = str_replace('.', '_', $name);
            if (is_array($content)) {
                $builder->add($sanitizedName, 'behat_launcher_features', array(
                    'project' => $options['project'],
                    'features' => $content,
                    'label' => $name.'/'
                ));
            } else {
                $sanitizedName = str_replace('.', '_', $content);
                $builder->add($sanitizedName, 'checkbox', array('label' => $content, 'required' => false));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'project' => null,
            'features' => function (Options $options, $v) {
                if (null === $v) {
                    return $options['project']->getFeatures();
                }
            },
            'multiple' => true,
            'expanded' => true
        ));

        $resolver->setRequired(array('project'));
        $resolver->setAllowedTypes(array('project' => 'Alex\BehatLauncherBundle\Behat\Project'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'behat_launcher_features';
    }
}
