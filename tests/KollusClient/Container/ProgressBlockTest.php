<?php

namespace KollusClient\Container;

use Kollus\Component\Container;

class ProgressBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstProgressBlock = new Container\ProgressBlock();
        $this->assertInstanceOf(Container\ProgressBlock::class, $firstProgressBlock);

        $testIsReadBlock = false;
        $testTime = 10;
        $testPercent = 100;
        $secondProgressBlock = new Container\ProgressBlock([
            'is_read_block' => $testIsReadBlock,
            'time' => $testTime,
            'percent' => $testPercent,
        ]);
        $this->assertEquals($testIsReadBlock, $secondProgressBlock->isReadBlock());
        $this->assertEquals($testTime, $secondProgressBlock->getTime());
        $this->assertEquals($testPercent, $secondProgressBlock->getPercent());

        $secondProgressBlock->setIsReadBlock(true);
        $secondProgressBlock->setTime(20);
        $secondProgressBlock->setPercent(50);

        $this->assertEquals(true, $secondProgressBlock->isReadBlock());
        $this->assertEquals(20, $secondProgressBlock->getTime());
        $this->assertEquals(50, $secondProgressBlock->getPercent());
    }

    public function testImportFromBlockInfo()
    {
        $testBlockInfo = (object)[
            'block_count' => 2,
            'blocks' => (object)[
                't0' => 10,
                'b0' => 1,
                'p0' => 50,
                't1' => 0,
                'b1' => 0,
                'p1' => 0,
            ],
        ];

        $progressBlocks = Container\ProgressBlock::importFromBlockInfo($testBlockInfo);
        /** @var Container\ProgressBlock[] $progressBlocks */
        $firstProgressBlock = $progressBlocks[0];
        $secondProgressBlock = $progressBlocks[1];

        $this->assertInstanceOf(Container\ProgressBlock::class, $firstProgressBlock);
        $this->assertInstanceOf(Container\ProgressBlock::class, $secondProgressBlock);
        $this->assertEquals((bool)$testBlockInfo->blocks->b0, $firstProgressBlock->isReadBlock());
        $this->assertEquals($testBlockInfo->blocks->t0, $firstProgressBlock->getTime());
        $this->assertEquals($testBlockInfo->blocks->p0, $firstProgressBlock->getPercent());
        $this->assertEquals((bool)$testBlockInfo->blocks->b1, $secondProgressBlock->isReadBlock());
        $this->assertEquals($testBlockInfo->blocks->t1, $secondProgressBlock->getTime());
        $this->assertEquals($testBlockInfo->blocks->p1, $secondProgressBlock->getPercent());
    }
}
