<?php

namespace Alex\BehatLauncher\Configuration;

class PropertySet
{
    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var array
     */
    private $propertyDefaults = array(
        'form_type'   => 'text',
        'default'     => null,
        'project'     => true,
        'run'         => true
    );

    /**
     * Adds a property to the property set.
     *
     * @param string $name
     * @param array  $values
     *
     * @return PropertySet
     */
    public function addProperty($name, array $values)
    {
        $this->properties[$name] = array_merge($this->propertyDefaults, $values);

        return $this;
    }

    /**
     * Converts the property set to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }
}
