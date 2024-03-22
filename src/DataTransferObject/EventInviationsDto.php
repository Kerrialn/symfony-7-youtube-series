<?php

namespace App\DataTransferObject;

use App\Entity\User\User;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class EventInviationsDto
{

    /**
     * @var ArrayCollection<int, User>
     */
    private ArrayCollection $participants;


    private CarbonImmutable $createdAt;


    /**
     * @param ArrayCollection $participants
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->createdAt = new CarbonImmutable();
    }

    public function getParticipants(): ArrayCollection
    {
        return $this->participants;
    }

    public function setParticipants(ArrayCollection $participants): void
    {
        $this->participants = $participants;
    }

}