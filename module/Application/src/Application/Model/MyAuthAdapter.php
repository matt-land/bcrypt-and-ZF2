<?php
/**
 * Created by IntelliJ IDEA.
 * User: mland
 * Date: 10/29/14
 * Time: 8:35 AM
 */

namespace Application\Model;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Result;
use Zend\Http\Request;
use Zend\Crypt\Password\Bcrypt;

class MyAuthAdapter implements AuthenticationServiceInterface
{
    const USER_FIELD        = 'username';
    const PASSWORD_FIELD    = 'password';

    private $identity = false;
    private $userName = false;
    private $userSecret = false;

    private $secret = array(
        //user1 has a secret of 'password' stored in md5
        'user1' => '5f4dcc3b5aa765d61d8327deb882cf99',
        //user2 has a secret of 'password' in bcrypt
        'user2' => '$2a$12$2jjpAL5DOkN9y4hdtB/79Oc1gUK1CianbZM1uxtyCBGyhKYM6QItm'
    );

    public function __construct(Request $request)
    {
        $this->userName = $request->getPost(self::USER_FIELD);
        $this->userSecret = $request->getPost(self::PASSWORD_FIELD);
    }

    public function authenticate()
    {
        if (! isset($this->secret[$this->userName])) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, false);
        }
        if (stripos($this->secret[$this->userName], '$2a') !== false) { //this is a bcrypt password
            $bool = $this->_bcryptTest($this->userSecret, $this->secret[$this->userName]);
        } else {
            $bool = $this->_md5Test($this->userSecret, $this->secret[$this->userName]);
        }
        if (! $bool) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, false);
        }
        $this->identity = $this->userName;

        return new Result(Result::SUCCESS, $this->getIdentity());
    }

    public static function _bcryptTest($userProvidedSecret, $secretHash)
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->verify($userProvidedSecret, $secretHash);
    }

    public static function _md5Test($userProvidedSecret, $secretHash)
    {
        return md5($userProvidedSecret) === $secretHash ? true : false;
    }

    public function clearIdentity()
    {
        $this->identity = false;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function hasIdentity()
    {
        if ($this->identity !== false) {
            return true;
        }
        return false;
    }
} 