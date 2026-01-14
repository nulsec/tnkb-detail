<?php

declare(strict_types=1);

namespace TNKB\Tests;

use PHPUnit\Framework\TestCase;
use TNKB\ClientConfig;
use TNKB\InvalidPlateException;
use TNKB\RegionCodes;
use TNKB\TNKBClient;

class TNKBClientTest extends TestCase
{
    private TNKBClient $client;

    protected function setUp(): void
    {
        $this->client = new TNKBClient(new ClientConfig());
    }

    public function testValidatePlateCorrectFormat(): void
    {
        $this->assertTrue($this->client->validatePlate('B 1234 ABC'));
        $this->assertTrue($this->client->validatePlate('AB 123 CDE'));
        $this->assertTrue($this->client->validatePlate('A 1 XYZ'));
    }

    public function testValidatePlateInvalidFormat(): void
    {
        $this->assertFalse($this->client->validatePlate('INVALID'));
        $this->assertFalse($this->client->validatePlate('1234567'));
        $this->assertFalse($this->client->validatePlate(''));
    }

    public function testGetRegionInfo(): void
    {
        $info = $this->client->getRegionInfo('B');
        $this->assertNotNull($info);
        $this->assertSame('B', $info->code);
        $this->assertSame('Jawa Barat', $info->name);
    }

    public function testGetRegionInfoUnknown(): void
    {
        $info = $this->client->getRegionInfo('ZZ');
        $this->assertNull($info);
    }

    public function testGetRegionInfoCaseInsensitive(): void
    {
        $info1 = $this->client->getRegionInfo('b');
        $info2 = $this->client->getRegionInfo('B');
        $this->assertSame($info1->code, $info2->code);
    }

    public function testListRegions(): void
    {
        $regions = $this->client->listRegions();
        $this->assertGreaterThan(30, \count($regions));

        foreach ($regions as $region) {
            $this->assertNotEmpty($region->code);
            $this->assertNotEmpty($region->name);
        }
    }

    public function testCheckPlateValidFormat(): void
    {
        $result = $this->client->checkPlate('B 1234 ABC');
        $this->assertNotEmpty($result->plateNumber);
        $this->assertNotEmpty($result->regionCode);
        $this->assertTrue($result->isValid);
    }

    public function testCheckPlateInvalidFormat(): void
    {
        $this->expectException(InvalidPlateException::class);
        $this->client->checkPlate('INVALID');
    }

    public function testBulkCheck(): void
    {
        $plates = ['B 1234 ABC', 'A 123 DEF'];
        $results = $this->client->bulkCheck($plates);

        $this->assertCount(2, $results);
        $this->assertNotEmpty($results[0]->plateNumber);
        $this->assertNotEmpty($results[1]->plateNumber);
    }

    public function testBulkCheckWithInvalid(): void
    {
        $plates = ['B 1234 ABC', 'INVALID'];
        $results = $this->client->bulkCheck($plates);

        $this->assertCount(2, $results);
        $this->assertTrue($results[0]->isValid);
        $this->assertFalse($results[1]->isValid);
    }
}
