<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function in_array;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function parse_str;
use function preg_match;
use function sprintf;
use const JSON_ERROR_NONE;

final class BodyParamsMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     * @link https://tools.ietf.org/html/rfc7231
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getParsedBody() || in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $handler->handle($request);
        }

        $contentType = $request->getHeaderLine('content-type');

        if (preg_match('#^application/(|[\S]+\+)json($|[ ;])#', $contentType)) {
            $parsedBody = json_decode((string) $request->getBody(), true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \Exception(sprintf(
                    'Error when parsing JSON request body: %s',
                    json_last_error_msg()
                ));
            }
        } elseif (preg_match('#^application/x-www-form-urlencoded($|[ ;])#', $contentType)) {
            parse_str((string) $request->getBody(), $parsedBody);
        }

        return $handler->handle(empty($parsedBody) ? $request : $request->withParsedBody($parsedBody));
    }
}
