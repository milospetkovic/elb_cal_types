<?php

namespace OCA\ElbCalTypes\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\ElbCalTypes\Service\ElbCalTypeNotFound;


trait Errors {

    protected function handleNotFound (Closure $callback): DataResponse {
        try {
            return new DataResponse($callback());
        } catch(ElbCalTypeNotFound $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }

}