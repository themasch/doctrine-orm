<?php

declare(strict_types=1);

namespace Doctrine\ORM\Id;

use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Id generator that obtains IDs from special "identity" columns. These are columns
 * that automatically get a database-generated, auto-incremented identifier on INSERT.
 * This generator obtains the last insert id after such an insert.
 */
class IdentityGenerator extends AbstractIdGenerator
{
    /**
     * @param string|null $sequenceName The name of the sequence to pass to lastInsertId()
     *                                  to obtain the last generated identifier within the current
     *                                  database session/connection, if any.
     */
    public function __construct(
        private ?string $sequenceName = null
    ) {
        if ($sequenceName !== null) {
            Deprecation::trigger(
                'doctrine/orm',
                'https://github.com/doctrine/orm/issues/8850',
                'Passing a sequence name to the IdentityGenerator is deprecated in favor of using %s. $sequenceName will be removed in ORM 3.0',
                SequenceGenerator::class
            );
        }
    }

    public function generateId(EntityManagerInterface $em, ?object $entity): int
    {
        return (int) $em->getConnection()->lastInsertId($this->sequenceName);
    }

    public function isPostInsertGenerator(): bool
    {
        return true;
    }
}
