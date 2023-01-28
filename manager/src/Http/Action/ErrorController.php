<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Common\Domain\Exception\AppException;
use App\Common\Infrastructure\Validator\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ErrorController extends AbstractController
{
    public function show(Throwable $exception, DebugLoggerInterface $logger = null): Response
    {
        return match (true) {
            $exception instanceof AppException => $this->appException($exception),
            $exception instanceof ValidationException => $this->validatorException($exception),
            default => $this->systemException($exception)
        };
    }

    private function appException(AppException $appException): Response
    {
        return $this->json(
            $this->getErrorData(
                [$appException->getMessage()],
                $appException
            ),
            422
        );
    }

    private function validatorException(ValidationException $validatorException): Response
    {
        $errorData = $this->getErrorData(
            ['Ошибка входных данных.'],
            $validatorException
        );
        $errorData = ['detail' => $this->validatorErrorsArray($validatorException->getViolations())] + $errorData;

        return $this->json(
            $errorData,
            422
        );
    }

    private function systemException(Throwable $throwable): Response
    {
        return $this->json(
            $this->getErrorData(
                ['Системная ошибка.'],
                $throwable
            ),
            500
        );
    }

    private function validatorErrorsArray(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }

    private function getErrorData(array $textErrors, Throwable $throwable): array
    {
        $errors = ['errors' => $textErrors];
        if ($debugInfo = $this->getDebugInfo($throwable)) {
            $errors['debug'] = $debugInfo;
        }

        return $errors;
    }

    private function getDebugInfo(Throwable $throwable): ?array
    {
        if ($this->getParameter('http.error.show_detail')) {
            return [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
                'class' => get_debug_type($throwable),
            ];
        }

        return null;
    }
}
