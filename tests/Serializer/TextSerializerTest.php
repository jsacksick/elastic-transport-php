<?php
/**
 * Elastic Transport
 *
 * @link      https://github.com/elastic/elastic-transport-php
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 *
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the Apache 2.0 License.
 * See the LICENSE file in the project root for more information.
 */
declare(strict_types=1);

namespace Elastic\Transport\Test\Serializer;

use Elastic\Transport\Serializer\TextSerializer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class TextSerializerTest extends TestCase
{
    public function setUp(): void
    {
        $this->serializer = new TextSerializer();
        $this->request = $this->createStub(ResponseInterface::class);
        $this->stream = $this->createStub(StreamInterface::class);

        $this->request->method('getBody')
            ->willReturn($this->stream);

        $this->body = 'Hello World!';
    }

    public function testDeserialize()
    {
        $this->stream->method('getContents')
            ->willReturn($this->body);

        $result = $this->serializer->deserialize($this->request);
        $this->assertEquals($this->body, $result);
    }

    public function testSerialize()
    {
        $data = $this->body;
        $result = $this->serializer->serialize($data);
        $this->assertEquals($this->body, $result);
    }
}