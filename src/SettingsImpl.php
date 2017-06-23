<?php
namespace IonutMilica\LaravelSettings;

use Illuminate\Contracts\Config\Repository as ConfigContract;

class SettingsImpl implements SettingsContract
{
    /**
     * Driver used to load and store the settings
     *
     * @var DriverContract
     */
    protected $driver;

    /**
     * Temporary storage for the settings
     *
     * @var array
     */
    protected $settings = null;

    /**
     * Changes made by set, forgot
     *
     * @var null|array
     */
    protected $dirt = null;

    /**
     * @var ConfigContract
     */
    protected $config;

    /**
     * SettingsImpl constructor.
     *
     * @param DriverContract $driver
     * @param ConfigContract $config
     */
    public function __construct(DriverContract $driver, ConfigContract $config)
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    protected function load()
    {
        if ($this->settings == null) {
            $this->settings = $this->driver->load();
        }
    }

    /**
     * Get setting by key
     *
     * @param  string $key
     * @param  string|null $default
     * @param  bool $save
     * @return mixed
     */
    public function get($key, $default = null, $save = false)
    {
        $this->load();

        if ($this->has($key)) {
            return Arr::get($this->settings, $key, $default);
        }

        if ($save) {
            $this->set($key, $default);
        }

        if ($this->config->has($key)) {
            return $this->config->get($key);
        }

        $defaultSettings = isset($default['default']) ? $default['default'] : [];
        $scopedSettings  = isset($default[$this->driver->getScope()]) ? $default[$this->driver->getScope()] : [];

        return array_merge($defaultSettings,$scopedSettings);
    }

    /**
     * Update setting
     *
     * @param  string $key
     * @param  string $value
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->load();

        if ($this->has($this->driver->getScope() . '.' . $key) && $this->get($this->driver->getScope() . '.' . $key) != $value) {
            $this->dirt[$this->driver->getScope()][$key] = [
                'type' => 'updated',
                'value' => $value
            ];
        }

        if ( ! $this->has($this->driver->getScope() . '.' . $key)) {
            $this->dirt[$this->driver->getScope()][$key] = [
                'type' => 'created',
                'value' => $value,
            ];
        }

        Arr::set($this->settings, $key, $value, $this->driver->getScope());

        return $value;
    }

    /**
     * Forget setting
     *
     * @param string $key
     */
    public function forget($key)
    {
        $this->load();

        if ($this->has($key)) {
            $this->dirt[$key] = ['type' => 'deleted'];
            Arr::forget($this->settings, $key);
        }
    }

    /**
     * Check if setting key exists
     *
     * @param  string $key
     * @return mixed
     */
    public function has($key)
    {
        $this->load();

        return Arr::has($this->settings, $key);
    }

    /**
     * Get all stored settings
     *
     * @return mixed
     */
    public function all()
    {
        $this->load();

        return $this->settings;
    }

    /**
     * Save dirty data into the data source
     *
     * @return bool
     */
    public function save()
    {
        if ($this->dirt) {
            return $this->driver->save($this->settings, $this->dirt);
        }

        return false;
    }

    /**
     * Check if setting exist via isset($settings['something']))
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get setting via []
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Add/Edit a setting
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @throws InvalidArrayAssignment
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new InvalidArrayAssignment();
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * Delete setting
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    public function getDriver()
    {
        return $this->driver;
    }

}

class InvalidArrayAssignment extends \Exception {}
