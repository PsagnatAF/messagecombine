<?php

namespace Psagnataf\MessageCombine\Models;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Psagnataf\MessageCombine\Interfaces\MessageCombineInterface;

class MessageCombine extends Model implements MessageCombineInterface
{
    protected $guarded = [];

    public function getVariables(): array
    {
        $messageableClass = $this->messageable;

        if (! class_exists($messageableClass)) {
            return [];
        }

        return $messageableClass::getPublicVariables();
    }

    public static function findForMessageable(Model $messageable)
    {

    }

    public function getVariablesAttribute(): array
    {
        return $this->getVariables();
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
