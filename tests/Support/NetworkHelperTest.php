<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Charcoal\Base\Support\Helpers\NetworkHelper;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test suite for the NetworkHelper utility class.
 *
 * This class contains a series of unit tests to validate the functionality
 * of methods within the NetworkHelper class, including those for validating IP
 * addresses and checking if an IP address falls within specific CIDR ranges.
 */
class NetworkHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValidIp_returns4ForValidIPv4(): void
    {
        $this->assertSame(4, NetworkHelper::isValidIp("127.0.0.1"));
        $this->assertSame(4, NetworkHelper::isValidIp("255.255.255.255"));
        $this->assertSame(4, NetworkHelper::isValidIp("0.0.0.0"));
    }

    public function testIsValidIp_returns6ForValidIPv6(): void
    {
        $this->assertSame(6, NetworkHelper::isValidIp("::1"));
        $this->assertSame(6, NetworkHelper::isValidIp("2001:0db8:85a3:0000:0000:8a2e:0370:7334"));
        $this->assertSame(6, NetworkHelper::isValidIp("2001:db8::1"));
    }

    public function testIsValidIp_returns6ForIPv4MappedIPv6(): void
    {
        $this->assertSame(6, NetworkHelper::isValidIp("::ffff:192.0.2.128"));
    }

    public static function invalidIpProvider(): array
    {
        return [
            [""],
            ["abc"],
            ["256.0.0.1"],
            ["1.2.3.4.5"],
            ["1.2.3"],
            [":::1"],
            ["::gggg"],
            [" 127.0.0.1"],
            ["127.0.0.1 "],
            ["2001:db8::zzzz"],
        ];
    }

    #[dataProvider("invalidIpProvider")]
    public function testIsValidIp_returnsFalseForInvalidInputs(string $ip): void
    {
        $this->assertFalse(NetworkHelper::isValidIp($ip));
    }

    #[dataProvider("invalidIpProvider")]
    public function testIsValidIp_invalidDataProviderWrapped(string $ip): void
    {
        $this->assertFalse(NetworkHelper::isValidIp($ip));
    }

    public function testIpInCidr_returnsFalseForInvalidIpInput(): void
    {
        $this->assertFalse(NetworkHelper::ipInCidr("not.an.ip", ["192.168.0.0/16"]));
        $this->assertFalse(NetworkHelper::ipInCidr(" 192.168.1.1", ["192.168.0.0/16"]));
    }

    public function testIpInCidr_returnsFalseForEmptyCidrList(): void
    {
        $this->assertFalse(NetworkHelper::ipInCidr("192.0.2.1", []));
        $this->assertFalse(NetworkHelper::ipInCidr("2001:db8::1", []));
    }

    public function testIpInCidr_matchesIPv4ExactHost(): void
    {
        $this->assertTrue(NetworkHelper::ipInCidr("203.0.113.5", ["203.0.113.5/32"]));
    }

    public function testIpInCidr_doesNotMatchIPv4OutsideRange(): void
    {
        $this->assertFalse(NetworkHelper::ipInCidr("203.0.113.5", ["203.0.114.0/24"]));
    }

    public function testIpInCidr_matchesIPv6ExactHost(): void
    {
        $this->assertTrue(NetworkHelper::ipInCidr("2001:db8::1234", ["2001:db8::1234/128"]));
    }

    public function testIpInCidr_doesNotMatchIPv6OutsideRange(): void
    {
        $this->assertFalse(NetworkHelper::ipInCidr("2001:db8::1234", ["2001:db8:1::/48"]));
    }

    public function testIpInCidr_mixedFamilyListMatchesOnlySameFamily(): void
    {
        // IPv4 IP should match IPv4 CIDR even if IPv6 CIDRs are present
        $this->assertTrue(NetworkHelper::ipInCidr(
            "192.0.2.1",
            ["2001:db8::/32", "192.0.2.0/24"]
        ));

        // IPv4 IP should not match if only IPv6 CIDRs are provided
        $this->assertFalse(NetworkHelper::ipInCidr(
            "192.0.2.1",
            ["2001:db8::/32"]
        ));
    }

    public function testIpInCidr_zeroPrefixMatchesAll(): void
    {
        $this->assertTrue(NetworkHelper::ipInCidr("203.0.113.5", ["0.0.0.0/0"]));
        $this->assertTrue(NetworkHelper::ipInCidr("2001:db8::1234", ["::/0"]));
    }

    public function testIpInCidr_ignoresInvalidCidrEntriesAndStillMatchesValidOne(): void
    {
        $cidrs = [
            "bad",
            "192.168.1.0",          // missing prefix
            "10.0.0.0/33",          // prefix too large
            "172.16.0.0/abc",       // non-numeric prefix
            " 192.168.1.0/24",      // leading space in net, will be invalid
            "172.16.0.0/12",        // valid, should cause match
        ];

        $this->assertTrue(NetworkHelper::ipInCidr("172.16.5.10", $cidrs));
    }

    public function testIpInCidr_partialBytePrefixIPv4(): void
    {
        // /9: 10.128.0.0 - 10.255.255.255
        $this->assertTrue(NetworkHelper::ipInCidr("10.128.0.1", ["10.128.0.0/9"]));
        $this->assertFalse(NetworkHelper::ipInCidr("10.127.255.255", ["10.128.0.0/9"]));

        // /13: 172.16.0.0 - 172.23.255.255
        $this->assertTrue(NetworkHelper::ipInCidr("172.20.10.5", ["172.16.0.0/13"]));
        $this->assertFalse(NetworkHelper::ipInCidr("172.24.0.1", ["172.16.0.0/13"]));
    }

    public function testIpInCidr_partialBitPrefixIPv6_65Bit(): void
    {
        // /65: first 65 bits fixed; 2001:db8:: has 65th bit = 0
        $this->assertTrue(NetworkHelper::ipInCidr("2001:db8:0:0:7fff::1", ["2001:db8::/65"]));
        $this->assertFalse(NetworkHelper::ipInCidr("2001:db8:0:0:8000::1", ["2001:db8::/65"]));
    }

    public function testIpInCidr_whitespaceInCidrEntriesMakesThemInvalid(): void
    {
        // Entry with spaces should be ignored (no implicit trimming)
        $this->assertFalse(NetworkHelper::ipInCidr("192.168.1.10", [" 192.168.1.0/24 "]));

        // A valid, trimmed entry should match
        $this->assertTrue(NetworkHelper::ipInCidr("192.168.1.10", [" 192.168.1.0/24 ", "192.168.1.0/24"]));
    }

    public function testIpInCidr_ipWithWhitespaceIsInvalid(): void
    {
        $this->assertFalse(NetworkHelper::ipInCidr(" 203.0.113.5", ["203.0.113.0/24"]));
        $this->assertFalse(NetworkHelper::ipInCidr("2001:db8::1234 ", ["2001:db8::/32"]));
    }

    public function testIpInCidr_ignoresOutOfRangePrefixValues(): void
    {
        // IPv4: /33 invalid and ignored
        $this->assertFalse(NetworkHelper::ipInCidr("10.0.0.1", ["10.0.0.0/33"]));

        // IPv6: /129 invalid and ignored
        $this->assertFalse(NetworkHelper::ipInCidr("2001:db8::1", ["2001:db8::/129"]));

        // Mixed with a valid entry should still match
        $this->assertTrue(NetworkHelper::ipInCidr("10.0.0.1", ["10.0.0.0/33", "10.0.0.0/8"]));
    }

    public function testValidHostnameBasic(): void
    {
        self::assertTrue(NetworkHelper::isValidHostname("example.com"));
        self::assertTrue(NetworkHelper::isValidHostname("sub.example.co"));
        self::assertTrue(NetworkHelper::isValidHostname("a-b.example.com"));
        self::assertTrue(NetworkHelper::isValidHostname("123.com"));
    }

    public function testValidHostnameWithTrailingDot(): void
    {
        self::assertTrue(NetworkHelper::isValidHostname("example.com."));
    }

    public function testValidHostnameWithPunycodeTld(): void
    {
        self::assertTrue(NetworkHelper::isValidHostname("example.xn--p1ai"));
    }

    public function testSingleLabelNotAllowedByDefault(): void
    {
        self::assertFalse(NetworkHelper::isValidHostname("localhost"));
        self::assertFalse(NetworkHelper::isValidHostname("my-host"));
    }

    public function testSingleLabelAllowedWhenFlagSet(): void
    {
        self::assertTrue(NetworkHelper::isValidHostname("localhost", false, true));
        self::assertTrue(NetworkHelper::isValidHostname("my-host", false, true));
    }

    public function testIpNotAllowedByDefault(): void
    {
        self::assertFalse(NetworkHelper::isValidHostname("127.0.0.1"));
        self::assertFalse(NetworkHelper::isValidHostname("2001:db8::1"));
    }

    public function testIpAllowedWhenFlagSet(): void
    {
        self::assertTrue(NetworkHelper::isValidHostname("127.0.0.1", true));
        self::assertTrue(NetworkHelper::isValidHostname("2001:db8::1", true));
    }

    public function testInvalidWhenLabelStartsOrEndsWithHyphen(): void
    {
        self::assertFalse(NetworkHelper::isValidHostname("-example.com"));
        self::assertFalse(NetworkHelper::isValidHostname("example-.com"));
        self::assertFalse(NetworkHelper::isValidHostname("sub.-host.example"));
        self::assertFalse(NetworkHelper::isValidHostname("sub.host-.example"));
    }

    public function testInvalidWhenLabelContainsUnderscore(): void
    {
        self::assertFalse(NetworkHelper::isValidHostname("ex_ample.com"));
        self::assertFalse(NetworkHelper::isValidHostname("sub._srv.example"));
    }

    public function testLabelLengthBoundaries(): void
    {
        $label63 = str_repeat("a", 63);
        $label64 = str_repeat("a", 64);

        self::assertTrue(NetworkHelper::isValidHostname($label63 . ".com"));
        self::assertFalse(NetworkHelper::isValidHostname($label64 . ".com"));
    }

    public function testHostnameTotalLengthBoundary(): void
    {
        // Build a hostname longer than 253 chars to ensure it fails.
        // "a." repeated 127 times is 254 chars, plus "a" makes 255.
        $tooLong = str_repeat("a.", 127) . "a";
        self::assertFalse(NetworkHelper::isValidHostname($tooLong));
    }

    public function testEmptyAndWhitespace(): void
    {
        self::assertFalse(NetworkHelper::isValidHostname(""));
        self::assertFalse(NetworkHelper::isValidHostname(" "));
    }
}