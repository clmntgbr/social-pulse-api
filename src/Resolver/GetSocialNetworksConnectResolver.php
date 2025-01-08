<?php

namespace App\Resolver;

use App\Dto\Api\GetSocialNetworksConnect;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class GetSocialNetworksConnectResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (GetSocialNetworksConnect::class !== $argument->getType()) {
            return;
        }

        $dto = new GetSocialNetworksConnect();
        $dto->socialNetworkType = $request->attributes->get('platform', null);
        $dto->callbackPath = $request->query->get('path', '/');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $dto;
    }
}
