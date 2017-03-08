<?php
define('APPLICATION_PATH', dirname(__DIR__));
use Zend\Http\Request;

class SW_HttpServer
{
    public static $request;
    protected $config;
    protected $application;
    protected static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (empty(self::$instance) || !(self::$instance instanceof SW_HttpServer)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function start()
    {
        $http = new swoole_http_server($this->config['server']['swoole']['ip'], $this->config['server']['swoole']['port']);
        $http->set($this->config['server']['swoole']['option']);
        $http->on('Start' , array($this , 'onStart'));
        $http->on('ManagerStart' , array($this , 'onManagerStart'));
        $http->on('WorkerStart' , array($this , 'onWorkerStart'));
        $http->on('Task', array($this , 'onTask'));
        $http->on('Finish', array($this , 'onFinish'));
        $http->on('Request', array($this, 'onRequest'));
        Yaf_Registry::set('sw_serv', $http);
        $http->start();
    }

    public function onStart(swoole_server $server)
    {
        swoole_set_process_name("php {$this->config['application']['name']} runing {$this->config['server']['swoole']['ip']}:{$this->config['server']['swoole']['port']} master:{$server->master_pid}");
    }

    public function onManagerStart(swoole_server $server)
    {
        swoole_set_process_name("php {$this->config['application']['name']} manager:{$server->manager_pid}");
    }

    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        if($worker_id < $server->setting['worker_num']) {
            swoole_set_process_name("php {$this->config['application']['name']} worker pid:{$server->worker_pid}");
            $this->application = new Yaf_Application($this->config);
            ob_start();
            $this->application->bootstrap()->run();
            ob_end_clean();
        } else {
            swoole_set_process_name("php {$this->config['application']['name']} tasker pid:{$server->worker_pid}");
        }
    }

    public function onTask(swoole_server $server, $task_id, $src_worker_id, $data) {}

    public function onFinish(swoole_server $server, $task_id, $data) {}

    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico' || $request->server['path_info'] == '/favicon.ico') {
            return $response->end();
        }
        self::$request = Request::fromString($request->data);
        ob_start();
        try {
            $yaf_request = new Yaf_Request_Http($request->server['request_uri'], '');
            $this->application->getDispatcher()->dispatch($yaf_request);
        } catch (Yaf_Exception $e) {
            var_dump($e);
        }
        $result = ob_get_contents();
        ob_end_clean();
        $response->header('Content-type', 'application/json');
        $response->end($result);
    }
}
$config = new Yaf_Config_Ini(APPLICATION_PATH . "/conf/application.ini", 'product');
SW_HttpServer::getInstance()->setConfig($config->toArray())->start();