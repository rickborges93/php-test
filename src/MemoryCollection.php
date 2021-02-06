<?php

namespace Live\Collection;

use DateTime;

/**
 * Memory collection
 *
 * @package Live\Collection
 */
class MemoryCollection implements CollectionInterface
{
    /**
     * Collection data
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }

        return $this->data[$index]['value'];
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $index, $value, int $minutesToExpired = 60)
    {
        $now = (new DateTime());
        $expired = $now->modify("+$minutesToExpired minutes")->format('Y-m-d H:i:s');

        $this->data[$index] = [
            'value' => $value,
            'expired' => $expired
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $index)
    {
        $indexExists = array_key_exists($index, $this->data);
        if ($indexExists) {
            $now = (new DateTime())->format('Y-m-d H:i:s');
            if (strtotime($now) <= strtotime($this->data[$index]['expired'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->data = [];
    }
}
