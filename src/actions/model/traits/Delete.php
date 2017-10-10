<?php

namespace flipbox\spark\actions\model\traits;

use flipbox\spark\actions\traits\CheckAccess;

trait Delete
{
    use CheckAccess, Manage;

    /**
     * HTTP success response code
     *
     * @return int
     */
    protected function statusCodeSuccess(): int
    {
        return 204;
    }

    /**
     * HTTP fail response code
     *
     * @return int
     */
    protected function statusCodeFail(): int
    {
        return 401;
    }
}
