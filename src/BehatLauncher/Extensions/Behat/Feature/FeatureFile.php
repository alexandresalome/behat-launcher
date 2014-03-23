<?php

namespace BehatLauncher\Extensions\Behat\Feature;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FeatureFile implements NormalizableInterface
{
    private $directory;
    private $name;

    public function __construct(FeatureDirectory $directory, $name)
    {
        $this->directory = $directory;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
   public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return array(
            'type' => 'file',
            'path' => $this->getPath(),
            'name' => $this->name
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $parent = $this->directory->getPath();

        if ($parent === '') {
            return $this->name;
        }

        return $parent.'/'.$this->name;
    }
}
