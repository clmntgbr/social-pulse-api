<?php

namespace App\Resolver;

use App\Dto\Api\PostSocialNetworksValidate;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PostSocialNetworksValidateResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): iterable
    {
        if (PostSocialNetworksValidate::class !== $argumentMetadata->getType()) {
            return;
        }

        $postSocialNetworksValidate = new PostSocialNetworksValidate();
        $postSocialNetworksValidate->validate = $request->attributes->get('validate', null);
        $postSocialNetworksValidate->uuids = $request->toArray();

        $errors = $this->validator->validate($postSocialNetworksValidate);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $postSocialNetworksValidate;
    }
}
