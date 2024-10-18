<?php

namespace App\Resolver;

use App\Dto\CreatePostsResponse;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CreatePostsResponseResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError,
        private SerializerInterface $serializer
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== CreatePostsResponse::class) {
            return;
        }

        $createPostsResponse = CreatePostsResponse::hydrate($this->serializer, $request->toArray());

        $errors = $this->validator->validate($createPostsResponse);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $createPostsResponse;
    }
}
