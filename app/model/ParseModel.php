<?php
class ParseModel {

    /**
     * Метод парсит сайт или линк в ширину, а не в глубину, тоесть сначала суказанного url,
     * потом сщ всех ссылок которые были на этой странице (depth = 1),
     * потом со всех ссылок, которые были на предыдущих страницах (depth = 2)
     * @param string $url
     * @param int $maxDepth
     * @param int $maxEmails
     * @return array
     */
    public function getAllEmailsAndLinks(string $url, int $maxDepth, int $maxEmails)
    {
        $depthCounter = 0;
        $emailsCounter = 0;
        $rawEmailsDataArray = [
            'website' => $url,
        ];

        $currentLevelLinksArray = [];
        $nextLevelLinksArray = [];

        $currentLevelLinksArray[$url] = $url;

        /**
         * Кажый depth-уровень мы собираем ссылки и создаем уникальные ключи для того
         * чтобы распознать по ключу глубину массива
         */
        while ($depthCounter <= $maxDepth) {

            foreach ($currentLevelLinksArray as $currentLevelLinkKey => $currentLevelLink) {

                if ($depthCounter === 0 || $currentLevelLink !== $url) {

                    $linkForParse = $currentLevelLink;
                    if ($depthCounter !== 0) {
                        $linkForParse = $this->getHost($url) . $currentLevelLink;
                    }

                    $dataFromCurrentLink = $this->parseContentFromLink($linkForParse);

                    $emailsList = array_unique($dataFromCurrentLink['emails']);
                    $emailDataArray = [];

                    if (!empty($emailsList)) {
                        $emailsNumber = count($emailsList);
                        $emailsCounter += $emailsNumber;


                        if ($maxEmails <= $emailsCounter) {
                            $redundantNumber = $maxEmails - $emailsCounter;
                            $slicedArray = array_slice($emailsList, $redundantNumber);

                            $emailDataArray['uniqueKey'] = $currentLevelLinkKey;
                            $emailDataArray['emails'] = $slicedArray;

                            $rawEmailsDataArray['emails'][] = $emailDataArray;
                            $rawEmailsDataArray['emailsCount'] = $maxEmails;

                            return $rawEmailsDataArray;
                        }

                        /** Привязываем полученные данные к уникальному ключу ссылки по которой мы получали данные */
                        $emailDataArray['uniqueKey'] = $currentLevelLinkKey;
                        $emailDataArray['emails'] = $emailsList;

                        $rawEmailsDataArray['emails'][] = $emailDataArray;
                    } else {
                        $emailDataArray['uniqueKey'] = $currentLevelLinkKey;
                        $emailDataArray['emails'][] = 'No emails was found on this page.';
                    }

                    $rawLinks = array_unique($dataFromCurrentLink['links']);

                    /** Добавляем ссылку с уникальным ключём в массив для следующего уровня */
                    foreach ($rawLinks as $link) {
                        $nextLevelLinksArray[$currentLevelLinkKey .'..'. $link] = $link;
                    }
                }
            }

            $currentLevelLinksArray = $nextLevelLinksArray;
            $nextLevelLinksArray = [];
            $depthCounter++;
        }

        $rawEmailsDataArray['emailsCount'] = $emailsCounter;

        return $rawEmailsDataArray;
    }

    /**
     * @param string $url
     * @return array
     */
    private function parseContentFromLink(string $url)
    {
        $emails = [];
        $links = [];

        $content = file_get_contents($url);
        if (!empty($content)) {
            $xpath = $this->getXpath($content);

            $emails = $this->parseEmails($xpath);
            $links = $this->parseLinks($xpath);
        }

        return ['emails' => $emails, 'links' => $links];
    }

    /**
     * @param DOMXPath $xpath
     * @return array
     */
    private function parseEmails(DOMXPath $xpath)
    {
        $emails = $xpath->query("/html/body//*[contains(text(), '@')]");

        $validEmails = [];
        if (is_iterable($emails)) {
            foreach ($emails as $email) {
                preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $email->textContent, $matches);
                $validEmail = $matches[0][0] ?? '';
                if ($validEmail !== '' ) {
                    $validEmails[] = $validEmail;

                }
            }
        }

        return $validEmails;
    }

    /**
     * @param DOMXPath $xpath
     * @return array
     */
    private function parseLinks(DOMXPath $xpath)
    {
        $internalLinks = $xpath->query("/html/body//a[starts-with(@href,'/')]/@href");

        $validLinks = [];
        if (is_iterable($internalLinks)) {
            foreach ($internalLinks as $website) {
                $link = $website->value;
                if (preg_match('/^[^.]*$/', $link) && $link !== '/') {
                    $validLinks[] = trim($link);
                }
            }
        }

        return $validLinks;
    }

    /**
     * Преобразование полученного из базы массива с уникальными ключами в многомерный массив
     * @param array $rawDataArray
     * @param string $url
     * @return array
     */
    public function arrayToMultidimensionalArrayConverter(array $rawDataArray, string $url)
    {
        $host = $this->getHost($url);

        $emailsArray = [];
        foreach ($rawDataArray as $testKey => $emailsDataArray) {

            $reverseEmailKeysArray = array_reverse(explode('..', $emailsDataArray->uniqueKey));

            $emailsArrayWithRecursiveStructure = [];

            foreach ($reverseEmailKeysArray as $key => $uniqueKey) {

                if ($uniqueKey !== $url) {
                    $uniqueKey = $host . $uniqueKey;
                }

                if ($key === 0) {
                    $emailsArrayWithRecursiveStructure[$uniqueKey] = $emailsDataArray->emails;

                    if (count($reverseEmailKeysArray) === 1) {
                        $emailsArrayWithRecursiveStructure[$uniqueKey] = $emailsDataArray->emails;
                    }

                } else {
                    $tempArray = $emailsArrayWithRecursiveStructure;
                    $emailsArrayWithRecursiveStructure = [];
                    $emailsArrayWithRecursiveStructure[$uniqueKey] = $tempArray;
                }
            }
            $emailsArray = array_merge_recursive($emailsArray, $emailsArrayWithRecursiveStructure);
        }

        return $emailsArray;
    }

    /**
     * @param string $content
     * @return DOMXPath
     */
    private function getXpath(string $content)
    {
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML((string) $content);
        libxml_clear_errors();

        return new \DOMXPath($dom);
    }

    /**
     * @param string $url
     * @return string
     */
    private function getHost(string $url)
    {
        $parsedUrl = parse_url($url);
        return $parsedUrl['scheme'] .'://' . $parsedUrl['host'];
    }

    public function makeEmailsList(array $array){
        $output = '<ul>';
        foreach($array as $key => $value){

            if (!is_int($key)) {
                $output .= "<li><b>{$key}: </b>";
            } else {
                $output .= "<li>";
            }

            if(is_array($value)){
                $output .= $this->makeEmailsList($value);
            } elseif (is_object($value)) {
                $output .= $this->makeEmailsList((array)$value);
            } else {
                $output .= $value;
            }
            
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
}