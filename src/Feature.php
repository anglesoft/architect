<?php

namespace Angle\Architect;

use Angle\Architect\Traits\Injectable;
use Angle\Architect\Traits\Taskable;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Feature {
    use Injectable;
    use Taskable;
    use DispatchesJobs;
}
