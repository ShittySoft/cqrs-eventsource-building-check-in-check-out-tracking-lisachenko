<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;

final class CheckUserIntoBuilding extends Command
{
    /**
     * @var string
     */
    private $buildingId;
    /**
     * @var string
     */
    private $username;

    private function __construct(string $username, string $buildingId)
    {
        $this->init();

        $this->username = $username;
        $this->buildingId = $buildingId;
    }

    public static function fromUsernameAndBuildingId(string $username, $buildingId) : self
    {
        return new self($username, $buildingId);
    }

    public function username() : string
    {
        return $this->username;
    }

    public function buildingId() : string
    {
        return $this->buildingId;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username'   => $this->username,
            'buildingId' => $this->buildingId,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username   = $payload['name'];
        $this->buildingId = $payload['buildingId'];
    }
}
