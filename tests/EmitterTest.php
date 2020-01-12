<?php

namespace Bag2\Cookie;

final class EmitterTest extends TestCase
{
    /** @var Emitter */
    protected $subject;
    /** @var ?array{name:string,value:string,options:array} */
    public $receive;

    public function setUp(): void
    {
        $this->subject = new class($this) implements Emitter {
            /** @var EmitterTest */
            private $case;

            public function __construct(EmitterTest $case)
            {
                $this->case = $case;
            }

            public function __invoke(
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

        $this->assertTrue($subject('name', 'val', ['expires' => 0]));
        $this->assertSame($expected, $this->receive);
    }
}
