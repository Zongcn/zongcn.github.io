<?php
class ApiController extends Yaf_Controller_Abstract
{
    public function entryAction()
    {
        $ret = 200;
        $result = '';
        $msg = '';
        $request = $this->getRequest();
        $method = $request->getQuery('method');
        if (empty($method) || strpos($method, '.') === false) throw new Exception('service or method is null', 500);
        list($controller, $action) = explode('.', $method);
        $controller = ucfirst($controller);
        $args = $_REQUEST;
        unset($args['method']);
        try {
            $class = "\\Api\\Controllers\\{$controller}";
            $controllerObject = new $class;
            $controllerObject->args = $args;
            if (method_exists($controllerObject, $action) === false) throw new Exception('service or method is null', 500);
            $result = call_user_func(array($controllerObject, $action));
        } catch (Exception $e) {
            $ret = $e->getCode();
            $msg = $e->getMessage();
        }

        $HttpStatus = self::getHttpStatusCode(200);
        header($HttpStatus);
        if (isset($_GET['jsoncallback']) && empty($_GET['jsoncallback']) == false) {
            header('Content-Type: application/x-javascript; charset=utf-8');
            echo $_GET['jsoncallback'] . "(" . json_encode(array('ret'=>$ret, 'data'=>$result, 'msg'=>$msg)) . ")";
        } else {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode(array('ret'=>$ret, 'data'=>$result, 'msg'=>$msg), JSON_UNESCAPED_UNICODE);
        }
        return false;
    }

    /**
     * 获取Http状态码
     *
     * @param int $num Http状态码
     * @return string
     */
    private static function getHttpStatusCode($num)
    {
        $httpStatusCodes = array(
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out"
        );
        return isset($httpStatusCodes[$num]) ? $httpStatusCodes[$num] : '';
    }	
}


/**
 * @api {get} /index.php/api/entry?mothod=user.get&id=1 获取用户信息
 * @apiName GetUser
 * @apiGroup User
 * @apiVersion 1.0.0
 * @apiSuccessExample {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *	'ret':200,
 *	'data': {
 *	    'code' => 0,
 *	    'info': {},
 *	    'msg': '获取成功'			
 *	},
 *	'msg': ''
 * }
 */
namespace Api\Controllers;

class Base
{
    private $_data = array();

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    protected function getData()
    {
        return $this->_data;
    }

    protected function getParams()
    {
        return $this->_data['args'];
    }
}

class User extends Base
{
    public function get()
    {
	$params = $this->getParams();
	$id = $params['id'];
	$data = array(
	    'code' => 0,
	    'info' => array(),
	    'msg' => '获取成功' 
	);
	return $data;
    }
}

