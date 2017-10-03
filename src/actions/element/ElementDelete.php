<?php

namespace flipbox\spark\actions\element;

use flipbox\spark\actions\model\ModelDelete;

abstract class ElementDelete extends ModelDelete
{
    use traits\DeleteAction, traits\Lookup;
}
