<?php 

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator{
    public function __construct(private UserRepository $userRepository) {}
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof UniqueEmail) return;
        $foundUser = $this->userRepository->findOneBy(['email' => $value]);
        if($foundUser){
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $value)->addViolation();
        }
    }    
}