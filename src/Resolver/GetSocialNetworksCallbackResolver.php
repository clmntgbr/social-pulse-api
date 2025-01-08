<?php

namespace App\Resolver;

use App\Dto\Api\GetSocialNetworksCallback;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class GetSocialNetworksCallbackResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (GetSocialNetworksCallback::class !== $argument->getType()) {
            return;
        }

        $dto = new GetSocialNetworksCallback();
        $dto->code = $request->query->get('code', null);
        $dto->state = $request->query->get('state', null);
        $dto->oauthToken = $request->query->get('oauth_token', null);
        $dto->oauthVerifier = $request->query->get('oauth_verifier', null);
        $dto->socialNetworkType = $request->attributes->get('platform', null);

        $errors = $this->validator->validate($dto, null, [$dto->socialNetworkType]);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $dto;
    }
}
