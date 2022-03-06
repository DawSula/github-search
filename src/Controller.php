<?php

declare(strict_types=1);

namespace App\src;

use App\src\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\src\Helper;

class Controller
{


    private $loader;
    private $twig;
    private Request $request;
    private Helper $helper;


    public function __construct()
    {
        $this->loader = new FilesystemLoader(__DIR__ . '/Views');
        $this->twig = new Environment($this->loader);
        $this->request = new Request();
        $this->helper = new Helper();
    }

    public function run(){

        $action = $this->request->getParams()['action'] ?? 'main';

        switch ($action){

            case 'main':
                $this->twig->addGlobal('_get', $_GET);
                echo $this->twig->render('main.html');
                break;

            case 'search':
                if (!($this->helper->validateData($this->request->getParams()))){
                    $this->redirect('/',['before'=>'dataError']);
                };
//                $sort = $this->request->getParams()['sort'];
                $param = $this->request->getParams()['github'];
                $data = $this->helper->filterData($param);
                if (empty($data)){
                    $this->redirect('/',['before'=>'notFound']);
                }
//                $data = $this->helper->sortData($data, $sort);
                $this->twig->addGlobal('_get', $_GET);
                echo $this->twig->render('list.html', ['data'=>$data]);
                break;

            default:
                echo $this->twig->render('notFound.html');
                break;
        }
    }

    private function redirect(string $to, array $params): void
    {
        $location = $to;

        if (count($params)) {
            $queryParams = [];
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }
            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }
        header("Location: $location");
        exit;
    }


}