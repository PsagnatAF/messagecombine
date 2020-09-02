<?php

namespace Psagnataf\MessageCombine;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Psagnataf\MessageCombine\Models\MessageCombine;
use Psagnataf\MessageCombine\Interfaces\MessageCombineInterface;

abstract class Combiner extends Model
{
    /** @var MessageCombineInterface */
    protected $messageTemplate;

    public static function getVariables(): array
    {
        return static::getPublicProperties();
    }

    public function getMessageTemplate(): MessageCombineInterface
    {
        return $this->messageTemplate;
    }

    public function buildView()
    {
        $renderer = $this->getMessageTemplateRenderer();

        $viewData = $this->buildViewData();

        $html = $renderer->renderHtmlLayout($viewData);
        $text = $renderer->renderTextLayout($viewData);

        return array_filter([
            'html' => new HtmlString($html),
            'text' => new HtmlString($text),
        ]);
    }

    public function build()
    {
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
