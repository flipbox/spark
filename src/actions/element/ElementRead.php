<?php

namespace flipbox\spark\actions\element;

use flipbox\spark\actions\model\ModelRead;

abstract class ElementRead extends ModelRead
{
    use traits\Lookup;
}
