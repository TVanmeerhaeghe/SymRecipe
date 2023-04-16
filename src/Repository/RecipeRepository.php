<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //Function qui permet de trouver les recipes ayant le isPublic a true
    public function findPublicRecipe(?int $nbRecipes): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            //Recupéré les recettes ou la colonne isPublic = 1
            ->where('r.isPublic = 1')
            ->orderBy('r.createdAt', 'DESC');
        
        //Si le nbRecipes est différent de 0 ou de nul définis le maximum de recette affiché en fonctio ndu nombre de recettes
        if ($nbRecipes !== 0 || $nbRecipes !== null) {
            $queryBuilder->setMaxResults($nbRecipes);
        }

        //Récupére le resultat de la query
        return $queryBuilder->getQuery()
            ->getResult();

    }
}
