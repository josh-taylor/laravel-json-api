<?php

/**
 * Copyright 2016 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\LaravelJsonApi\Routing;

use CloudCreativity\LaravelJsonApi\Contracts\Document\LinkFactoryInterface;
use CloudCreativity\LaravelJsonApi\TestCase;
use Illuminate\Contracts\Routing\Registrar;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class ResourceRegistrarTest
 * @package CloudCreativity\LaravelJsonApi
 */
final class ResourceRegistrarTest extends TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mock;

    /**
     * @var ResourceRegistrar
     */
    private $registrar;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var Registrar $registrar */
        $registrar = $this->getMockBuilder(Registrar::class)->getMock();

        $this->registrar = new ResourceRegistrar($registrar);
        $this->mock = $registrar;
    }

    public function testRegistration()
    {
        $index = sprintf(LinkFactoryInterface::ROUTE_NAME_INDEX, 'posts');
        $resource = sprintf(LinkFactoryInterface::ROUTE_NAME_RESOURCE, 'posts');
        $relatedResource = sprintf(LinkFactoryInterface::ROUTE_NAME_RELATED_RESOURCE, 'posts');
        $relationship = sprintf(LinkFactoryInterface::ROUTE_NAME_RELATIONSHIPS, 'posts');

        $this->willSee('GET', '/posts', 'index', $index);
        $this->willSee('POST', '/posts', 'create');
        $this->willSee('GET', '/posts/{resource_id}', 'read', $resource);
        $this->willSee('PATCH', '/posts/{resource_id}', 'update');
        $this->willSee('DELETE', '/posts/{resource_id}', 'delete');
        $this->willSee('GET', '/posts/{resource_id}/{relationship_name}', 'readRelatedResource', $relatedResource);
        $this->willSee('GET', '/posts/{resource_id}/relationships/{relationship_name}', 'readRelationship', $relationship);
        $this->willSee('PATCH', '/posts/{resource_id}/relationships/{relationship_name}', 'replaceRelationship');
        $this->willSee('POST', '/posts/{resource_id}/relationships/{relationship_name}', 'addToRelationship');
        $this->willSee('DELETE', '/posts/{resource_id}/relationships/{relationship_name}', 'removeFromRelationship');

        $this->registrar->resource('posts', 'PostsController');
    }

    /**
     * @param $uri
     * @param $httpMethod
     * @param $controllerMethod
     * @param $as
     */
    private function willSee(
        $httpMethod,
        $uri,
        $controllerMethod,
        $as = null
    ) {
        $options = ['uses' => 'PostsController@' . $controllerMethod];

        if ($as) {
            $options['as'] = $as;
        }

        $this->mock
            ->expects($this->at($this->index))
            ->method(strtolower($httpMethod))
            ->with($uri, $options);

        $this->index++;
    }
}
