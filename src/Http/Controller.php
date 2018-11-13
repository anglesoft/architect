<?php

namespace Angle\Architect\Http;

use Angle\Architect\ServesFeaturesTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
    use ValidatesRequests;
    use ServesFeaturesTrait;
}
