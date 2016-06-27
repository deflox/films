<?php

/**
 * This library provides functions for
 * authentication purposes.
 *
 * @author Leo Rudin
 */

namespace App\Libraries;

use App\Models\User as User;
use App\Models\Attempt as Attempt;
use App\Accessor;

class Auth extends Accessor
{
    /**
     * Contains string with the error.
     *
     * @var string
     */
    private $error = null;

    /**
     * Defines how many attempts a user
     * can make until he gets locked
     * out of the application.
     *
     * @var integer
     */
    private $attempts = 3;

    /**
     * Defines how long the user gets
     * locked out of the application
     * in minutes in case he made
     * more attempts than possible.
     *
     * @var integer
     */
    private $lockTime = 5;

    /**
     * Makes an attempt to authenticate a user
     * to the application.
     *
     * @param  $username
     * @param  $password
     * @param  $ip
     * @return bool
     */
    public function attempt($username, $password, $ip)
    {
        if ($this->checkAttempt($ip)) {

            $user = User::where('username', $username)
                ->get()
                ->first();

            // Check if user exists
            if (!isset($user)) {
                $this->error = "We couldn't verify your credentials. Please try again.";
                return false;
            }

            // Check password
            if (!password_verify($password, $user->password)) {
                $this->error = "We couldn't verify your credentials. Please try again.";
                return false;
            }

            // Set session
            $_SESSION['user'] = $user->id;

            // Reset attempt if user logged in correctly
            $this->resetAttempt($ip);

            return true;

        } else {

            $this->error = "There where already ".$this->attempts." failed login attempts with your ip address (".$ip."). Please wait ".$this->lockTime." minutes and try again.";
            return false;

        }

    }

    /**
     * Checks if the users attempt is valid.
     *
     * @param  $ip
     * @return bool
     */
    private function checkAttempt($ip)
    {
        $attempt = Attempt::where('ip_address', md5($ip))
            ->get()
            ->first();


        if (!isset($attempt)) {


            $attempt = new Attempt();
            $attempt->ip_address = md5($ip);
            $attempt->count = 1;
            $attempt->save();
            return true;

        } else {

            if ($attempt->count >= $this->attempts) {

                if (!isset($attempt->lock_time)) {

                    Attempt::where('id', $attempt->id)
                        ->update([
                            'lock_time' => date('Y-m-d H:i:s')
                        ]);

                    return false;

                } else if (!$this->checkLockTime($attempt->lock_time, $this->lockTime)) {

                    return false;

                } else {

                    Attempt::where('id', $attempt->id)
                        ->update([
                            'count' => 1,
                            'lock_time' => null
                        ]);

                    return true;

                }

            } else {
                Attempt::where('id', $attempt->id)
                    ->update([
                        'count' => $attempt->count + 1
                    ]);
                return true;
            }

        }

    }

    /**
     * Resets an attempt entry based on the users
     * ip address.
     *
     * @param $ip
     */
    private function resetAttempt($ip)
    {
        Attempt::where('ip_address', md5($ip))
            ->update([
                'count' => 0,
                'lock_time' => null
            ]);
    }

    /**
     * Returns true if lock time on database is older
     * than the defined time, a user gets locked out
     * of the application.
     *
     * @param  $actual string Lock time on databse
     * @param  $check  int    Time in minutes to check
     * @return bool
     */
    private function checkLockTime($actual, $check)
    {
        return (strtotime($actual) <= strtotime('-'.$check.' minutes'));
    }

    /**
     * Returns error variable.
     *
     * @return string
     */
    public function error()
    {
        return $this->error;
    }
}