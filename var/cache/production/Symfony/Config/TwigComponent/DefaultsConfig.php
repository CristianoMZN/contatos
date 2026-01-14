<?php

namespace Symfony\Config\TwigComponent;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefaultsConfig 
{
    private $templateDirectory;
    private $namePrefix;
    private $_usedProperties = [];

    /**
     * @default 'components'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function templateDirectory($value): static
    {
        $this->_usedProperties['templateDirectory'] = true;
        $this->templateDirectory = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function namePrefix($value): static
    {
        $this->_usedProperties['namePrefix'] = true;
        $this->namePrefix = $value;

        return $this;
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('template_directory', $config)) {
            $this->_usedProperties['templateDirectory'] = true;
            $this->templateDirectory = $config['template_directory'];
            unset($config['template_directory']);
        }

        if (array_key_exists('name_prefix', $config)) {
            $this->_usedProperties['namePrefix'] = true;
            $this->namePrefix = $config['name_prefix'];
            unset($config['name_prefix']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['templateDirectory'])) {
            $output['template_directory'] = $this->templateDirectory;
        }
        if (isset($this->_usedProperties['namePrefix'])) {
            $output['name_prefix'] = $this->namePrefix;
        }

        return $output;
    }

}
