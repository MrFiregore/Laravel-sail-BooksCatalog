<?php

namespace App\Exceptions;

use App\Http\DooResponse;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function whoopsHandler()
    {
        try {
            return app(\Whoops\Handler\HandlerInterface::class);
        }
        catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            return parent::whoopsHandler();
        }
    }

    /**
     * @inheritDoc
     */
    public function render($request, Throwable $e)
    {
        /**
         * @var \Illuminate\Http\Response $view
         */
        $view = parent::render($request, $e);

        if ($view instanceof DooResponse) {
            return $view;
        }

        if ($e instanceof ModelNotFoundException) {
            $ids = collect($request->route()->parameters())->flip()->only($e->getIds());

            return $this->sendErrorResponse(
                [
                    'code' => config('constants.apis.ERRCODE_INFOS_ROUTE_MODEL_NOT_FOUND'),
                    'data' => $ids->flip()->all(),
                    'msg'  => 'Ids not found (' . $ids->keys()->implode(",") . ')',
                ]
            );
        }
        return $this->sendErrorResponse(
            [
                'code' => config('constants.apis.ERRCODE_INTERNAL_ERROR'),

                'data' => (\App::environment(['local', 'testing']) || filter_var($request->get("debug", false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) ?
                    [(($view instanceof JsonResponse) ? $view->getContent() : $view->getOriginalContent()), 'tmp' => get_class($request),] : [],
            ]
        );
    }
}
