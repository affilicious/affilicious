<?php
namespace Affilicious\Product\Update\Configuration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Configuration implements Configuration_Interface
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
        $this->config = wp_parse_args($defaults, array(
            self::UPDATE_INTERVAL => self::DEFAULT_UPDATE_INTERVAL,
            self::MIN_TASKS => self::DEFAULT_MIN_TASKS,
            self::MAX_TASKS => self::DEFAULT_MAX_TASKS,
        ));
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
