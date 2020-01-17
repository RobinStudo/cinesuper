<?php

namespace App\Service;

use App\Repository\EventRepository;

/**
 * Class EventService
 * @package App\Service
 */
class EventService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var \DateTime
     */
    private $currentDate;

    /**
     * EventService constructor.
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->currentDate = new \DateTime();
    }

    /**
     * @return float|int
     */
    public function getMultiplicatorWhenEvent()
    {
        $events = $this->eventRepository->findAll();

        $multiplicator = 0;

        if ($events) {
            forEach($events as $event) {
                if ($this->currentDate >= $event->getStartAt() && $this->currentDate <= $event->getEndAt()) {
                    $multiplicator += $event->getMultiplicateur();
                }
            }
        }
        else {
            $multiplicator = 1;
        }

        return $multiplicator;
    }
}
