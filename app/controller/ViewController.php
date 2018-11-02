<?php

class ViewController extends Controller {

    public function indexAction()
    {
        $params = [];

        if (!empty($_POST)) {
            $id = (string)$_POST['id'];

            if ($id !== '') {
                $emailsData = $this->adapter->find('test', ['_id' => new MongoDB\BSON\ObjectID($id)]);

                $params = [
                    'url' => $emailsData->website,
                    'model' => $this->model
                ];
                $params['emails'] = $this->model->arrayToMultidimensionalArrayConverter($emailsData->emails, $emailsData->website);
            }
        }

        $this->view->generate('view.php', $params);
    }
}