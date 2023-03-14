<?php

/**
 * JBZoo Toolbox - Composer-Graph.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Composer-Graph
 */

declare(strict_types=1);

namespace JBZoo\ComposerGraph;

use JBZoo\Utils\Cli;

class Helper
{
    public const HASH_LENGTH = 7;

    /**
     * @codeCoverageIgnore
     * @phan-suppress PhanUnusedVariableCaughtException
     */
    public static function getGitVersion(): ?string
    {
        try {
            if ($tag = \trim(Cli::exec('git describe --abbrev=0 --tags'))) {
                return $tag;
            }
        } catch (\Exception $exception) {
            try {
                if ($branch = \trim(Cli::exec('git rev-parse --abbrev-ref HEAD'))) {
                    return "dev-{$branch}";
                }
            } catch (\Exception $exception) {
                try {
                    if ($commit = \trim(Cli::exec('git rev-parse HEAD'))) {
                        return \substr($commit, 0, self::HASH_LENGTH);
                    }
                } catch (\Exception $exception) {
                    return null;
                }
            }
        }

        return null;
    }
}
