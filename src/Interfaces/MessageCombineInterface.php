<?php

namespace Psagnataf\MessageCombine\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface MessageCombineInterface
{
    /**
     * Get the event.
     *
     * @return string
     */
    public function getEvent(): string;

}
