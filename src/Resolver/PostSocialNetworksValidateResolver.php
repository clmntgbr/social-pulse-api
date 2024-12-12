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
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== PostSocialNetworksValidate::class) {
            return;
        }

        $dto = new PostSocialNetworksValidate();
        $dto->validate = $request->attributes->get('validate', null);
        $dto->uuids = $request->toArray();

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $dto;
    }
}