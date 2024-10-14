<?php

namespace App\Resolver;

use App\Dto\FacebookCallback;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class FacebookCallbackResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== FacebookCallback::class) {
            return;
        }

        $facebookCallback = new FacebookCallback();
        $facebookCallback->code = $request->query->get('code');
        $facebookCallback->state = $request->query->get('state');

        $errors = $this->validator->validate($facebookCallback);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $facebookCallback;
    }
}
