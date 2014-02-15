<?php

namespace Alex\BehatLauncher\Form\Type;

use Alex\BehatLauncher\Behat\Project;
use Alex\BehatLauncher\Behat\ProjectList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeaturesType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['path'] = $options['path'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['features'] as $name => $content) {
            if (is_array($content)) {
                $path = null === $options['path'] ? $name : $options['path'].'/'.$name;
                $sanitizedName = $this->sanitizeName($name);
                $builder->add($sanitizedName, 'behat_launcher_features', array(
                    'project'  => $options['project'],
                    'path'     => $path,
                    'features' => $content,
                    'label'    => $name.'/'
                ));
            } else {
                $sanitizedName = $this->sanitizeName($content);
                $builder->add($sanitizedName, 'checkbox', array('label' => $content, 'required' => false));
            }
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $result = array();
            foreach ($form->getConfig()->getOption('features') as $key => $content) {
                if (is_array($content)) {
                    $name = $key;
                    $sanitizeName = $this->sanitizeName($name);
                    $value = $data[$sanitizeName];
                    if (count($value)) {
                        $result[$name] = $value;
                    }
                } else {
                    $name = $content;
                    $sanitizeName = $this->sanitizeName($name);
                    $value = $content;
                    if ($data[$sanitizeName]) {
                        $result[] = $name;
                    }
                }
            }

            $event->setData($result);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'project' => null,
            'path'    => null,
            'features' => function (Options $options, $v) {
                if (null === $v) {
                    return $options['project']->getFeatures();
                }
            },
            'multiple' => true,
            'expanded' => true
        ));

        $resolver->setRequired(array('project'));
        $resolver->setAllowedTypes(array('project' => 'Alex\BehatLauncher\Behat\Project'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'behat_launcher_features';
    }

    private function sanitizeName($name)
    {
        return strtr($name, array('.' => '_'));
    }
}
