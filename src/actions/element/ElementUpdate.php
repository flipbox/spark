<?php

namespace flipbox\spark\actions\element;

use flipbox\spark\actions\model\ModelUpdate;

abstract class ElementUpdate extends ModelUpdate
{
    use traits\SaveAction, traits\Lookup;
}
