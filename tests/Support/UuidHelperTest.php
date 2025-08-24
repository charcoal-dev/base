<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Random\RandomException;

/**
 * This class contains test cases for validating the functionality
 * of the UuidHelper utility class.
 */
class UuidHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws RandomException
     */
    public function testUuid4_isValidRfc4122V4AndMostlyUnique(): void
    {
        $seen = [];
        for ($i = 0; $i < 200; $i++) {
            $uuid = \Charcoal\Base\Support\Helpers\UuidHelper::uuid4();

            // Valid format and RFC4122 version/variant
            $this->assertTrue(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid($uuid));
            $this->assertMatchesRegularExpression(
                "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/",
                $uuid
            );

            $seen[$uuid] = true;
        }

        $this->assertSame(200, count($seen), "UUIDv4 should be unique with overwhelming probability");
    }

    public function testUuid5_deterministicKnownVectorAndValid(): void
    {
        $nsDns = "6ba7b810-9dad-11d1-80b4-00c04fd430c8"; // DNS namespace
        $name = "example.com";

        $uuid = \Charcoal\Base\Support\Helpers\UuidHelper::uuid5($nsDns, $name);

        // Known test vector for UUIDv5(DNS, "example.com")
        $this->assertSame("cfbff0d1-9375-5685-968c-48ce8b15ae17", $uuid);

        // Deterministic
        $this->assertSame($uuid, \Charcoal\Base\Support\Helpers\UuidHelper::uuid5($nsDns, $name));

        // Valid per validator and regex (version 5 + RFC4122 variant)
        $this->assertTrue(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid($uuid));
        $this->assertMatchesRegularExpression(
            "/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/",
            $uuid
        );

        // Changing inputs changes the UUID
        $this->assertNotSame($uuid, \Charcoal\Base\Support\Helpers\UuidHelper::uuid5($nsDns, "example.net"));
        $nsUrl = "6ba7b811-9dad-11d1-80b4-00c04fd430c8";
        $this->assertNotSame($uuid, \Charcoal\Base\Support\Helpers\UuidHelper::uuid5($nsUrl, $name));
    }

    /**
     * @return void
     * @throws \Random\RandomException
     */
    public function testIsValidUuid_acceptsMultipleVersionsAndCaseInsensitive(): void
    {
        // Version 1 (valid)
        $this->assertTrue(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("6ba7b810-9dad-11d1-80b4-00c04fd430c8"));

        // Uppercase hex should be accepted (case-insensitive)
        $this->assertTrue(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("2ED6657D-E927-568B-95E1-2665A8AEA6A2"));

        // A fresh v4 must also validate
        $this->assertTrue(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid(\Charcoal\Base\Support\Helpers\UuidHelper::uuid4()));
    }

    public function testIsValidUuid_rejectsMalformedVersionAndVariant(): void
    {
        // Invalid hex character
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("g23e4567-e89b-12d3-a456-426655440000"));

        // Missing hyphens
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567e89b12d3a456426655440000"));

        // Wrong length
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567-e89b-12d3-a456-42665544000"));

        // Invalid version nibble (0 or 9 not allowed by [1-8])
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567-e89b-02d3-a456-426655440000"));
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567-e89b-92d3-a456-426655440000"));

        // Invalid variant (must be 8,9,a,b). Here it's 7.
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567-e89b-12d3-7456-426655440000"));

        // Leading/trailing whitespace should invalidate
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid(" 123e4567-e89b-12d3-a456-426655440000"));
        $this->assertFalse(\Charcoal\Base\Support\Helpers\UuidHelper::isValidUuid("123e4567-e89b-12d3-a456-426655440000 "));
    }
}