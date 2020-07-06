<?php
namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Author;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
private $encoder;
public function __construct(UserPasswordEncoderInterface $encoder){
    $this->encoder = $encoder;
}
public function PersistAnnonceEntity(Annonce $entity){
    $entity->setDate(new \DateTime);
    $entity->setAuthor($this->getUser());

    parent::persistEntity($entity);
}
public function PersistUserEntity(Author $entity){
    $entity->setCreatedOn(new \DateTime);
    $plainPassword = $entity->getPassword();
    $entity->setPassword($this->encoder->encodePassword($entity, $plainPassword));
    parent::persistEntity($entity);
}

}