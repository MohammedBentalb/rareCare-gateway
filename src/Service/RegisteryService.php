<?php

namespace App\Service;

class RegisteryService {
    public function __construct(private array $services) {}

    public function get(string $service): string{
        if(!isset($this->services[$service])) throw new \InvalidArgumentException("the param $service is invalid inside the ServiceRegistery");
        return $this->services[$service];
    }

    public function has(string $service): bool {
        return isset($this->services[$service]);
    }
    
    public function getAll(): array{
        return $this->services;
    }
}