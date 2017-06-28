<?php

namespace Kollus\Component\Container;

class ProgressBlock extends AbstractContainer
{
    /**
     * @var boolean
     */
    private $is_read_block;

    /**
     * @var integer
     */
    private $time;

    /**
     * @var integer
     */
    private $percent;

    /**
     * ProgressBlock constructor.
     */
    public function __construct($item = [])
    {
        if (isset($item['is_read_block'])) {
            $this->is_read_block = $item['is_read_block'];
        }
        if (isset($item['time'])) {
            $this->time = $item['time'];
        }
        if (isset($item['percent'])) {
            $this->percent = $item['percent'];
        }
    }

    /**
     * @return bool
     */
    public function isReadBlock()
    {
        return $this->is_read_block;
    }

    /**
     * @param bool $is_read_block
     */
    public function setIsReadBlock($is_read_block)
    {
        $this->is_read_block = $is_read_block;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }

    public static function importFromBlockInfo($blockInfo)
    {
        $progressBlocks = [];
        if (isset($blockInfo->block_count) && isset($blockInfo->blocks)) {
            for ($i = 0; $i < $blockInfo->block_count; $i++) {
                $progressBlocks[$i] = new static([
                    'is_read_block' => isset($blockInfo->blocks->{'b'.$i}) ? (bool)$blockInfo->blocks->{'b'.$i} : false,
                    'time' => isset($blockInfo->blocks->{'t'.$i}) ? (int)$blockInfo->blocks->{'t'.$i} : 0,
                    'percent' => isset($blockInfo->blocks->{'p'.$i}) ? (int)$blockInfo->blocks->{'p'.$i} : 0,
                ]);
            }
        }

        return $progressBlocks;
    }
}
