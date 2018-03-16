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
class Cradle_Module_[[singular]]_ControllreTest extends TestCase
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
     * @var Request $request
     */
    protected $request;

    /**
     * @var Request $response
     */
    protected $response;

    /**
     * @var int $id
     */
    protected static $id;

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
     * GET Create test
     */
    public function testGetCreate()
    {
        cradle()->routeTo('GET', '/[[name]]/create', $this->request, $this->response);
        $this->assertContains('form', $this->response->getContent())
    }

    /**
     * POST Create test
     */
    public function testPostCreate()
    {
        cradle()->routeTo('POST', '/[[name]]/create', $this->request, $this->response);
        self::$id = $this->response->getResults('[[primary]]');
        $this->assertTrue(is_numeric(self::$id));
    }

    /**
     * GET Detail test
     */
    public function testGetDetail()
    {
        $route = sprintf('/[[name]]/detail/%s', self::$id);
        cradle()->routeTo('GET', $route, $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * GET Remove test
     */
    public function testGetRemove()
    {
        $route = sprintf('/[[name]]/remove/%s', self::$id);
        cradle()->routeTo('GET', $route, $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * GET Restore test
     */
    public function testGetRestore()
    {
        $route = sprintf('/[[name]]/restore/%s', self::$id);
        cradle()->routeTo('GET', $route, $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * GET Search test
     */
    public function testGetSearch()
    {
        cradle()->trigger('[[name]]-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getTotal());
    }

    /**
     * GET Update test
     */
    public function testGetUpdate()
    {
        $route = sprintf('/[[name]]/update/%s', self::$id);
        cradle()->routeTo('GET', $route, $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
        $this->assertContains('form', $this->response->getContent())
    }

    /**
     * POST Update test
     */
    public function testPostUpdate()
    {
        $route = sprintf('/[[name]]/update/%s', self::$id);
        cradle()->routeTo('POST', $route, $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }
}
