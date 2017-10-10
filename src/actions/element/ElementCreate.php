<?php

namespace flipbox\spark\actions\element;

use flipbox\spark\actions\model\ModelCreate;

abstract class ElementCreate extends ModelCreate
{
    use traits\Save;
}
