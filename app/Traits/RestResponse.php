<?php

namespace App\Traits;

use App\Events\ErrorLog;
use App\Models\ErrorLogLegacy;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Response;

trait RestResponse
{
    /**
     * success
     *
     * @param  mixed $data
     * @param  mixed $code
     * @return mixed
     */
    public function success($data = [], $code = Response::HTTP_OK): mixed
    {
        return response()->json($data, $code);
    }

    /**
     * response
     *
     * @param mixed $data
     * @param mixed $code
     *
     * @return Illuminate\Http\Response
     */
    public function response($data, $code = Response::HTTP_OK)
    {
        return response()->json(["data" => ($data) ? $data : []], $code);
    }

    /**
     * information
     *
     * @param $message
     * @param mixed $code
     * @return mixed
     */
    public function information($message, $code = Response::HTTP_OK): mixed
    {
        return response()->json([
            'timestamps' => date('Y-m-d H:i:s'),
            'path' => request()->path(),
            'detail' => $message,
            'code' => $code
        ], $code);
    }

    /**
     * error
     *
     * @param  mixed $path
     * @param  mixed $exception
     * @param  mixed $message
     * @param  mixed $code
     * @return mixed
     */
    public function error($path, Throwable $exception, $message, $code)
    {
        // if ($code == Response::HTTP_INTERNAL_SERVER_ERROR) {
        //     event(new ErrorLog(new ErrorLogLegacy([
        //         'is_frontend' => 0,
        //         'path' =>  $path,
        //         'exception' =>  basename(str_replace('\\', '/', get_class($exception))),
        //         'line' => $exception->getLine(),
        //         'file' => $exception->getFile(),
        //         'detail' => $exception->getMessage(),
        //         'date_time' =>  date('Y-m-d H:i:s.v')
        //     ])));
        // }
        // dd(54);
        return response()->json([
            'timestamps' => date('Y-m-d H:i:s'),
            'path' => $path,
            'exception' =>  basename(str_replace('\\', '/', get_class($exception))),
            'detail' => $this->checkIsArray($message),
            'code' => $code
        ], $code);
    }

    /**
     * streamDownload
     *
     * @param  mixed $path
     * @param  mixed $name
     * @return void
     */
    public function streamDownload($path, $name)
    {
        return response()->streamDownload(function () use ($path) {
            echo file_get_contents($path);
        }, $name);
    }

    // /**
    //  * success
    //  *
    //  * @param  string|array $data
    //  * @param mixed $cookie
    //  * @param  int $code
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function successCookie ($data = [], $cookie, $code = Response::HTTP_OK) {
    //     return response()->json($data, $code)->withCookie($cookie);
    // }

    /**
     * checkIsArray
     *
     * @param  mixed $message
     * @return void
     */
    private function checkIsArray($message)
    {
        $messageArray = [];
        if (!is_array($message)) {
            array_push($messageArray, $message);
            $message = $messageArray;
        }
        return collect($message)->unique()->values()->all();
    }

    /**
     * successStr
     *
     * @param  mixed $data
     * @param  mixed $code
     * @return Illuminate\Http\Response
     */
    public function successStr($data = [], $code = Response::HTTP_OK)
    {
        return response($data, $code)->header('Content-Type', 'application/json');
    }

    /**
     * errorStr
     *
     * @param  mixed $message
     * @param  mixed $code
     * @return Illuminate\Http\Response
     */
    public function errorStr($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }
}
