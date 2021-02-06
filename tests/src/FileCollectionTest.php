<?php

namespace Live\Collection;

use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase
{
    private $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new FileCollection();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->collection->clean();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function objectCanBeConstructed()
    {
        return $this->collection;
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     * @doesNotPerformAssertions
     */
    public function dataCanBeAdded()
    {
        $this->collection->set('index1', 'value');
        $this->collection->set('index2', 5);
        $this->collection->set('index3', true);
        $this->collection->set('index4', 6.5);
    }

     /**
     * @test
     * @depends dataCanBeAdded
     */
    public function dataCanBeRetrieved()
    {
        $this->collection->set('index1', 'value');

        $this->assertEquals('value', $this->collection->get('index1'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function inexistentIndexShouldReturnDefaultValue()
    {
        $this->assertNull($this->collection->get('index1'));
        $this->assertEquals('defaultValue', $this->collection->get('index1', 'defaultValue'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function newCollectionShouldNotContainItems()
    {
        $this->assertEquals(0, $this->collection->count());
    }

    /**
     * @test
     * @depends dataCanBeAdded
     */
    public function collectionWithItemsShouldReturnValidCount()
    {
        $this->collection->set('index1', 'value');
        $this->collection->set('index2', 5);
        $this->collection->set('index3', true);

        $this->assertEquals(3, $this->collection->count());
    }

    /**
     * @test
     * @depends collectionWithItemsShouldReturnValidCount
     */
    public function collectionCanBeCleaned()
    {
        $this->collection->set('index', 'value');
        $this->assertEquals(1, $this->collection->count());

        $this->collection->clean();
        $this->assertEquals(0, $this->collection->count());
    }

    /**
     * @test
     * @depends collectionCanBeCleaned
     */
    public function dataCantBeRetrievedWithExpiredTime()
    {
        $this->collection->set('index1', 'value', -60);
        $this->assertNull($this->collection->get('index1'));
    }
}
