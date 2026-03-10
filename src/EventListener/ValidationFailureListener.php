<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final class ValidationFailureListener {
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onValidationFailure(ExceptionEvent $event): void {
        $exception = $this->extractValidationExeption($event->getThrowable());

        if(!$exception) return;

        $violations = [];
        foreach($exception->getViolations() as $violation){
            $violations[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ]; 
        }

        $response = new JsonResponse(['status' => 'error', 'message' => 'validation failed', 'errors' => $violations], 400);
        $event->setResponse($response);
    }

    public function extractValidationExeption(Throwable $exception): ?ValidationFailedException{
        while($exception){
            if($exception instanceof ValidationFailedException) return $exception;
            $exception = $exception->getPrevious();
        }
        return null;
    }
}