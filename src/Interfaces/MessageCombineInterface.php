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

    /**
     * Get the template.
     *
     * @return string
     */
    public function getTemplate(): string;
}
