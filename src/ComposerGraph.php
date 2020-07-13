<?php

/**
 * JBZoo Toolbox - Composer-Graph
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Composer-Graph
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Composer-Graph
 */

namespace JBZoo\ComposerGraph;

use JBZoo\Data\Data;
use JBZoo\MermaidPHP\Graph;
use JBZoo\MermaidPHP\Link;
use JBZoo\MermaidPHP\Node;

use function JBZoo\Data\data;
use function JBZoo\Utils\bool;

/**
 * Class ComposerGraph
 * @package JBZoo\ComposerGraph\Graphs
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ComposerGraph
{
    public const FORMAT_HTML    = 'html';
    public const FORMAT_MERMAID = 'mermaid';

    /**
     * @var Graph
     */
    private $graphWrapper;

    /**
     * @var Graph
     */
    private $graphMain;

    /**
     * @var Graph
     */
    private $graphRequire;

    /**
     * @var Graph
     */
    private $graphDev;

    /**
     * @var Graph
     */
    private $graphPlatform;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string[]
     */
    private $createdLinks = [];

    /**
     * @var array
     */
    private $renderedNodes = [];

    /**
     * @var Data
     */
    protected $params;

    /**
     * ComposerGraph constructor.
     * @param Collection $collection
     * @param array      $params
     */
    public function __construct(Collection $collection, array $params = [])
    {
        $this->collection = $collection;

        $this->params = data(array_merge([
            // view options
            'php'          => false,
            'ext'          => false,
            'dev'          => false,
            'suggest'      => false,
            'link-version' => false,
            'lib-version'  => false,
            // output options
            'output-path'  => null,
            'direction'    => Graph::LEFT_RIGHT,
            'format'       => self::FORMAT_HTML,
            'vendor-dir'   => null,
            'abc-order'    => false,
        ], $params));

        $direction = $this->params->get('direction');
        $order = $this->params->get('abc-order');

        $this->graphWrapper = new Graph(['direction' => $direction, 'abc_order' => $order]);
        $this->graphWrapper->addSubGraph($this->graphMain = new Graph(['title' => 'Your Package']));

        $this->graphRequire = new Graph(['direction' => $direction, 'title' => 'Required', 'abc_order' => $order]);
        $this->graphDev = new Graph(['direction' => $direction, 'title' => 'Required Dev', 'abc_order' => $order]);
        $this->graphPlatform = new Graph(['direction' => $direction, 'title' => 'PHP Platform', 'abc_order' => $order]);
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $isSafeMode = defined('IS_PHPUNIT_TEST') && !IS_PHPUNIT_TEST;
        Node::safeMode($isSafeMode);

        $main = $this->collection->getMain();
        $this->renderNodeTree($main);
        return $this->render();
    }

    /**
     * @param Package $source
     * @return bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderNodeTree(Package $source): bool
    {
        if (in_array($source->getId(), $this->renderedNodes, true)) {
            return false;
        }
        $this->renderedNodes[] = $source->getId();

        $showPhp = $this->params->get('php');
        $showExt = $this->params->get('ext');
        $showDev = $this->params->get('dev');
        $showSuggest = $this->params->get('suggest');

        if (!$showSuggest && !$source->isTag(Package::HAS_META)) {
            return false;
        }

        foreach ($source->getRequired() as $target => $version) {
            $target = $this->collection->getByName($target);
            if (
                (!$showExt && $target->isPhpExt()) ||
                (!$showPhp && $target->isPhp())
            ) {
                continue;
            }

            $this->renderNodeTree($target);
            $this->addLink($source, $target, $version);
        }

        if ($showDev) {
            foreach ($source->getRequiredDev() as $target => $version) {
                $target = $this->collection->getByName($target);
                if (
                    (!$showExt && $target->isPhpExt()) ||
                    (!$showPhp && $target->isPhp())
                ) {
                    continue;
                }

                $this->renderNodeTree($target);
                $this->addLink($source, $target, $version);
            }
        }

        if ($showSuggest) {
            foreach (array_keys($source->getSuggested()) as $target) {
                $target = $this->collection->getByName((string)$target);
                if (
                    (!$showExt && $target->isPhpExt()) ||
                    (
                        !$showDev &&
                        !$target->isTag(Package::REQUIRED) &&
                        $target->isTag(Package::REQUIRED_DEV)
                    )
                ) {
                    continue;
                }

                $this->renderNodeTree($target);
                $this->addLink($source, $target, 'suggest');
            }
        }

        return true;
    }

    /**
     * @return string
     */
    protected function render(): string
    {
        if (count($this->graphRequire->getNodes()) > 0) {
            $this->graphWrapper->addSubGraph($this->graphRequire);
        }

        if (count($this->graphDev->getNodes()) > 0) {
            $this->graphWrapper->addSubGraph($this->graphDev);
        }

        if (count($this->graphPlatform->getNodes()) > 0) {
            $this->graphWrapper->addSubGraph($this->graphPlatform);
        }

        $format = strtolower(trim($this->params->get('format')));
        if (self::FORMAT_HTML === $format) {
            $htmlPath = (string)$this->params->get('output-path');

            $headerKeys = array_filter(array_keys($this->params->getArrayCopy(), static function (string $key): bool {
                return in_array($key, ['php', 'ext', 'dev', 'suggest', 'link-version', 'lib-version'], true);
            }));

            /**
             * @psalm-suppress InvalidArgument
             */
            $headerKeys = array_reduce($headerKeys, function (array $acc, string $key): array {
                if (bool($this->params->get($key))) {
                    $acc[] = $key;
                }
                return $acc;
            }, []);

            $titlePostfix = '';
            if (count($headerKeys)) {
                $flags = implode(' / ', $headerKeys);
                $titlePostfix = "\n<br><small><small>Flags: {$flags}</small></small>";
            }

            $main = $this->collection->getMain();
            file_put_contents($htmlPath, $this->graphWrapper->renderHtml([
                'version' => '8.6.0',
                'title'   => $main->getName() . ' - Graph of Dependencies' . $titlePostfix,
            ]));

            return $htmlPath;
        }

        if (self::FORMAT_MERMAID === $format) {
            return $this->graphWrapper->render();
        }

        throw new Exception("Invalid format: \"{$format}\"");
    }

    /**
     * @param Package $package
     * @return Node
     */
    protected function createNode(Package $package): Node
    {
        $graph = $this->getGraph($package);

        $nodeId = $package->getId();
        $showVersion = (bool)$this->params->get('lib-version');

        if ($currentNode = $graph->getNode($nodeId)) {
            return $currentNode;
        }

        if ($this->params->get('vendor-dir')) {
            $isInstalled = $package->isTag(Package::INSTALLED);
        } else {
            $isInstalled = $package->isTag(Package::HAS_META);
        }

        if ($isInstalled) {
            $node = new Node($nodeId, $package->getName($showVersion));
        } else {
            $node = new Node($nodeId, $package->getName($showVersion), Node::STADIUM);
        }

        $graph->addNode($node);

        return $node;
    }

    /**
     * @param Package $source
     * @param Package $target
     * @param string  $version
     * @return bool
     */
    private function addLink(Package $source, Package $target, string $version): bool
    {
        $sourceName = $source->getId();
        $targetName = $target->getId();
        $version = $version === '*' ? '' : $version;

        $pattern = "{$sourceName}=={$targetName}";

        if (!array_key_exists($pattern, $this->createdLinks)) {
            $sourceNode = $this->createNode($source);
            $targetNode = $this->createNode($target);
            $isSuggested = 'suggest' === $version;

            if (!$this->params->get('link-version')) {
                $version = '';
            }

            if ($source->isMain() && $target->isDirectPackage()) {
                $this->graphWrapper->addLink(new Link($sourceNode, $targetNode, $version, Link::THICK));
            } elseif ($source->isMain() && $target->isDirectPackageDev()) {
                $this->graphWrapper->addLink(new Link($sourceNode, $targetNode, $version, Link::THICK));
            } elseif ($isSuggested) {
                $this->graphWrapper->addLink(new Link($sourceNode, $targetNode, $version, Link::DOTTED));
            } else {
                $this->graphWrapper->addLink(new Link($sourceNode, $targetNode, $version, Link::ARROW));
            }

            $this->createdLinks[$pattern] = $version;
            return true;
        }

        return false;
    }

    /**
     * @param Package $package
     * @return Graph
     */
    private function getGraph(Package $package): Graph
    {
        if ($package->isMain()) {
            return $this->graphMain;
        }

        if ($package->isPlatform()) {
            return $this->graphPlatform;
        }

        if ($package->isTag(Package::DIRECT)) {
            if ($package->isTag(Package::REQUIRED) || $package->isTag(Package::SUGGEST)) {
                return $this->graphRequire;
            }

            if ($package->isTag(Package::REQUIRED_DEV)) {
                return $this->graphDev;
            }
        }

        if ($package->isTag(Package::REQUIRED)) {
            return $this->graphRequire;
        }

        if ($package->isTag(Package::REQUIRED_DEV)) {
            return $this->graphDev;
        }

        throw new Exception("Can't detect env for package: {$package->getName(false)}");
    }
}
