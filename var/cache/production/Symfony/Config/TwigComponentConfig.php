<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'TwigComponent'.\DIRECTORY_SEPARATOR.'DefaultsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'TwigComponent'.\DIRECTORY_SEPARATOR.'ProfilerConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class TwigComponentConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $defaults;
    private $anonymousTemplateDirectory;
    private $profiler;
    private $controllersJson;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * @template TValue of string|array
     * @param TValue $value
     * @default ["__deprecated__use_old_naming_behavior"]
     * @return \Symfony\Config\TwigComponent\DefaultsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\TwigComponent\DefaultsConfig : static)
     * @deprecated since Symfony 7.4
     */
    public function defaults(string $namespace, string|array $value = []): \Symfony\Config\TwigComponent\DefaultsConfig|static
    {
        $this->_hasDeprecatedCalls = true;
        if (!\is_array($value)) {
            $this->_usedProperties['defaults'] = true;
            $this->defaults[$namespace] = $value;

            return $this;
        }

        if (!isset($this->defaults[$namespace]) || !$this->defaults[$namespace] instanceof \Symfony\Config\TwigComponent\DefaultsConfig) {
            $this->_usedProperties['defaults'] = true;
            $this->defaults[$namespace] = new \Symfony\Config\TwigComponent\DefaultsConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "defaults()" has already been initialized. You cannot pass values the second time you call defaults().');
        }

        return $this->defaults[$namespace];
    }

    /**
     * Defaults to `components`
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function anonymousTemplateDirectory($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['anonymousTemplateDirectory'] = true;
        $this->anonymousTemplateDirectory = $value;

        return $this;
    }

    /**
     * @template TValue of array|bool
     * @param TValue $value
     * Enables the profiler for Twig Component
     * @default {"enabled":"%kernel.debug%","collect_components":true}
     * @return \Symfony\Config\TwigComponent\ProfilerConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\TwigComponent\ProfilerConfig : static)
     * @deprecated since Symfony 7.4
     */
    public function profiler(array|bool $value = []): \Symfony\Config\TwigComponent\ProfilerConfig|static
    {
        $this->_hasDeprecatedCalls = true;
        if (!\is_array($value)) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = $value;

            return $this;
        }

        if (!$this->profiler instanceof \Symfony\Config\TwigComponent\ProfilerConfig) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = new \Symfony\Config\TwigComponent\ProfilerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "profiler()" has already been initialized. You cannot pass values the second time you call profiler().');
        }

        return $this->profiler;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @deprecated Since symfony/ux-twig-component 2.18: The "twig_component.controllers_json" config option is deprecated, and will be removed in 3.0.
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function controllersJson($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['controllersJson'] = true;
        $this->controllersJson = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'twig_component';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('defaults', $config)) {
            $this->_usedProperties['defaults'] = true;
            $this->defaults = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\TwigComponent\DefaultsConfig($v) : $v, $config['defaults']);
            unset($config['defaults']);
        }

        if (array_key_exists('anonymous_template_directory', $config)) {
            $this->_usedProperties['anonymousTemplateDirectory'] = true;
            $this->anonymousTemplateDirectory = $config['anonymous_template_directory'];
            unset($config['anonymous_template_directory']);
        }

        if (array_key_exists('profiler', $config)) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = \is_array($config['profiler']) ? new \Symfony\Config\TwigComponent\ProfilerConfig($config['profiler']) : $config['profiler'];
            unset($config['profiler']);
        }

        if (array_key_exists('controllers_json', $config)) {
            $this->_usedProperties['controllersJson'] = true;
            $this->controllersJson = $config['controllers_json'];
            unset($config['controllers_json']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['defaults'])) {
            $output['defaults'] = array_map(fn ($v) => $v instanceof \Symfony\Config\TwigComponent\DefaultsConfig ? $v->toArray() : $v, $this->defaults);
        }
        if (isset($this->_usedProperties['anonymousTemplateDirectory'])) {
            $output['anonymous_template_directory'] = $this->anonymousTemplateDirectory;
        }
        if (isset($this->_usedProperties['profiler'])) {
            $output['profiler'] = $this->profiler instanceof \Symfony\Config\TwigComponent\ProfilerConfig ? $this->profiler->toArray() : $this->profiler;
        }
        if (isset($this->_usedProperties['controllersJson'])) {
            $output['controllers_json'] = $this->controllersJson;
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
