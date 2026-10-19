<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Docs\Reference;

use OpenApi\Builder;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Merge\Concerns\IdentityTrait;
use OpenApi\Merge\Mode;
use OpenApi\Tools\Docs\DocGenerator;

/**
 * The shipped mergers, what makes two attributes the same one, and the modes a folding merger
 * takes.
 *
 * The defaults come from the live `Builder`, so they are listed in the order they are tried.
 * Opt-in mergers ship but are never in the defaults, so they are found under `src/Merge/`.
 */
class MergerGenerator extends DocGenerator
{
    /**
     * @return array<string, string>
     */
    public function generate(): array
    {
        $content = $this->renderer->preamble(
            'Merger',
            $this->snippetContent('mergers'),
        );

        $content .= "\n" . $this->renderer->sectionHeader('Default Mergers');
        foreach ($this->collect($this->mergers()) as $data) {
            $content .= "\n" . $this->renderer->classHeader($data['name'], 'Merge');
            $content .= $this->renderSections($data);
        }

        $optIn = $this->optInMergers();
        if ($optIn !== []) {
            $content .= "\n" . $this->renderer->sectionHeader('Opt-in Mergers');
            foreach ($this->collect($optIn) as $data) {
                $content .= "\n" . $this->renderer->classHeader($data['name'], 'Merge');
                $content .= $this->renderSections($data);
            }
        }

        $content .= "\n" . $this->renderer->sectionHeader('Identity');
        $content .= "\n" . $this->parseDocblock((new \ReflectionMethod(IdentityTrait::class, 'identity'))->getDocComment())['content'] . "\n";

        $content .= "\n" . $this->renderer->sectionHeader('Modes');
        $content .= "\n" . $this->parseDocblock((new \ReflectionEnum(Mode::class))->getDocComment())['content'] . "\n";
        foreach ((new \ReflectionEnum(Mode::class))->getCases() as $case) {
            $content .= "\n" . $this->renderer->sectionHeader('`Mode::' . $case->getName() . '`', 3);
            $content .= "\n" . $this->parseDocblock($case->getDocComment())['content'] . "\n";
        }

        return ['mergers' => $content];
    }

    /**
     * @return list<MergerInterface>
     */
    protected function mergers(): array
    {
        return array_values(iterator_to_array((new Builder())->getMergers()));
    }

    /**
     * The mergers that ship under `src/Merge/` but are not registered by default.
     *
     * @return list<MergerInterface>
     */
    protected function optInMergers(): array
    {
        $defaults = array_map(static fn (MergerInterface $merger): string => $merger::class, $this->mergers());

        $optIn = [];
        foreach (glob("{$this->projectRoot}/src/Merge/*.php") ?: [] as $file) {
            $class = 'OpenApi\\Merge\\' . pathinfo($file, PATHINFO_FILENAME);
            if (!class_exists($class) || !is_a($class, MergerInterface::class, true) || in_array($class, $defaults, true)) {
                continue;
            }

            $rc = new \ReflectionClass($class);
            if (!$rc->isAbstract()) {
                /** @var MergerInterface $merger */
                $merger = $rc->newInstance();
                $optIn[] = $merger;
            }
        }

        return $optIn;
    }
}
