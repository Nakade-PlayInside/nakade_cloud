<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"CLASS", "ANNOTATION"})
 */
class ResultMatch extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $messageWinByDefault = 'match.result.winByDefault';
    public $messageUnsetPairing = 'match.result.unsetPairing';
    public $messageDrawByDefault = 'match.draw.winByDefault';

    /**
     * allows to use an instance of that entity.
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
