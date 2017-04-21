<?php
namespace StephenHarris\WordPressBehatExtension\Context\Users;

/**
 * A 'helper' class primarily used by WordPressUserContext which holds the step definitions.
 *
 * This class has been seperated out from the step definitions so that it can be re-used for other contexts.
 *
 * @package StephenHarris\WordPressBehatExtension\Context
 */
trait WordPressUserTrait
{

    public function insert($userData)
    {
        $user_id = wp_insert_user($userData);
        if (!is_int($user_id)) {
            throw new \InvalidArgumentException("Invalid user information schema.");
        }
        return $user_id;
    }

    public function getUser($login_or_email)
    {
        try {
            $term = $this->getUserByLogin($login_or_email);
        } catch (\Exception $e) {
            try {
                $term = $this->getUserByEmail($login_or_email);
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf('No user with login or email "%s" found', $login_or_email)
                );
            }
        }
        return $term;
    }

    public function getUserByLogin($login)
    {
        $user = get_user_by('login', $login);
        if (! $user) {
            throw new \Exception(
                sprintf('No user with login "%s" found', $login)
            );
        }
        return $user;
    }

    public function getUserByEmail($email)
    {
        if ($email !== sanitize_email($email)) {
            throw new \Exception(
                sprintf('"%s" is not a valid e-mail', $email)
            );
        }

        $user = get_user_by('email', $email);
        if (! $user) {
            throw new \Exception(
                sprintf('No user with email "%s" found', $email)
            );
        }
        return $user;
    }

    public function validatePassword(\WP_User $WPUser, $password)
    {
        if (! wp_check_password($password, $WPUser->data->user_pass, $WPUser->ID)) {
            throw new \Exception(sprintf('Password for user %s incorrect', $password));
        }
    }
}
