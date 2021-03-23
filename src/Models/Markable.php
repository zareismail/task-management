<?php

namespace Zareismail\Task\Models;

use Zareismail\Markable\{
    Markable as ZareismailMarkable, HasDraft, HasPending, Publishable, Acceptable, Rejectable, Progressive, Completable
};

trait Markable 
{
    use ZareismailMarkable, HasDraft, HasPending, Publishable, Acceptable, Rejectable, Progressive, Completable;  

    /**
     * Determine if can run actions on the model.
     * 
     * @return boolean
     */
    public function isAccessible()
    {
        return $this->isPublished();
    }

    /**
     * Determine if can not run actions on the model.
     * 
     * @return boolean
     */
    public function isNotAccessible()
    {
        return ! $this->isAccessible();
    }
}
