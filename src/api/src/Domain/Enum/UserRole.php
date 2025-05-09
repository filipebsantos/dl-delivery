<?php

namespace DLDelivery\Domain\Enum;

enum UserRole: int {
    case DELIVERER = 1;
    case OPERATOR = 2;
    case ADMINISTRATOR = 3;

    public function hasAccessLevel(UserRole $requiredAccess): bool {
        return $this->value >= $requiredAccess->value;
    }

}
