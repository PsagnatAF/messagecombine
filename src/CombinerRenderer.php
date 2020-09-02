<?php

namespace Psagnataf\MessageCombine;

use Mustache_Engine;
use Illuminate\Support\Str;

class CombinerRenderer
{

    /** @var \Psagnataf\MessageCombine\Combiner */
    protected $templateMessageable;

    /** @var \Psagnataf\MessageCombine\Interfaces\MessageCombineInterface */
    protected $messageTemplate;

    /** @var \Mustache_Engine */
    protected $mustache;

    public function __construct(Combiner $templateMessageable, Mustache_Engine $mustache)
    {
        $this->templateMessageable = $templateMessageable;
        $this->mustache = $mustache;
        $this->messageTemplate = $templateMessageable->getMessageTemplate();
    }

    public function renderLayout(array $data = []): string
    {
        $body = $this->mustache->render(
            $this->messageTemplate->getTemplate(),
            $data
        );

        return $this->renderInLayout($body, $data);
    }

    protected function renderInLayout(string $body, array $data = []): string
    {
        $method = 'getTemplate';
        $layout = $this->templateMessageable->$method()
            ?? (method_exists($this->messageTemplate, $method) ? $this->messageTemplate->$method() : null)
            ?? '{{{ body }}}';

        $this->guardAgainstInvalidLayout($layout);

        $data = array_merge(['body' => $body], $data);

        return $this->mustache->render($layout, $data);
    }

    protected function guardAgainstInvalidLayout(string $layout): void
    {
        if (! Str::contains($layout, [
            '{{{body}}}',
            '{{{ body }}}',
            '{{body}}',
            '{{ body }}',
            '{{ $body }}',
            '{!! $body !!}',
        ])) {
            throw new Exception('Parse error in template');
        }
    }
}
