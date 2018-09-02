<?php

namespace App\Application\SettingsBundle\Repository;

use App\Application\SettingsBundle\Entity\Settings;
use \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use \Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Settings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Settings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Settings[]    findAll()
 * @method Settings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsRepository extends EntityRepository 
{

}
