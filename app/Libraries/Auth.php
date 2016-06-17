<?php


namespace App\Libraries;

use App\Models\User as User;
use App\Models\Attempt as Attempt;
use App\Accessor;

class Auth extends Accessor
{
    private $error = null;

    public function attempt($username, $password, $ip)
    {
        if ($this->checkAttempt($ip)) {

            $user = User::where('username', $username)
                ->get()
                ->first();

            if (!isset($user)) {
                $this->error = "We couldn't verify your credentials. Please try again.";
                return false;
            }

            if (!password_verify($password, $user->password)) {
                $this->error = "We couldn't verify your credentials. Please try again.";
                return false;
            }

            $_SESSION['user'] = $user->id;
            $this->resetAttempt($ip);

            return true;

        } else {
            $this->error = "There where already three failed login attempts with your ip address (".$ip."). Please wait five minutes and try again.";
            return false;
        }
    }

    private function resetAttempt($ip)
    {
        Attempt::where('ip_address', md5($ip))
            ->update([
                'count' => 0,
                'lock_time' => null
            ]);
    }

    private function checkAttempt($ip)
    {
        $encryptedIp = md5($ip);

        $attempt = Attempt::where('ip_address', $encryptedIp)
            ->get()
            ->first();

        if (!isset($attempt)) {

            $attempt = new Attempt();
            $attempt->ip_address = $encryptedIp;
            $attempt->count = 1;
            $attempt->save();

            return true;

        } else {

            if ($attempt->count >= 3) {

                if (!isset($attempt->lock_time)) {

                    Attempt::where('id', $attempt->id)
                        ->update([
                            'lock_time' => date('Y-m-d H:i:s')
                        ]);
                    return false;

                } else if (!$this->checkIfToOld($attempt->lock_time, '5')) {
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

    public function error()
    {
        return $this->error;
    }

    /**
     * Returns true if the given timestamp is older than
     * the check value in minutes.
     *
     * Timestamp must be in format Y-m-d H:i:s
     *
     * @param  $actual
     * @param  $check
     * @return bool
     */
    private function checkIfToOld($actual, $check)
    {
        return (strtotime($actual) <= strtotime('-'.$check.' minutes'));
    }
}