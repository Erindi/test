<?php

class HomepageController extends Controller {

    public function indexAction()
    {
        $params = [];

        if (!empty($_POST)) {
            $url = (string)$_POST['url'];
            $depth = (int)$_POST['depth'];
            $maxEmails = (int)$_POST['maxEmails'];

            $rawEmailsDataArray = $this->model->getAllEmailsAndLinks($url, $depth, $maxEmails);
            $params['url'] = $url;
            $params['emailsCount'] = $rawEmailsDataArray['emailsCount'];

            if (!empty($rawEmailsDataArray['emails'])) {
                $insertedId = $this->adapter->write('test', $rawEmailsDataArray);
                $params['insertedId'] = $insertedId;
            }
        }

        $this->view->generate('homepage.php', $params);
    }
}