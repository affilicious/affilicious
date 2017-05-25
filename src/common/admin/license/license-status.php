<?php
namespace Affilicious\Common\Admin\License;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class License_Status
{
    const UNKNOWN = 'unknown';
    const MISSING = 'missing';
    const VALID = 'valid';
    const INVALID = 'invalid';
    const SUCCESS = 'success';
    const ERROR = 'error';

    /**
     * @var string
     */
    private $type;

    /**
     * @var null|string
     */
    private $message;

    /**
     * Get a license status indicating a success.
     *
     * @since 0.9
     * @param null|string $message Leave the custom message empty for a default message.
     * @return License_Status
     */
    public static function success($message = null)
    {
        if($message === null) {
            $message = __('The action was successful.', 'affilicious');
        }

        return new self(self::SUCCESS, $message);
    }

    /**
     * Get a license status indicating an error.
     *
     * @since 0.9
     * @param null|string $message Leave the custom message empty for a default message.
     * @return License_Status
     */
    public static function error($message = null)
    {
        if($message === null) {
           $message = __('An error has occurred. Please try again.', 'affilicious');
        }

        return new self(self::ERROR, $message);
    }

    /**
     * Get a license status indicating an unknown license.
     *
     * @since 0.8.13
     * @return License_Status
     */
    public static function unknown()
    {
        return new self(self::UNKNOWN);
    }

    /**
     * Get a license status indicating a missing license.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function missing()
    {
        return new self(self::MISSING, __('The license is missing.', 'affilicious'));
    }

    /**
     * Get a license status indicating a valid license.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function valid()
    {
        return new self(self::VALID, __('The license is valid', 'affilicious'));
    }

    /**
     * Get a license status indicating an invalid license.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function invalid()
    {
        return new self(self::ERROR, __('The license is invalid', 'affilicious'));
    }

    /**
     * Build a license status indicating an activation success.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function activation_success()
    {
        return new self(self::SUCCESS, __('Succeeded to activate the license.', 'affilicious'));
    }

    /**
     * Build a license status indicating an activation error.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function activation_error()
    {
        return new self(self::SUCCESS, __('Failed to activate the license.', 'affilicious'));
    }

    /**
     * Build a license status indicating a deactivation success.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function deactivation_success()
    {
        return new self(self::SUCCESS, __('Succeeded to deactivate the license.', 'affilicious'));
    }

    /**
     * Build a license status indicating a deactivation error.
     *
     * @since 0.9
     * @return License_Status
     */
    public static function deactivation_error()
    {
        return new self(self::SUCCESS, __('Failed to deactivate the license.', 'affilicious'));
    }

    /**
     * @since 0.9
     * @param string $type
     * @param string $message
     */
    public function __construct($type, $message = null)
    {
        Assert::string($type, 'Expected the type to be a string. Got: %s');
        Assert::nullOrString($type, 'Expected the message to be a string or null. Got: %s');

        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the license status type.
     *
     * @since 0.9
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Check if the license status type is valid.
     *
     * @since 0.9
     * @return bool
     */
    public function is_valid()
    {
        return $this->type == self::VALID;
    }

    /**
     * Check if the license status type is valid.
     *
     * @since 0.9
     * @return bool
     */
    public function is_invalid()
    {
        return $this->type == self::INVALID;
    }

    /**
     * Check if the license status type is missing.
     *
     * @since 0.9
     * @return bool
     */
    public function is_missing()
    {
        return $this->type == self::MISSING;
    }

    /**
     * Check if the license status type is indicating a success.
     *
     * @since 0.9
     * @return bool
     */
    public function is_success()
    {
        return $this->type == self::SUCCESS;
    }

    /**
     * Check if the license status type is indicating an error.
     *
     * @since 0.9
     * @return bool
     */
    public function is_error()
    {
        return $this->type == self::ERROR;
    }

    /**
     * Check if the license status has an message.
     *
     * @since 0.9
     * @return bool
     */
    public function has_message()
    {
        return $this->message !== null;
    }

    /**
     * Get the license status message.
     *
     * @since 0.9
     * @return null|string
     */
    public function get_message()
    {
        return $this->message;
    }
}
