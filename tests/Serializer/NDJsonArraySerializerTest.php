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

use Elastic\Transport\Serializer\NDJsonArraySerializer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class NDJsonArraySerializerTest extends TestCase
{
    public function setUp(): void
    {
        $this->serializer = new NDJsonArraySerializer();
        $this->request = $this->createStub(ResponseInterface::class);
        $this->stream = $this->createStub(StreamInterface::class);

        $this->request->method('getBody')
            ->willReturn($this->stream);

        $this->ndjson = <<<'EOT'
{"index":{"_index":"test","_id":"1"}}
{"field1":"value1"}
{"delete":{"_index":"test","_id":"2"}}

EOT;
    }

    public function testDeserialize()
    {
        $this->stream->method('getContents')
            ->willReturn($this->ndjson);

        $result = $this->serializer->deserialize($this->request);
        $this->assertIsArray($result);
        $this->assertEquals('test', $result[0]['index']['_index']);
        $this->assertEquals('value1', $result[1]['field1']);
        $this->assertEquals('2', $result[2]['delete']['_id']);
    }

    public function testSerialize()
    {
        $data = [
            [
                'index' => [
                    '_index' => 'test',
                    '_id' => '1'
                ]
            ],
            [
                'field1' => 'value1'
            ],
            [
                'delete' => [
                    '_index' => 'test',
                    '_id' => '2'
                ]
            ]
        ];
        $result = $this->serializer->serialize($data);
        $this->assertEquals($this->ndjson, $result);
    }

    public function testSerializeWithEmptyElement()
    {
        $ndjson = <<<'EOT'
{"index":{"_index":"test","_id":"1"}}
{}
{"delete":{"_index":"test","_id":"2"}}

EOT;

        $data = [
            [
                'index' => [
                    '_index' => 'test',
                    '_id' => '1'
                ]
            ],
            [],
            [
                'delete' => [
                    '_index' => 'test',
                    '_id' => '2'
                ]
            ]
        ];
        $result = $this->serializer->serialize($data);
        $this->assertEquals($ndjson, $result);
    }
}