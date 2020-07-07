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

use JBZoo\Utils\Cli;

/**
 * Class Helper
 * @package JBZoo\ComposerGraph
 */
class Helper
{
    public const HASH_LENGTH = 7;

    /**
     * @return string|null
     * @phan-suppress PhanUnusedVariableCaughtException
     */
    public static function getGitVersion(): ?string
    {
        try {
            if ($tag = trim(Cli::exec('git describe --abbrev=0 --tags'))) {
                return $tag;
            }
        } catch (\Exception $exception) {
            try {
                if ($branch = trim(Cli::exec('git rev-parse --abbrev-ref HEAD'))) {
                    return "dev-{$branch}";
                }
            } catch (\Exception $exception) {
                try {
                    if ($commit = trim(Cli::exec('git rev-parse HEAD'))) {
                        return substr($commit, 0, self::HASH_LENGTH);
                    }
                } catch (\Exception $exception) {
                    return null;
                }
            }
        }

        return null;
    }
}
