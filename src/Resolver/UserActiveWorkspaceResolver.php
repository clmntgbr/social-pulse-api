<?php

namespace App\Resolver;

use App\Dto\UserActiveWorkspace;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class UserActiveWorkspaceResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== UserActiveWorkspace::class) {
            return;
        }

        $content = $request->getContent();
        $userActiveWorkspace = $this->serializer->deserialize($content, UserActiveWorkspace::class, 'json');

        $errors = $this->validator->validate($userActiveWorkspace);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $userActiveWorkspace;
    }
}
