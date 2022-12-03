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
class Email {

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }


}
