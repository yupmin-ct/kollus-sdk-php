<?php

namespace Kollus\Component\Container;

class Uservalues extends AbstractContainer
{
    /**
     * @var array
     */
    private $uservalues = [];

    /**
     * Uservalues constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        foreach ($items as $key => $value) {
            $this->uservalues[$key] = (string)$value;
        }
    }

    /**
     * @return array
     */
    public function getUservalues()
    {
        return $this->uservalues;
    }

    /**
     * @param array $uservalues
     */
    public function setUservalues($uservalues)
    {
        $this->uservalues = $uservalues;
    }

    /**
     * @param int|string $index
     * @return string|null
     */
    public function getUservalueAt($index)
    {
        return isset($this->uservalues['uservalue' . $index]) ? $this->uservalues['uservalue' . $index] : null;
    }
}
