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

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): iterable
    {
        if (GetSocialNetworksCallback::class !== $argumentMetadata->getType()) {
            return;
        }

        $getSocialNetworksCallback = new GetSocialNetworksCallback();
        $getSocialNetworksCallback->code = $request->query->get('code', null);
        $getSocialNetworksCallback->state = $request->query->get('state', null);
        $getSocialNetworksCallback->oauthToken = $request->query->get('oauth_token', null);
        $getSocialNetworksCallback->oauthVerifier = $request->query->get('oauth_verifier', null);
        $getSocialNetworksCallback->socialNetworkType = $request->attributes->get('platform', null);

        $errors = $this->validator->validate($getSocialNetworksCallback, null, [$getSocialNetworksCallback->socialNetworkType]);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $getSocialNetworksCallback;
    }
}
