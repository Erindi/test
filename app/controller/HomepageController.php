<?php

class HomepageController extends Controller {

    public function indexAction()
    {
        $params = [];

        if (!empty($_POST['url']) && !empty($_POST['maxEmails'])) {
            $url = filter_var((string)$_POST['url'], FILTER_VALIDATE_URL);

            if ($url) {
                $depth = (int)$_POST['depth'] ?? 0;
                $maxEmails = (int)$_POST['maxEmails'];

                $rawEmailsDataArray = $this->model->getAllEmailsAndLinks($url, $depth, $maxEmails);
                $params['url'] = $url;
                $params['emailsCount'] = $rawEmailsDataArray['emailsCount'];

                if (!empty($rawEmailsDataArray['emails'])) {
                    $insertedId = $this->adapter->write('test', $rawEmailsDataArray);
                    $params['insertedId'] = $insertedId;
                }
            }
        }

        $this->view->generate('homepage.php', $params);
    }
}