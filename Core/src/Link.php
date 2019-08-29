<?php

namespace Croogo\Core;

use ArrayObject;
use Cake\Routing\Router;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Log\Log;
use Croogo\Core\Utility\StringConverter;

class Link extends ArrayObject
{

    public static function createFromLinkString($link)
    {
        $stringConverter = new StringConverter();

        return new Link($stringConverter->linkStringToArray($link));
    }

    public function __construct($url)
    {
        if (is_array($url)) {
            $this->exchangeArray($url);
        } elseif (is_string($url)) {
            $this->url = $url;
        }
    }

    public function getUrl()
    {
        $copy = array_map(function ($val) {
            if (is_array($val)) {
                return $val;
            }
            $decoded = urldecode($val);
            if (boolval($decoded) === false) {
                $decoded = false;
            }
            return $decoded;
        }, $this->getArrayCopy());
        unset($copy['pass']);
        return (isset($this->controller)) ? $copy : $this->url;
    }

    public function getPath()
    {
        try {
            return Router::url($this->getUrl());
        } catch (MissingRouteException $e) {
            Log::error('Croogo/Core.Link::getPath() cannot get url');
            Log::error($e->getMessage());
            return '/';
        }
    }

    public function toLinkString()
    {
        $stringConverter = new StringConverter();

        return $stringConverter->urlToLinkString($this->getArrayCopy());
    }

    public function __toString()
    {
        return (isset($this->controller)) ? $this->toLinkString() : $this->url;
    }

    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this[$name]);
    }
}
