<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    //Récupére le Hasher de password
    private UserPasswordHasherInterface $hasher;

    Public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    //Function qui se déclenche avant le persist pour hash les password user
    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }

    //Function qui se déclenche avant le update pour hash les password du user
    public function preUpdate(User $user)
    {
        $this->encodePassword($user);
    }

    //Hash le password basé sur le PlainPassword 
    public function encodePassword(User $user)
    {
        if($user->getPlainPassword() === null){
            return;
        }

        //Set le password Hash grace a l méthode HashPassword native a symfony
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );
    }
}