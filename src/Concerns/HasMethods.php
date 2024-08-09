<?php

namespace Conquest\Assemble\Concerns;

use Illuminate\Support\Str;

trait HasMethods
{
    public const METHODS = [
        'Index',
        'Show',
        'Create',
        'Store',
        'Edit',
        'Update',
        'Delete',
        'Destroy',
    ];

    public const MODAL_METHODS = [
        'Delete',
    ];

    public const PAGE_METHODS = [
        'Index',
        'Show',
        'Create',
        'Edit',
    ];

    public const RESOURCELESS_METHODS = [
        'Store',
        'Update',
        'Destroy',
    ];

    public const FORM_METHODS = [
        'Edit',
        'Create',
    ];

    /**
     * Check if the method is valid
     * 
     * @param string $method
     * @return bool
     */
    public function isValidMethod($method)
    {
        return in_array($method, self::METHODS);
    }

    /**
     * Get the method name from the class name, if it exists and is valid
     * 
     * @param string $class
     * @return string|null
     */
    public function getMethodName($class)
    {
        $parts = explode('/', $class);
        $methodName = end($parts);
        
        preg_match('/[A-Z][a-z]+$/', $methodName, $matches);
        
        if ($this->isValidMethod($matches[0])) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Get the HTTP method for the route.
     * 
     * @param  string  $method
     * @return string
     */
    public function getHttpMethod($method)
    {
        return match (Str::lower($method)) {
            'store' => 'post',
            'update' => 'patch',
            'destroy' => 'delete',
            default => 'get',
        };
    }

    /**
     * Check whether the method renders a page by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasPage($method)
    {
        return in_array($method, self::PAGE_METHODS);
    }

    /**
     * Check whether the method renders a modal by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasModal($method)
    {
        return in_array($method, self::MODAL_METHODS);
    }

    /**
     * Check whether the method renders a form by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasForm($method)
    {
        return in_array($method, self::MODAL_METHODS);
    }

     /**
     * Checks whether the method cannot generate a javascript resource file
     *
     * @param  string  $method
     * @return bool
     */
    public function isResourceless($method)
    {
        return in_array($method, self::RESOURCELESS_METHODS);
    }
}