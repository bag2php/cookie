<?php

namespace Bag2\Cookie;

/**
 * @phpstan-import-type options from CookieEmitter
 */
final class CookieEmitterTest extends TestCase
{
    /** @var ?CookieEmitter */
    protected $subject;
    /**
     * @var ?array{name:string,value:string,options:array}
     * @phpstan-var ?array{name:non-empty-string,value:string,options:options}
     */
    public $receive;

    public function setUp(): void
    {
        $this->subject = new class ($this) implements CookieEmitter {
            /** @var CookieEmitterTest */
            private $case;

            public function __construct(CookieEmitterTest $case)
            {
                $this->case = $case;
            }

            public function emitCookie(
                string $name,
                string $value,
                array $options
            ): bool {
                $this->case->receive = [
                    'name' => $name,
                    'value' => $value,
                    'options' => $options,
                ];

                return true;
            }
        };
    }

    public function test(): void
    {
        $expected = [
            'name' => 'name',
            'value' => 'val',
            'options' => ['expires' => 0],
        ];
        $subject = $this->subject;
        assert($subject !== null);

        $this->assertTrue($subject->emitCookie('name', 'val', ['expires' => 0]));
        $this->assertSame($expected, $this->receive);
    }
}
