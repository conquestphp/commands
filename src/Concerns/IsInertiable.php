<?php

namespace Conquest\Assemble\Concerns;

trait IsInertiable
{
    public $modalMethods = [
        'Delete'
    ];

    public $pageMethods = [
        'Index',
        'Show',
        'Create',
        'Edit',
    ];

    public $nonInertiableMethods = [
        'Store',
        'Update',
        'Destroy',
    ];

    public $formMethods = [
        'Edit',
        'Create',
    ];

    /**
     * Check whether the method renders a page by default
     * 
     * @param string $method
     * @return bool
     */
    public function isPage($method)
    {
        return in_array($method, $this->pageMethods);
    }

    /**
     * Check whether the method renders a modal by default
     * 
     * @param string $method
     * @return bool
     */
    public function isModal($method)
    {
        return in_array($method, $this->modalMethods);
    }

    /**
     * Check whether the method renders a page or modal by default
     * 
     * @param string $method
     * @return bool
        */
    public function isInertiable($method)
    {
        return $this->isPage($method) || $this->isModal($method);
    }

    /**
     * Check whether the method renders a form by default
     * 
     * @param string $method
     * @return bool
     */
    public function isForm($method)
    {
        return in_array($method, $this->formMethods);
    }

    /**
     * Checks whether the method cannot generate a javascript resource file
     * 
     * @param string $method
     * @return bool
     */
    public function isNotInertiable($method)
    {
        return in_array($method, $this->nonInertiableMethods);
    }
}