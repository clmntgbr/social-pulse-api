<?php

namespace App\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use App\Service\ContextService;
use Symfony\Component\HttpFoundation\Request;

class GroupsContextBuilder implements SerializerContextBuilderInterface
{
    private array $allowedEntity = [];

    public function __construct(
        private readonly SerializerContextBuilderInterface $decorated,
        private readonly ContextService $contextService
    ){}

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if (!in_array($resourceClass, $this->allowedEntity, true)) {
            return $context;
        }

        $data = $request->query->get('groups', '');
        $groups = $this->contextService->getGroups($data);

        if (is_null($groups)) {
            return $context;
        }

        $context['groups'] = $groups;
        return $context;
    }
}