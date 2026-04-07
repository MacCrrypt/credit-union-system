<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return parent::render($request, $exception);
        }

        $status = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
        $view = "errors.{$status}";

        if (!view()->exists($view)) {
            $view = 'errors.error';
        }

        return response()->view($view, [
            'code' => $status,
            'exception' => $exception,
        ], $status);
    }
}
