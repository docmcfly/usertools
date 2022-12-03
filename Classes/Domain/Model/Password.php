<?php

namespace Cylancer\Usertools\Domain\Model;

/**
 *
 * This file is part of the "User tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 *
 * @package Cylancer\Usertools\Domain\Model
 */
class Password {

    /**
     * @var string
     */
    protected $password = '';

		/**
     * @var string
     */
    protected $confirmPassword = '';


    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getConfirmPassword() {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     */
    public function setConfirmPassword($confirmPassword) {
        $this->confirmPassword = $confirmPassword;
    }

}
