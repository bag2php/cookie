<?php

namespace Bag2\Cookie;

final class EmitterTest extends TestCase
{
    /** @var Emitter */
    protected $subject;

    public function setUp()
    {
        $this->subject = new class implements Emitter {
            /** @var array{name:string,value:string,options:array} */
            private $receive;

            public function __invoke(
                string $name,
                string $value,
                array $options
            ): bool {
                $this->receive = [
                    'name' => $name,
                    'value' => $value,
                    'options' => $options,
                ];

                return true;
            }


            public function getReceive(): ?array
            {
                return $this->receive;
            }
        };
    }

    public function test()
    {
        $expected = [
            'name' => 'name',
            'value' => 'val',
            'options' => ['expires' => 0],
        ];
        $subject = $this->subject;

        $this->assertTrue($subject('name', 'val', ['expires' => 0]));
        $this->assertSame($expected, $subject->getReceive());
    }
}
