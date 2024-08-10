<?php

namespace Conquest\Assemble\Concerns;

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
     * @param  string  $method
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
     * @param  string  $class
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
            $class = preg_replace('/'.$method.'$/', '', $class);
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
        return match (str($method)->lower()->toString()) {
            'store' => 'post',
            'update' => 'patch',
            'destroy' => 'delete',
            default => 'get',
        };
    }

    /**
     * Get the base name of a pure class name
     *
     * @return string
     */
    public function getBase(string $name)
    {
        return last(explode('/', $name));
    }

    /**
     * Check whether the method renders a page by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasPage($method)
    {
        return in_array(
            strtolower($method),
            collect([
                'Index',
                'Show',
                'Create',
                'Edit',
            ])->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Check whether the method renders a modal by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasModal($method)
    {
        return in_array(
            strtolower($method),
            collect([
                'Delete',
            ])->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Check whether the method renders a form by default
     *
     * @param  string  $method
     * @return bool
     */
    public function hasForm($method)
    {
        return in_array(
            strtolower($method),
            collect([
                'Edit',
                'Create',
            ])->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Checks whether the method cannot generate a javascript resource file, or cannot change
     *
     * @param  string  $method
     * @return bool
     */
    public function isResourceless($method)
    {
        return in_array(
            strtolower($method),
            collect([
                'Store',
                'Update',
                'Destroy',
            ])->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Check whether the method can be scoped to a model
     *
     * @param  string  $method
     * @return bool
     */
    public function isScoped($method)
    {
        return in_array(
            strtolower($method),
            collect([
                'Show',
                'Edit',
                'Update',
                'Delete',
                'Destroy',
            ])->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }
}
