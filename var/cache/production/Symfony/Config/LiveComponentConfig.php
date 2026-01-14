<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class LiveComponentConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $secret;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * The secret used to compute fingerprints and checksums
     * @default '%kernel.secret%'
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function secret($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['secret'] = true;
        $this->secret = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'live_component';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('secret', $config)) {
            $this->_usedProperties['secret'] = true;
            $this->secret = $config['secret'];
            unset($config['secret']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['secret'])) {
            $output['secret'] = $this->secret;
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
