<?php

namespace Conquest\Assemble\Concerns;

trait HasNames
{
    public const METHODS = [
        'Index',
        'Show',
        'Create',
        'Store',
        'Edit',
        'Update',
        'Delete',
        'Destroy'
    ];

     /**
     * Parse the name of the class
     *
     * @param  string  $name
     * @return array{string, ?string}
     */
    public function parseName($name)
    {
        // Split about the final /
        $system = explode('/', $name);
        $fileName = array_pop($system);

        // Split camel case
        $words = preg_split('/(?=[A-Z])/', $fileName, -1, PREG_SPLIT_NO_EMPTY);
        $finalWord = array_pop($words);

        // Reconstruct the path including the final word
        $name = implode('/', $system);
        $name .= '/' . implode('', $words);

        // Add back the final word to the name if it is not a method
        if (!in_array($finalWord, self::METHODS)) {
            $name .= $finalWord;
        }

        // Check if the final word is in methods, remove it as give it as the type
        $method = in_array($finalWord, self::METHODS) ? $finalWord : null;
        return [$name, $method];
    }

    /**
     * Get the name of the class including the method
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getFullName($name, $method = null)
    {
        return $name . ($method ? $method : '');
    }

    /**
     * Get the name of the class without the namespace
     *
     * @param  string  $name
     * @return string
     */
    public function getClassName($name)
    {
        $name = str_replace($this->getNamespace($name).'\\', '', $name);
        $name = str_replace('\\', '/', $name);
        $name = explode('/', $name);
        $name = array_pop($name);
        return $name;
    }

    /**
     * Get the name of the request class
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getRequest($name, $method)
    {
        return $this->getFullName($name, $method) . 'Request';
    }

    /**
     * Get the namespace of the request class
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getRequestNamespace($name, $method)
    {
        return $this->getNamespace($name) . 'Http\\Requests\\' . $this->getRequest($name, $method);
    }

    /**
     * Get the name of the controller class
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getController($name, $method)
    {
        return $this->getFullName($name, $method) . 'Controller';
    }

    /**
     * Get the namespace of the controller class
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getControllerNamespace($name, $method)
    {
        return $this->getNamespace($name) . 'Http\\Controllers\\' . $this->getController($name, $method);
    }

    /**
     * Get the name of the model class
     *
     * @param  string  $name
     * @return string
     */
    public function getModel($name)
    {
        return $this->getClassName($name);
    }

    /**
     * Get the namespace of the model class
     *
     * @param  string  $name
     * @return string
     */
    public function getModelNamespace($name)
    {
        return $this->getNamespace($name) . 'Models\\' . $this->getModel($name);
    }

    /**
     * Get the name of the javascript resource class referenced by Inertia
     * 
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    public function getResource($name, $method)
    {
        return $this->getFullName($name, $method);
    }





}