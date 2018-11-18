<?php

namespace Angle\Architect;

use Angle\Architect\Traits\Injection;
use Angle\Architect\Traits\RunsTasks;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Feature {
    use Injection;
    use RunsTasks;
    use DispatchesJobs;
}
