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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IdentifiableTrait
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @Groups({ "default", "metadata" })
     */
    private $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
