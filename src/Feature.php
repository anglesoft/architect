<?php

namespace Angle\Architect;

use Illuminate\Foundation\Bus\DispatchesJobs;

class Feature {
    use DispatchesJobs;
    use MarshalTrait;
    use TaskDispatcherTrait;
}
