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

interface IdentifiableInterface
{
    public function getId(): ?string;

    public function setId(int $id);
}
