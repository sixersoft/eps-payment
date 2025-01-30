<?php 

namespace Sixersoft\EPSPayment\Facades;

use Illuminate\Support\Facades\Facade;

class EPSFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eps';
    }
}
