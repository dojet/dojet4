<?php
/**
 * web service
 *
 * @author liyan
 * @since 2014
 */
abstract class WebService extends Service implements IRouteDelegate {

    private $actionExecutor;

    public function dojetDidStart() {
        $uri = $this->uriWillRoute($_SERVER['REQUEST_URI']);
        $router = $this->router();
        DAssert::assert($router instanceof IRoutable, 'illegal router');
        $this->actionExecutor = $this->actionExecutor();
        $router->route($uri);
    }

    protected function router() {
        return new Router($this);
    }

    private function actionExecutor() {
        while(true) {
            $action = yield;
            DAssert::assert($action instanceof BaseAction, 'illegal action');
            $action->execute();
        }
    }

    public function executeAction(BaseAction $action) {
        $this->actionExecutor->send($action);
    }

    public function uriWillRoute($uri) {
        $uri = substr($uri, 1);
        return $uri;
    }

    public function routeFinished($action) {
        $classFile = $action.'.class.php';
        DAssert::assertFileExists($classFile, 'action class not exists. file: '.$classFile);
        require $classFile;

        $className = basename($action);
        $actionIns = new $className($this);

        $this->executeAction($actionIns);
    }

    public function notFound($uri) {
        header('HTTP/1.1 404 Not Found');
    }

    abstract public function root();
}
