<?php

namespace Kollus\Component\Container;

/**
 * Class ContainerArray
 * @package Kollus\Component\Container
 */
class ContainerArray extends \ArrayIterator
{
    /**
     * @param mixed $element
     */
    public function appendElement($element)
    {
        $this->append($element);
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->getArrayCopy(), true);

        if ($key === false) {
            return false;
        }

        $this->offsetUnset($key);

        return true;
    }
}
