<?php

namespace App\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;

#[AsDecorator('api_platform.serializer.context_builder')]
readonly class DynamicGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public const string SEPARATOR = ':';

    public function __construct(#[AutowireDecorated] private SerializerContextBuilderInterface $decorated)
    {
    }

    public function createFromRequest(Request $request,
        bool $normalization,
        ?array $extractedAttributes = null): array
    {
        return $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        /*$context['groups'] = $context['groups'] ?? [];
        $context['groups'] = array_unique(array_merge($context['groups'],
            $this->generateGroups($context, $normalization)));

        return $context;*/
    }

    /*public function generateGroups(array $context,
        bool $normalization): array
    {
        $resourceClass = $context['resource_class'] ?? null;

        if ($resourceClass === Error::class) {
            return [];
        }

        $resourceClassShortName = (new ReflectionClass($resourceClass))->getShortName();
        $classAlias = strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($resourceClassShortName)));

        $operationNameTab = explode('_', $context['operation_name'] ?? '');
        $operationName = end($operationNameTab);
        $inOrOutput = $normalization ? 'output' : 'input';

        $user = $this->security->getUser();
        $userClassAlias = 'anonymous';
        if ($user) {
            $userClassAlias = strtolower(preg_replace('/[A-Z]/',
                '_\\0',
                lcfirst((new ReflectionClass($user))->getShortName())));
        }

        $groups = [
            // {resourceClass}:{operationName}
            sprintf('%s' . self::SEPARATOR . '%s', $classAlias, $operationName),
            // {resourceClass}:{operationName}:{input/output}
            sprintf('%s' . self::SEPARATOR . '%s:%s', $classAlias, $operationName, $inOrOutput),
            // {userClassAlias}:{resourceClass}:{operationName}
            sprintf('%s' . self::SEPARATOR . '%s' . self::SEPARATOR . '%s',
                $userClassAlias,
                $classAlias,
                $operationName),
            // {userClassAlias}:{resourceClass}:{operationName}:{input/output}
            sprintf('%s' . self::SEPARATOR . '%s' . self::SEPARATOR . '%s' . self::SEPARATOR . '%s',
                $userClassAlias,
                $classAlias,
                $operationName,
                $inOrOutput)
        ];

        return $groups;
    }*/
}
