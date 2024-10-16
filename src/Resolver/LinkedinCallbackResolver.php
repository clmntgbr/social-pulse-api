<?php

namespace App\Resolver;

use App\Dto\LinkedinCallback;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class LinkedinCallbackResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== LinkedinCallback::class) {
            return;
        }

        $linkedinCallback = new LinkedinCallback();
        $linkedinCallback->code = $request->query->get('code');
        $linkedinCallback->state = $request->query->get('state');

        $errors = $this->validator->validate($linkedinCallback);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $linkedinCallback;
    }
}
