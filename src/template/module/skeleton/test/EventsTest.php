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
class Cradle_Module_[[singular]]_EventTest extends TestCase
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
     * Create test
     */
    public function testCreate()
    {
        cradle()->trigger('[[name]]-create', $this->request, $this->response);
        self::$id = $this->response->getResults('[[primary]]');
        $this->assertTrue(is_numeric(self::$id));
    }

    /**
     * Detail test
     */
    public function testDetail()
    {
        $request->setStage('[[primary]]', self::$id);
        cradle()->trigger('[[name]]-detail', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * Remove test
     */
    public function testRemove()
    {
        $request->setStage('[[primary]]', self::$id);
        cradle()->trigger('[[name]]-remove', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * Restore test
     */
    public function testRestore()
    {
        $request->setStage('[[primary]]', self::$id);
        cradle()->trigger('[[name]]-restore', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }

    /**
     * Search test
     */
    public function testSearch()
    {
        cradle()->trigger('[[name]]-search', $this->request, $this->response);
        $this->assertEquals(1, $this->response->getTotal());
    }

    /**
     * Update test
     */
    public function testUpdate()
    {
        $request->setStage('[[primary]]', self::$id);
        cradle()->trigger('[[name]]-update', $this->request, $this->response);
        $this->assertEquals(self::$id, $this->response->getResults('[[primary]]'));
    }
}
