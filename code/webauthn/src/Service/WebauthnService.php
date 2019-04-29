<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\WebauthnCredential;

class WebauthnService
{
  private $emi;
  private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->emi = $entityManagerInterface;
    }

    public function findUserByUsername($Username){
      return $this->emi->getRepository(User::class)->findOneBy(['Username' => $Username]);
    }

    public function insertUser($Username, $Password){
      $user = new User();
      $user->setUsername($Username);
      $user->setPassword(password_hash($Password, PASSWORD_BCRYPT));
      $this->emi->persist($user);
      $this->emi->flush();
    }

    public function findCredentials($Username){
      return $this->emi->getRepository(WebauthnCredential::class)->findBy(['Username' => $Username]);
    }

    public function findCredentialById($Username, $id){
      return $this->emi->getRepository(WebauthnCredential::class)->findOneBy(['Username' => $Username, 'credentialId' => $id]);
    }

    public function deleteCredential($Username, $id){
      $webauthncredential = $this->emi->getRepository(WebauthnCredential::class)->findOneBy(['Username' => $Username, 'id' => $id]);
      if($webauthncredential){
        $this->emi->remove($webauthncredential);
        $this->emi->flush();
      }
    }

    public function insertCredential($webauthncredential){
      $this->emi->persist($webauthncredential);
      $this->emi->flush();
    }
}
?>
