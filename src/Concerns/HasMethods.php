<?php

namespace Conquest\Assemble\Concerns;

use Illuminate\Support\Str;

trait HasMethods
{
    public $methods = [
        'Index',
        'Show',
        'Create',
        'Store',
        'Edit',
        'Update',
        'Delete',
        'Destroy',
    ];

    /**
     * Check if the method is valid
     * 
     * @param string $method
     * @return bool
     */
    public function isValidMethod($method)
    {
        return in_array(
            strtolower($method),
            collect($this->methods)
                ->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Get the method name from the class name, if it exists and is valid
     * 
     * @param string $class
     * @return string|null
     */
    public function getMethodName($class)
    {
        $class = str_replace(['Controller', 'Request'], '', $class);
        $parts = explode('/', $class);
        $methodName = end($parts);
        
        preg_match('/[A-Z][a-z]+$/', $methodName, $matches);
        
        if ($this->isValidMethod($matches[0])) {
            return $matches[0];
        }

        return null;
    }

    public function getPureClassName($class)
    {
        $class = str_replace(['Controller', 'Request'], '', $class);
        foreach ($this->methods as $method) {
            $class = preg_replace('/' . $method . '$/', '', $class);
        }
        return $class;
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
        return in_array($method, [
            'Index',
            'Show',
            'Create',
            'Edit',
        ]);
    }

    /**
     * Check whether the method renders a modal by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasModal($method)
    {
        return in_array($method, [
            'Delete',
        ]);
    }

    /**
     * Check whether the method renders a form by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasForm($method)
    {
        return in_array($method, [
            'Edit',
            'Create',
        ]);
    }

     /**
     * Checks whether the method cannot generate a javascript resource file
     *
     * @param  string  $method
     * @return bool
     */
    public function isResourceless($method)
    {
        return in_array($method, [
            'Store',
            'Update',
            'Destroy',
        ]);
    }

    /**
     * Check whether the method can be scoped to a model
     * 
     * @param  string  $method
     * @return bool
     */
    public function isScoped($method)
    {
        return in_array($method, [
            'Show',
            'Edit',
            'Update',
            'Delete',
            'Destroy',
        ]);
    }
}