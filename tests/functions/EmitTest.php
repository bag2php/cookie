<?php

namespace Bag2\Cookie\functions;

use Badoo\SoftMocks;
use Bag2\Cookie\Bag;
use Bag2\Cookie\Emitter;

final class EmitTest extends \Bag2\Cookie\TestCase
{
    /** @var array<array{name:string,value:string,options:array}> */
    public $received;
    /** @var Emitter */
    protected $subject;

    public function setUp()
    {
        $this->received = [];

        $emitter = new class($this) implements Emitter {
            /** @var EmitTest */
            private $case;

            public function __construct(EmitTest $case)
            {
                $this->case = $case;
            }

            public function __invoke(
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
            'Bag2\Cookie\create_emitter', '',
            static function () use ($emitter) {
                return $emitter;
            }
        );
    }

    /**
     * @dataProvider cookieProvider
     * @param class-string $expected
     */
    public function test(Bag $bag, array $expected): void
    {
        $actual = \Bag2\Cookie\emit($bag);

        $this->assertSame($expected['result'], $actual);
        $this->assertSame($expected['received'], $this->received);
    }

    /**
     * @return array<string,array{0:int,1:class-string}>
     */
    public function cookieProvider()
    {
        return [
            [
                new Bag(),
                [
                    'result' => true,
                    'received' => [],
                ]
            ],
            [
                (new Bag())->add('A', 'v'),
                [
                    'result' => true,
                    'received' => [
                        [
                            'name' => 'A',
                            'value' => 'v',
                            'options' => [],
                        ]
                    ],
                ]
            ],
        ];
    }
}
