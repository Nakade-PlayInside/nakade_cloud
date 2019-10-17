<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeatureCommentRepository")
 */
class FeatureComment extends FeatureSuperclassBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Feature", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    public function getParent(): ?Feature
    {
        return $this->parent;
    }

    public function setParent(?Feature $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
