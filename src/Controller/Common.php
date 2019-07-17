<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

/**
 * Class Common
 * @package App\Controller
 */
class Common
{
    public function number()
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}