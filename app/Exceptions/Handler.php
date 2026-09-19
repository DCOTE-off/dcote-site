<?php

namespace App\Exceptions;

use App\Helpers\SeoMeta;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Страницы ошибок рендерим Inertia-компонентом, чтобы не поддерживать
        // отдельный Blade-фронтенд.
        $this->renderable(function (Throwable $e, Request $request) {
            $status = $this->inertiaErrorStatus($e);

            if ($status === null) {
                return null;
            }

            try {
                // Для несуществующих URL web-мидлварь (и HandleInertiaRequests) не
                // отрабатывает — шарим общие пропсы сами, иначе shell падает.
                if ($request->hasSession()) {
                    Inertia::share(app(HandleInertiaRequests::class)->share($request));
                }

                return Inertia::render('Errors/Error', [
                    'status' => $status,
                    'meta' => SeoMeta::make(
                        "Ошибка {$status}",
                        null,
                        null,
                        ['robots' => 'noindex, nofollow'],
                    ),
                ])->toResponse($request)->setStatusCode($status);
            } catch (Throwable) {
                // Shell не собрался (например, БД недоступна) — отдаём статичный Blade/дефолт.
                return null;
            }
        });
    }

    private function inertiaErrorStatus(Throwable $e): ?int
    {
        if (
            $e instanceof ValidationException
            || $e instanceof AuthenticationException
            || $e instanceof TokenMismatchException
        ) {
            return null;
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();

            return in_array($status, [403, 404, 405, 429], true) ? $status : null;
        }

        if ($e instanceof AuthorizationException) {
            return 403;
        }

        // Непредвиденные ошибки — только в проде (в dev пусть будет debug-страница).
        return config('app.debug') ? null : 500;
    }
}
