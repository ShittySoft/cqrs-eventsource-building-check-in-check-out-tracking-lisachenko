<?php

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutFromBuilding;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array|string[]
     */
    private $checkedInUsers = [];

    public static function new($name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (in_array($username, $this->checkedInUsers, true)) {
            throw new \InvalidArgumentException(
                "User {$username} was already checked in"
            );
        }

        $this->recordThat(UserCheckedIntoBuilding::occur(
            $this->id(),
            [
                'username' => $username
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
        if (!in_array($username, $this->checkedInUsers, true)) {
            throw new \InvalidArgumentException(
                "User {$username} was already checked out"
            );
        }

        $this->recordThat(UserCheckedOutFromBuilding::occur(
            $this->id(),
            [
                'username' => $username
            ]
        ));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenUserCheckedIntoBuilding(UserCheckedIntoBuilding $event)
    {
        $username = $event->username();

        $this->checkedInUsers[] = $username;

        $this->checkedInUsers = array_unique($this->checkedInUsers);
    }

    public function whenUserCheckedOutFromBuilding(UserCheckedOutFromBuilding $event)
    {
        $username = $event->username();

        $this->checkedInUsers = array_diff($this->checkedInUsers, [$username]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
