<?php

namespace Angle\Architect\Http;

use Angle\Architect\Traits\Featurable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ValidatesRequests;
    use Featurable;
}
