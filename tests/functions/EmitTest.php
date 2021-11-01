<?php

namespace Bag2\Cookie\functions;

use Badoo\SoftMocks;
use Bag2\Cookie\Emitter;
use Bag2\Cookie\Oven;

/**
 *
 * @phpstan-import-type options from Emitter
 */
final class EmitTest extends \Bag2\Cookie\TestCase
{
    /**
     * @var ?array<array{name:string,value:string,options:array}>
     * @phpstan-var ?array<array{name:non-empty-string,value:string,options:options}>
     */
    public $received;
    /** @var ?Emitter */
    protected $subject;

    public function setUp(): void
    {
        $this->received = [];

        $emitter = new class ($this) implements Emitter {
            /** @var EmitTest */
            private $case;

            public function __construct(EmitTest $case)
            {
                $this->case = $case;
            }

            public function emitCookie(
                string $name,
                string $value,
                array $options
            ): bool {
                $this->case->received[] = [
                    'name' => $name,
                    'value' => $value,
                    'options' => $options,
                ];

                return true;
            }
        };
        $this->subject = $emitter;

        SoftMocks::redefineFunction(
            'Bag2\Cookie\create_emitter',
            '',
            static function () use ($emitter) {
                return $emitter;
            }
        );
    }

    /**
     * @dataProvider cookieProvider
     * @param array{result:bool,received:array<array>} $expected
     * @phpstan-param array{result:bool,received:list<options>} $expected
     */
    public function test(Oven $oven, array $expected): void
    {
        $actual = \Bag2\Cookie\emit($oven);

        $this->assertSame($expected['result'], $actual);
        $this->assertSame($expected['received'], $this->received);
    }

    /**
     * @return array<array{0:Oven,1:array{result:bool,received:array<array>}}>
     * @phpstan-return array<array{0:Oven,1:array{result:bool,received:list<options>}}>
     */
    public function cookieProvider()
    {
        return [
            [
                new Oven(),
                [
                    'result' => true,
                    'received' => [],
                ],
            ],
            [
                (new Oven())->add('A', 'v'),
                [
                    'result' => true,
                    'received' => [
                        [
                            'name' => 'A',
                            'value' => 'v',
                            'options' => [],
                        ],
                    ],
                ],
            ],
        ];
    }
}
