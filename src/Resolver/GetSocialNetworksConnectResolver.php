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

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): iterable
    {
        if (GetSocialNetworksConnect::class !== $argumentMetadata->getType()) {
            return;
        }

        $getSocialNetworksConnect = new GetSocialNetworksConnect();
        $getSocialNetworksConnect->socialNetworkType = $request->attributes->get('platform', null);
        $getSocialNetworksConnect->callbackPath = $request->query->get('path', '/');

        $errors = $this->validator->validate($getSocialNetworksConnect);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $getSocialNetworksConnect;
    }
}
