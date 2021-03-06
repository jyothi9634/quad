<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
      // echo "gfgdfgdf";exit;
       // print_r($e->message);
            
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }       
        
        /*if ($e instanceof \Illuminate\Database\QueryException)
        return response(view('errors.404'), 404);
            
        if ($e instanceof \Symfony\Component\Debug\Exception\NotFoundHttpException)
        return response(view('errors.404'), 404);
		
		if ($e instanceof \Symfony\Component\Debug\Exception\BadRequestHttpException)
        return response(view('errors.404'), 404);
		
		if ($e instanceof \Symfony\Component\Debug\Exception\HttpException)
        return response(view('errors.404'), 404);		
		
        if ($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException){
        return response(view('errors.404'), 404);	
        }*/
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {            
            return redirect('/')->with('message', 'Sorry, your session seems to have expired. Please login again.');
        }
	
		/*if ($this->isHttpException($e))
		{       
			if($e instanceof NotFoundHttpException)
			{
				return response()->view('errors.404', [], 404);
			}
			if (
			return $this->renderHttpException($e);
		}*/

        return parent::render($request, $e);
    }
}
