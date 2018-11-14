<?php

/*
 * This file is part of exter-n/seqgen-bundle.
 *
 * Copyright (C) Exter-N
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exn\SeqgenBundle\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Exn\SeqgenBundle\IdentifierGeneratorInterface;

class IdentifiableUpdater
{
    /** @var IdentifierGeneratorInterface */
    private $identifierGenerator;

    public function __construct(IdentifierGeneratorInterface $identifierGenerator)
    {
        $this->identifierGenerator = $identifierGenerator;
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $ent = $event->getEntity();

        if (\method_exists($ent, 'isTombstone') && $ent->isTombstone()) {
            throw new \LogicException('Won\'t persist tombstone of '.\get_class($ent));
        }

        if ($ent instanceof IdentifiableInterface && null === $ent->getId()) {
            $ent->setId($this->identifierGenerator->generateOne());
        }
    }
}
