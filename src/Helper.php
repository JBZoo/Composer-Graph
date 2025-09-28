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

final class Helper
{
    public const HASH_LENGTH = 7;

    public static function getGitVersion(): ?string
    {
        try {
            $tag = \trim(Cli::exec('git describe --abbrev=0 --tags'));
            if ($tag !== '') {
                return $tag;
            }
        } catch (\Exception) {
            try {
                $branch = \trim(Cli::exec('git rev-parse --abbrev-ref HEAD'));
                if ($branch !== '') {
                    return "dev-{$branch}";
                }
            } catch (\Exception) {
                try {
                    $commit = \trim(Cli::exec('git rev-parse HEAD'));
                    if ($commit !== '') {
                        return \substr($commit, 0, self::HASH_LENGTH);
                    }
                } catch (\Exception) {
                    return null;
                }
            }
        }

        return null;
    }
}
