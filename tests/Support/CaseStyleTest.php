<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Tests\Support;

use Charcoal\Base\Support\CaseStyle;
use Charcoal\Base\Support\CaseStyleHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CaseStyleTest extends TestCase
{
    #[DataProvider("provideEnumConversions")]
    public function testEnumFrom(string $input, CaseStyle $from, CaseStyle $to, string $expected): void
    {
        $this->assertSame($expected, $to->from($input, $from));
    }

    public static function provideEnumConversions(): array
    {
        return [
            // RAW → target
            ["hello world", CaseStyle::RAW, CaseStyle::SNAKE_CASE, "hello_world"],
            ["Hello--world 123", CaseStyle::RAW, CaseStyle::KEBAB_CASE, "hello-world-123"],
            ["XMLHttpRequest", CaseStyle::RAW, CaseStyle::CAMEL_CASE, "xmlHttpRequest"],
            ["URL42Parser", CaseStyle::RAW, CaseStyle::PASCAL_CASE, "Url42Parser"],
            ["already_snake_case", CaseStyle::RAW, CaseStyle::PASCAL_CASE, "AlreadySnakeCase"],
            ["already-kebab-case", CaseStyle::RAW, CaseStyle::CAMEL_CASE, "alreadyKebabCase"],

            // SNAKE → target
            ["already_snake_case", CaseStyle::SNAKE_CASE, CaseStyle::CAMEL_CASE, "alreadySnakeCase"],
            ["already_snake_case", CaseStyle::SNAKE_CASE, CaseStyle::PASCAL_CASE, "AlreadySnakeCase"],
            ["already_snake_case", CaseStyle::SNAKE_CASE, CaseStyle::KEBAB_CASE, "already-snake-case"],

            // KEBAB → target
            ["already-kebab-case", CaseStyle::KEBAB_CASE, CaseStyle::SNAKE_CASE, "already_kebab_case"],
            ["already-kebab-case", CaseStyle::KEBAB_CASE, CaseStyle::PASCAL_CASE, "AlreadyKebabCase"],
            ["already-kebab-case", CaseStyle::KEBAB_CASE, CaseStyle::CAMEL_CASE, "alreadyKebabCase"],

            // CAMEL → target
            ["alreadyCamelCase", CaseStyle::CAMEL_CASE, CaseStyle::SNAKE_CASE, "already_camel_case"],
            ["alreadyCamelCase", CaseStyle::CAMEL_CASE, CaseStyle::KEBAB_CASE, "already-camel-case"],
            ["alreadyCamelCase", CaseStyle::CAMEL_CASE, CaseStyle::PASCAL_CASE, "AlreadyCamelCase"],

            // PASCAL → target
            ["AlreadyPascalCase", CaseStyle::PASCAL_CASE, CaseStyle::SNAKE_CASE, "already_pascal_case"],
            ["AlreadyPascalCase", CaseStyle::PASCAL_CASE, CaseStyle::KEBAB_CASE, "already-pascal-case"],
            ["AlreadyPascalCase", CaseStyle::PASCAL_CASE, CaseStyle::CAMEL_CASE, "alreadyPascalCase"],

            // No-op RAW
            ["KeepAsIs", CaseStyle::RAW, CaseStyle::RAW, "KeepAsIs"],
        ];
    }

    public function testHelper_pascalCaseFromRaw(): void
    {
        $this->assertSame("HelloWorld123", CaseStyleHelper::pascalCaseFromRaw("  hello--world 123 "));
        $this->assertSame("XmlHttpRequest", CaseStyleHelper::pascalCaseFromRaw("XML_http-request"));
        $this->assertSame("", CaseStyleHelper::pascalCaseFromRaw("___---")); // no tokens
    }

    public function testHelper_camelCaseFromStyled(): void
    {
        $this->assertSame("alreadySnakeCase", CaseStyleHelper::camelCaseFromStyled("Already_Snake_Case"));
        $this->assertSame("alreadyKebabCase", CaseStyleHelper::camelCaseFromStyled("Already-Kebab-Case"));
        $this->assertSame("xmlHttpRequest", CaseStyleHelper::camelCaseFromStyled("XMLHttpRequest"));
        $this->assertSame("url42Parser", CaseStyleHelper::camelCaseFromStyled("URL42Parser"));
    }

    public function testHelper_delimiterCaseFromRaw(): void
    {
        $this->assertSame("hello_world_123", CaseStyleHelper::delimiterCaseFromRaw("Hello  World 123", "_"));
        $this->assertSame("hello-world-123", CaseStyleHelper::delimiterCaseFromRaw("Hello  World 123", "-"));
        $this->assertSame("", CaseStyleHelper::delimiterCaseFromRaw("", "_"));
    }

    public function testHelper_delimiterCaseFromStyled(): void
    {
        $this->assertSame("already_snake_case", CaseStyleHelper::delimiterCaseFromStyled("AlreadySnakeCase", "_"));
        $this->assertSame("already-kebab-case", CaseStyleHelper::delimiterCaseFromStyled("AlreadyKebabCase", "-"));
        $this->assertSame("xml_http_request", CaseStyleHelper::delimiterCaseFromStyled("XMLHttpRequest", "_"));
        $this->assertSame("url_42_parser", CaseStyleHelper::delimiterCaseFromStyled("URL42Parser", "_"));
    }

    public function testAsciiOnlyBehaviorOnUnicode(): void
    {
        // ASCII-only tokenization: non-ASCII letters are ignored in raw splitting
        $this->assertSame("LutK", CaseStyleHelper::pascalCaseFromRaw("Žlutý_kůň"));
        // Styled splitting keeps ASCII portions
        $this->assertSame("lutRequestId", CaseStyleHelper::camelCaseFromStyled("ŽlutýRequestID"));
    }
}
