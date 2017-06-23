<?php

namespace IonutMilica\LaravelSettings\Drivers;

use Illuminate\Database\DatabaseManager;
use IonutMilica\LaravelSettings\Arr;
use IonutMilica\LaravelSettings\DriverContract;

class Database implements DriverContract
{
    /**
     * Sql table used to store the settings
     *
     * @var string
     */
    protected $table = 'laravel_settings';

    /**
     * @var DatabaseManager
     */
    protected $database;

    /**
     * The default scope of the settings.
     *
     * @var string
     */
    protected $scope = 'default';

    /**
     * Database constructor.
     *
     * @param DatabaseManager $database
     * @param string|null $table
     * @param string|null $scope
     */
    public function __construct(DatabaseManager $database, $table = null, $scope = null)
    {
        $this->database = $database;
        $this->table = $table ?? $this->table;
        $this->scope = $scope ?? $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $data = [];
        $keys = [];

        $settings = $this->database->table($this->table)->select('*')->where('scope', $this->scope)->get();

        foreach ($settings as $setting) {
            $this->createArr($setting, $keys, $data);
        }

        $defaultSettings = $this->database->table($this->table)->select('*')->whereNotIn('key', $keys)->where('scope', 'default')->get();

        foreach ($defaultSettings as $setting) {
            $this->createArr($setting, $keys, $data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $settings = [], array $dirt = [])
    {
        foreach ($dirt as $scopeKey => $scopes) {
            foreach ($scopes as $key =>  $scope) {
                switch ($scope['type']) {
                    case 'created':
                        $this->createSetting($key, $scopeKey, $scope['value']);
                        break;
                    case 'updated':
                        $this->updateSetting($key, $scopeKey, $scope['value']);
                        break;
                    case 'deleted':
                        $this->deleteSetting($key, $scopeKey);
                        break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Create setting
     *
     * @param  string       $key
     * @param  string|array $value
     * @return mixed
     */
    protected function createSetting($key, $scope, $value)
    {
        $value = is_array($value) ? json_encode($value) : $value;

        return $this->getTableObject()->insert([
            'key'   => $key,
            'value' => $value,
            'scope' => $scope,
        ]);
    }

    /**
     * Update setting
     *
     * @param  string       $key
     * @param  string|array $value
     * @return mixed
     */
    protected function updateSetting($key, $scope, $value)
    {
        $value = is_array($value) ? json_encode($value) : $value;

        return $this->getTableObject()
            ->where('key', $key)
            ->where('scope', $scope)
            ->update([
                'value' => $value
            ]);
    }

    /**
     * Delete setting from the database
     *
     * @param  string $key
     * @return mixed
     */
    public function deleteSetting($key, $scope)
    {
        return $this->getTableObject()
            ->where([
                'key'   => $key,
                'scope' => $scope,
            ])
            ->delete();
    }

    /**
     * Get the object for the current table
     *
     * @return mixed
     */
    private function getTableObject()
    {
        return $this->database->table($this->table);
    }

    /**
     * @param $setting
     * @param $keys
     * @param $data
     */
    private function createArr($setting, &$keys, &$data)
    {
        $key = $setting->key;
        $keys[] = $key;
        $value = $setting->value;

        $decoded = json_decode($value, 1, 512);
        if (is_array($decoded)) {
            $value = $decoded;
        }

        Arr::set($data, $key, $value, $this->scope);
    }

}
