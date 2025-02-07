<?php

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $this->formatValidation($exception, $event);

            return;
        }

        $validationFailedException = $exception->getPrevious();

        if ($validationFailedException instanceof ValidationFailedException) {
            $this->formatValidation($validationFailedException, $event);
        }
    }

    public function formatValidation(ValidationFailedException $validationFailedException, ExceptionEvent $event): void
    {
        $violations = $validationFailedException->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $jsonResponse = new JsonResponse(
            [
                'message' => 'Validation error',
                'data' => $errors,
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $event->setResponse($jsonResponse);
        $event->stopPropagation();
    }
}
