<?php

namespace App\Resolver;

use App\Dto\Api\PatchUserActiveOrganization;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PatchUserActiveOrganizationResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): iterable
    {
        if (PatchUserActiveOrganization::class !== $argumentMetadata->getType()) {
            return;
        }

        $patchUserActiveOrganization = new PatchUserActiveOrganization();
        $patchUserActiveOrganization->organizationUuid = $request->attributes->get('uuid', null);

        $errors = $this->validator->validate($patchUserActiveOrganization);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $patchUserActiveOrganization;
    }
}
