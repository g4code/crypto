<?php

use G4\Crypto\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Adapter
     */
    private $adapter;


    protected function setUp()
    {
        $this->adapter = new Adapter();
    }

    protected function tearDown()
    {
        $this->adapter = null;
    }

    public function test()
    {
        
    }
}