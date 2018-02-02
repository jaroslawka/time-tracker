<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AppBundle\Model;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Description of User
 *
 * @author dev
 */
class User
{

    private $user;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * Get current logged user id
     * 
     * @return int
     */
    public function getLoggedUserId()
    {
        return $this->user->getId();
    }
}
