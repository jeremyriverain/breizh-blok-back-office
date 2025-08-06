<?php

namespace App\Utils;

use Symfony\Component\ExpressionLanguage\Expression;

class AllowContributionExpression extends Expression
{
    public function __construct()
    {
        parent::__construct('"ROLE_ADMIN" in role_names or subject.getCreatedBy()?.getId() == user.getId()');
    }
}
