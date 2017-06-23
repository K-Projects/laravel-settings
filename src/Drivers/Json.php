<?php
namespace IonutMilica\LaravelSettings\Drivers;

use IonutMilica\LaravelSettings\DriverContract;

class Json implements DriverContract
{
    /**
     * @var
     */
    protected $filePath;

    /**
     * @var null
     */
    private $scope = 'default';

    /**
     * Json constructor.
     *
     * @param $filePath
     * @param null $scope
     */
    public function __construct($filePath, $scope = null)
    {
        $this->filePath = $filePath;
        $this->scope = $scope ?? $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data = [], array $dirt = [])
    {
        if ($dirt != null) {
            file_put_contents($this->filePath, json_encode($data));
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $data = [];

        if (is_file($this->filePath)) {
            $data = json_decode(file_get_contents($this->filePath), true);
        }

        return $data[$this->getScope()];
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
}
