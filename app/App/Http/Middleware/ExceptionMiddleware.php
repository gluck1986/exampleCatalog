<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use HttpSoft\Response\JsonResponse;
use League\OpenAPIValidation\PSR15\Exception\InvalidServerRequestMessage;
use League\OpenAPIValidation\Schema\Exception\SchemaMismatch;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ExceptionMiddleware implements MiddlewareInterface
{
    public function __construct()
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (InvalidServerRequestMessage $e) {
            $result = $this->getValidationMessage($e);

            return new JsonResponse($result, 400);
        }
    }

    /**
     * @param \Exception|InvalidServerRequestMessage $e
     */
    private function getValidationMessage(\Exception|InvalidServerRequestMessage $e): string
    {
        $result = $e->getMessage();
        $middleMessage = $e->getPrevious()?->getMessage();

        if ($middleMessage) {
            $result .= '; ' . $middleMessage;
        }

        $prev = $e->getPrevious()?->getPrevious();

        if ($prev instanceof SchemaMismatch) {
            $dataBreadCrumb = $prev->dataBreadCrumb();

            if ($dataBreadCrumb == null) {
                $reason = $prev->getMessage();
                $result .= '; ' . $reason;
            } else {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $field = implode('.', $dataBreadCrumb->buildChain());
                $reason = $prev->getMessage();
                $result .= '; Field: ' . $field;
                $result .= '; ' . $reason;
            }
        }

        return $result;
    }
}
