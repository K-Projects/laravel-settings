<?php

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if ( ! function_exists('settings')) {

    /**
     * Get setting from the storage
     *
     * @param  string|null $name
     * @param  string|null $default
     * @param  bool $save
     * @return \Illuminate\Foundation\Application|mixed
     */
    function settings($name = null, $default = null, $save = false)
    {
        /** @var \IonutMilica\LaravelSettings\SettingsContract $settings */
        $settings = app(\IonutMilica\LaravelSettings\SettingsContract::class);

        if ( ! $name) {
            return $settings->all();
        }

        return $settings->get($name, $default, $save);
    }
}

if ( ! function_exists('settingImpl')) {

    /**
     * Get setting from the storage
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    function settingImpl()
    {
        return app(\IonutMilica\LaravelSettings\SettingsContract::class);
    }
}

if ( ! function_exists('setting')) {

    /**
     * Get setting from the storage
     *
     * @param  string|null $name
     * @param  string|null $default
     * @param  bool $save
     * @return \Illuminate\Foundation\Application|mixed
     */
    function setting($name = null, $default = null, $save = false)
    {
        $setting = app(\IonutMilica\LaravelSettings\SettingsContract::class);

        if ( ! $name) {
            return $setting->all();
        }

        return $setting->get($name, $default, $save);
    }
}

if ( ! function_exists('setting_set')) {

    /**
     * Create or update a new setting
     *
     * @param  string $name
     * @param  string $value
     * @return \Illuminate\Foundation\Application|mixed
     */
    function setting_set($name, $value)
    {
        return app(\IonutMilica\LaravelSettings\SettingsContract::class)
                ->set($name, $value);
    }
}

if ( ! function_exists('setting_forget')) {

    /**
     * Forget a setting
     *
     * @param  string $name
     * @return \Illuminate\Foundation\Application|mixed
     */
    function setting_forget($name)
    {
        return app(\IonutMilica\LaravelSettings\SettingsContract::class)
            ->forget($name);
    }
}
