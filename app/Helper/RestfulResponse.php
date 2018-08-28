<?php
namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

/**
 * Class RestfulResponse
 *
 */
class RestfulResponse extends JsonResponse
{
    /**
     * 编码选项
     *
     * @var int
     */
    protected $encodingOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * 创建接口响应
     *
     * @param mixed $data
     * @param int $status
     */
    public function __construct($data = null, $status = 200)
    {
        if (config('app.debug')) {
            // 调试模式下美化 JSON 输出
            $this->encodingOptions |= JSON_PRETTY_PRINT;
        }

        parent::__construct($data, $status, [], false);
    }

    /**
     * 创建成功响应
     *
     * @param $data
     * @param string $message
     * @return static
     */
    public static function success($data, $message = '')
    {
        $data = [
            'code'    => 0,
            'message' => $message,
            'data'    => $data
        ];

        return new static($data, 200);
    }

    /**
     * 创建错误响应
     *
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @param \Throwable $e
     *
     * @return static
     */
    public static function error($code, $message, $data = null, $status = 400, Throwable $e)
    {
        $data = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];

        if (config('app.debug') && $e) {
            $exception = [
                'code'     => $e->getCode(),
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'trace'    => static::parseTrace($e),
                'previous' => []
            ];

            $previous = $e;

            while ($previous = $previous->getPrevious()) {
                $exception['previous'][] = [
                    'code'     => $e->getCode(),
                    'message'  => $e->getMessage(),
                    'file'     => $e->getFile(),
                    'line'     => $e->getLine(),
                    'trace'    => static::parseTrace($e),
                ];
            }

            $data['exception'] = $exception;
        }

        return new static($data, $status);
    }

    /**
     * 获得异常栈信息
     *
     * @param \Throwable $e
     *
     * @return array
     */
    protected static function parseTrace(Throwable $e)
    {
        $trace = $e->getTraceAsString();

        return explode("\n", $trace);
    }
}
