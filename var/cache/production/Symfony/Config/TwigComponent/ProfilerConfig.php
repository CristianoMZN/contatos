<?php

namespace Symfony\Config\TwigComponent;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ProfilerConfig 
{
    private $enabled;
    private $collectComponents;
    private $_usedProperties = [];

    /**
     * @default '%kernel.debug%'
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * Collect components instances
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function collectComponents($value): static
    {
        $this->_usedProperties['collectComponents'] = true;
        $this->collectComponents = $value;

        return $this;
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('enabled', $config)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $config['enabled'];
            unset($config['enabled']);
        }

        if (array_key_exists('collect_components', $config)) {
            $this->_usedProperties['collectComponents'] = true;
            $this->collectComponents = $config['collect_components'];
            unset($config['collect_components']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['collectComponents'])) {
            $output['collect_components'] = $this->collectComponents;
        }

        return $output;
    }

}
