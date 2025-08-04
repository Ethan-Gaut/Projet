<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }


    public function findAllWithCategorySorted(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->orderBy('c.name', 'ASC')    // Utilise 'nom' si ton champ s'appelle 'nom' dans Category
            ->addOrderBy('a.titre', 'ASC');
    
        $articles = $qb->getQuery()->getResult();
    
        // Regroupement par catégorie
        $grouped = [];
    
        foreach ($articles as $article) {
            foreach ($article->getCategories() as $category) {
                $grouped[$category->getName()][] = $article; // ou getName() selon ton entité
            }
        }
    
        // Tri alphabétique des catégories
        ksort($grouped);
    
        return $grouped;
    }
//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
