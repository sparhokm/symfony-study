<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Common\Application\AppException;
use App\Common\Application\Denormalizer\DenormalizerException;
use App\Common\Application\Validator\ValidationException;
use App\Common\Domain\DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ErrorController extends AbstractController
{
    private bool $showErrorDetails;

    public function __construct(bool $showErrorDetails)
    {
        $this->showErrorDetails = $showErrorDetails;
    }

    public function show(Throwable $exception, DebugLoggerInterface $logger = null): Response
    {
        return match (true) {
            $exception instanceof DenormalizerException => $this->denormalizeException($exception),
            $exception instanceof ValidationException => $this->validatorException($exception),
            $exception instanceof AppException => $this->appException($exception),
            $exception instanceof BadRequestHttpException => $this->badRequestException($exception),
            $exception instanceof DomainException => $this->domainException($exception),
            default => $this->systemException($exception)
        };
    }

    private function domainException(DomainException $appException): Response
    {
        return $this->json(
            $this->getErrorData(
                [$appException->getMessage()],
                $appException
            ),
            422
        );
    }

    private function appException(AppException $appException): Response
    {
        return $this->json(
            $this->getErrorData(
                [$appException->getMessage()],
                $appException
            ),
            400
        );
    }

    private function badRequestException(BadRequestHttpException $appException): Response
    {
        return $this->json(
            $this->getErrorData(
                ['Ошибка входных данных.'],
                $appException
            ),
            400
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
            400
        );
    }

    private function denormalizeException(DenormalizerException $denormalizerException): Response
    {
        return $this->json(
            $this->getErrorData(
                ['Ошибка входных данных.'],
                $denormalizerException
            ),
            400
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
        if (!$this->showErrorDetails) {
            return null;
        }

        return [
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'class' => get_debug_type($throwable),
        ];
    }
}
