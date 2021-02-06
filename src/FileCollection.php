<?php

namespace Live\Collection;

use DateTime;

/**
 * File collection
 *
 * @package Live\Collection
 */
class FileCollection implements CollectionInterface
{
    /**
     * Collection file
     *
     * @var array
     */
    protected $database;
    private $filePath = 'storage/database.txt';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createFile();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $index, $defaultValue = null)
    {
        $value = $this->has($index);
        if (!$value) {
            return $defaultValue;
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $index, $value, int $minutesToExpired = 60)
    {
        $now = (new DateTime());
        $expired = $now->modify("+$minutesToExpired minutes")->format('Y-m-d H:i:s');
        $text = $index . '|' . $value .'|'. $expired . PHP_EOL;
        fwrite($this->database, $text);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $index)
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $archive = fopen($this->filePath, 'r');
        while (!feof($archive)) {
            $line = fgets($archive);

            if (is_null($line)) {
                continue;
            }

            $line = explode('|', $line);
            if (isset($line[0]) && isset($line[1]) && $index == $line[0] && strtotime($now) <= strtotime($line[2])) {
                fclose($archive);
                return $line[1];
            }
        }
        fclose($archive);
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        $lines = 0;
        $archive = fopen($this->filePath, 'r');
        while (!feof($archive)) {
            fgets($archive);
            $lines++;
        }
        fclose($archive);
        return $lines - 1;
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        unlink($this->filePath);
        $this->createFile();
    }

    /**
     * Read the file or create if doesn't exist
     *
     * @return void
     */
    private function createFile()
    {
        $this->database = fopen($this->filePath, 'a+');
        if ($this->database == false) {
            die('Error on create archive.');
        }
    }
}
