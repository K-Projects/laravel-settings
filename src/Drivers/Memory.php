<?php

namespace Bitempest\LaravelSettings\Drivers;

use Bitempest\LaravelSettings\Contracts\SettingsContract;
use Illuminate\Support\Arr;

class Memory implements SettingsContract
{

    protected $data = [];

    /**
     * Get setting by key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Update setting
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        return Arr::set($this->data, $key, $value);
    }

    /**
     * Get all stored settings
     *
     * @return mixed
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Check if setting key exists
     *
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Save dirty data into the data source
     *
     * @return mixed
     */
    public function save()
    {
        //
    }
}