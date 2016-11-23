<?php
namespace Affilicious\Product\Application\Update\Worker;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Configuration_Resolver implements Configuration_Resolver_Interface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(array $defaults = array())
    {
        $this->config = $defaults;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has($key)
    {
        return isset($this->config[$key]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function delete($key)
    {
        unset($this->config[$key]);

        return $this;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get($key)
    {
        if(!$this->has($key)) {
            return null;
        }

        return $this->config[$key];
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_all()
    {
        return $this->config;
    }
}
