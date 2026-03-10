<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;


#[Attribute(Attribute::TARGET_PROPERTY)]
class UniqueEmail extends Constraint {
    public string $message = "The Email {{ value }} is not unique";
}