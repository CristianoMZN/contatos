<?php

namespace ContainerLe42o0U;

class RequestPayloadValueResolverGhost3e63c2e extends \Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'serializer' => [parent::class, 'serializer', null, 530],
        "\0".parent::class."\0".'translationDomain' => [parent::class, 'translationDomain', null, 16],
        "\0".parent::class."\0".'translator' => [parent::class, 'translator', null, 530],
        "\0".parent::class."\0".'validator' => [parent::class, 'validator', null, 530],
        'serializer' => [parent::class, 'serializer', null, 530],
        'translationDomain' => [parent::class, 'translationDomain', null, 16],
        'translator' => [parent::class, 'translator', null, 530],
        'validator' => [parent::class, 'validator', null, 530],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('RequestPayloadValueResolverGhost3e63c2e', false)) {
    \class_alias(__NAMESPACE__.'\\RequestPayloadValueResolverGhost3e63c2e', 'RequestPayloadValueResolverGhost3e63c2e', false);
}
