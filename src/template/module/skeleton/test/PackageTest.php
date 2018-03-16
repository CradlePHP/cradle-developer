<?php //-->
/**
 * This file is part of a Custom Project
 * (c) 2017-2019 Acme Inc
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request;
use Cradle\Http\Response;

use PHPUnit\Framework\TestCase;

/**
 * Event test
 *
 * @vendor   Acme
 * @package  Profile
 * @author   John Doe <john@acme.com>
 */
class Cradle_Module_[[singular]]_PackageTest extends TestCase
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var Request $response
     */
    protected $response;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->request = Request::i()->load();
        $this->response = Response::i()->load();
    }

    /**
     * Create test
     */
    public function testInstall()
    {
        cradle()->trigger('module-[[name]]-install', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Update test
     */
    public function testUpdate()
    {
        cradle()->trigger('module-[[name]]-update', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Remove test
     */
    public function testRemove()
    {
        cradle()->trigger('module-[[name]]-remove', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Elastic Flush test
     */
    public function testElasticFlush()
    {
        cradle()->trigger('module-[[name]]-elastic-flush', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Elastic Map test
     */
    public function testElasticMap()
    {
        cradle()->trigger('module-[[name]]-elastic-map', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Elastic Populate test
     */
    public function testElasticPopulate()
    {
        cradle()->trigger('module-[[name]]-elastic-populate', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Redis Flush test
     */
    public function testRedisFlush()
    {
        cradle()->trigger('module-[[name]]-redis-flush', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * Redis Populate test
     */
    public function testRedisPopulate()
    {
        cradle()->trigger('module-[[name]]-redis-populate', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * SQL Build test
     */
    public function testSqlBuild()
    {
        cradle()->trigger('module-[[name]]-sql-build', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * SQL Flush test
     */
    public function testSqlFlush()
    {
        cradle()->trigger('module-[[name]]-sql-flush', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }

    /**
     * SQL Populate test
     */
    public function testSqlopulate()
    {
        cradle()->trigger('module-[[name]]-sql-populate', $this->request, $this->response);
        $this->assertFalse($response->isError());
    }
}
