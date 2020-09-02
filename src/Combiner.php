<?php

namespace Psagnataf\MessageCombine;

use Mustache_Engine;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Psagnataf\MessageCombine\Models\MessageCombine;
use Psagnataf\MessageCombine\Interfaces\MessageCombineInterface;

abstract class Combiner extends Model
{
    /** @var MessageCombineInterface */
    protected $messageTemplate;
    protected $messageable;
    protected $params;

    public static function getVariables(): array
    {
        return static::getPublicProperties();
    }

    public function template()
    {
        $this->messageable = MessageCombine::where('messageable', static::class)->where('event', $this->event)->first();
        if (!is_null($this->messageable)) {
            return $this->render();
        }
        return '';
    }

    public function render()
    {
        return $this->combineParams()->build();
    }

    public function build()
    {
        $mustache = new Mustache_Engine(array('entity_flags' => ENT_QUOTES));
        return $mustache->render($this->messageable->template, $this->params);
    }

    public function combineParams()
    {
        $this->params = [];
        foreach(static::getPublicProperties() as $param) {
            $this->params[$param] = $this->$param; 
        }
        return $this;
    }

    protected static function getPublicProperties(): array
    {
        $class = new ReflectionClass(static::class);

        return collect($class->getProperties(ReflectionProperty::IS_PUBLIC))
            ->map->getName()
            ->diff(static::getIgnoredPublicProperties())
            ->values()
            ->all();
    }

    protected static function getIgnoredPublicProperties(): array
    {
        $modelClass = new ReflectionClass(Model::class);

        return collect()
            ->merge($modelClass->getProperties(ReflectionProperty::IS_PUBLIC))
            ->map->getName()
            ->values()
            ->all();
    }

    protected function getMessageTemplateRenderer(): CombinerRenderer
    {
        return app(CombinerRenderer::class, ['templateMessageable' => $this]);
    }
}
