<?php
namespace IonutMilica\LaravelSettings\Drivers;

use IonutMilica\LaravelSettings\DriverContract;

class Memory implements DriverContract
{
    /**
     * Load the data in memory
     */
    public function load()
    {
        return [];
    }

    /**
     * Save dirty data into the data source
     *
     * @param array $settings
     * @param array $dirt
     * @return mixed
     */
    public function save(array $settings = [], array $dirt = [])
    {
        //
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        // TODO: Implement setScope() method.
    }

    /**
     * @return string
     */
    public function getScope()
    {
        // TODO: Implement getScope() method.
    }
}
