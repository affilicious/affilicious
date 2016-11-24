<?php
namespace Affilicious\Product\Application\Update\Configuration;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Configuration_Context implements Configuration_Context_Interface
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(array $defaults = array())
    {
        $this->context = wp_parse_args($defaults, array(
            self::UPDATE_INTERVAL => self::DEFAULT_UPDATE_INTERVAL,
        ));
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has($key)
    {
        return isset($this->context[$key]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set($key, $value)
    {
        $this->context[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function delete($key)
    {
        unset($this->context[$key]);

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

        return $this->context[$key];
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_all()
    {
        return $this->context;
    }
}
