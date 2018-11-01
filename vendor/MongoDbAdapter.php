<?php
class MongoDbAdapter {

    /** @var string  */
    private $uri = 'mongodb://localhost:27017';

    /**
     * @var \MongoDB\Driver\Manager
     */
    private $manager;

    /**
     * @var string
     */
    private $database;

    public function __construct(string $database = 'test') {

        $this->manager = new MongoDB\Driver\Manager($this->uri);
        $this->database = $database . '.';
    }

    /**
     * @param string $collection
     * @param array $data
     * @return string
     */
    public function write(string $collection, array $data) {

        $insRec = new MongoDB\Driver\BulkWrite;
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY);
        $insertedId = $insRec->insert($data);

        $this->manager->executeBulkWrite($this->database . $collection, $insRec, $writeConcern);
        return $insertedId;
    }

    /**
     * @param string $collection MongoDb collection name
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function find(string $collection, array $filter = [], array $options = []) {

        try {
            $query = new MongoDB\Driver\Query($filter, $options);

            return $this->manager->executeQuery($this->database . $collection, $query)->toArray()[0] ?? [];

        } catch (\MongoDb\Driver\Exception\Exception $exception) {
            echo $exception->getMessage();die;
        }
    }
}