<?php
declare(strict_types=1);
namespace AawTeam\Minipoll\ViewModel;

/*
 * Copyright 2017 Agentur am Wasser | Maeder & Partner AG
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * AbstractViewModel
 */
abstract class AbstractViewModel implements \ArrayAccess
{
    /**
     * @var \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject
     */
    protected $domainModel;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param AbstractDomainObject $domainObject
     * @param array $options
     * @return \AawTeam\Minipoll\ViewModel\AbstractViewModel
     */
    public static function createFromDomainModel(AbstractDomainObject $domainObject, array $options = null)
    {
        $instance = new static($options);
        $instance->setDomainModel($domainObject);
        return $instance;
    }

    /**
     * @param array $options
     * @return void
     */
    protected function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string|mixed $key
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param AbstractDomainObject $domainModel
     */
    public function setDomainModel(AbstractDomainObject $domainModel)
    {
        $this->domainModel = $domainModel;
    }

    /**
     * @return \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject
     */
    public function getDomainModel()
    {
        return $this->domainModel;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @throws \RuntimeException
     * @return mixed|null
     */
    public function __call($name, array $arguments)
    {
        if (\strpos($name, 'get') === 0) {
            return $this->offsetGet(\lcfirst(\substr($name, 3)));
        }
        throw new \RuntimeException('Method "' . \htmlspecialchars($name) . '" dos not exist or is not accessible');
    }

    /**
     * @param string|mixed $offset
     * @return boolean
     */
    protected function optionExists($offset)
    {
        return isset($this->options[$offset]);
    }

    /**
     * @param string|mixed $offset
     * @return boolean
     */
    protected function propertyExistsInDomainModel($offset)
    {
        return $this->domainModel->_hasProperty($offset);
    }

    /**
     * @param string|mixed $offset
     * @return boolean
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return $this->optionExists($offset) || $this->propertyExistsInDomainModel($offset);
    }

    /**
     * @param string|mixed $offset
     * @return null|mixed
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        if ($this->optionExists($offset)) {
            return $this->options[$offset];
        } elseif ($this->propertyExistsInDomainModel($offset)) {
            return $this->domainModel->_getProperty($offset);
        }
        return null;
    }

    /**
     * @param string|mixed $offset
     * @param mixed $value
     * @return void
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->options[$offset] = $value;
    }

    /**
     * @param string|mixed $offset
     * @return void
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        if ($this->optionExists($offset)) {
            unset($this->options[$offset]);
        }
    }
}
