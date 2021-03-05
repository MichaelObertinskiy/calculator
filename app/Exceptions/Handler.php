<?php

namespace App\Exceptions;

use Throwable;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /* Render an exception into an HTTP response.
    *
    * @param  Request  $request
    * @param Exception $exception
    * @return Application|JsonResponse|RedirectResponse|Response|Redirector
    */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof BaseException) {
            return $this->convertBaseExceptionToResponse($exception, $request);
        }
        return parent::render($request, $exception);
    }

    /**
     * @param BaseException $exception
     * @param $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    protected function convertBaseExceptionToResponse(BaseException $exception, $request)
    {
        return $request->expectsJson()
            ? $this->invalidBaseExceptionJson($exception)
            : $this->invalidBaseException($request, $exception);
    }

    /**
     * Convert a base exception into a response.
     *
     * @param Request $request
     * @param BaseException $exception
     * @return Application|RedirectResponse|Redirector
     */
    protected function invalidBaseException($request, BaseException $exception)
    {
        return redirect(url()->previous())
            ->withInput(Arr::except($request->input(), $this->dontFlash))
            ->withErrors($exception->errors());
    }

    /**
     * Convert a base exception into a JSON response.
     *
     * @param BaseException $exception
     * @return JsonResponse
     */
    protected function invalidBaseExceptionJson(BaseException $exception)
    {
        return response()->json([
            'error' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->getCode());
    }
}
