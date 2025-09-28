# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **jbzoo/composer-graph**, a PHP CLI tool that renders dependency graphs from composer.json and composer.lock files. It generates HTML visualizations using Mermaid.js to display package dependencies, PHP extensions, dev dependencies, and suggested packages.

## Development Commands

### Primary Commands
- `make update` - Install/update all dependencies using Composer
- `make build` - Install dependencies and build PHAR file
- `make test-all` - Run full test suite and code style checks
- `make test` - Run PHPUnit tests only
- `make codestyle` - Run code style checks and fixes

### Testing
- `vendor/bin/phpunit` - Run PHPUnit tests directly
- Tests are in `tests/` directory with fixtures in `tests/fixtures/`
- PHPUnit configuration in `phpunit.xml.dist`

### Code Quality Tools
The project uses `jbzoo/toolbox-dev` which provides various code quality tools:
- `vendor/bin/php-cs-fixer` - PHP CS Fixer for code formatting
- `vendor/bin/phan` - Static analysis
- Various other tools available in `vendor/bin/`

## Architecture

### Core Classes
- **`ComposerGraph`** (`src/ComposerGraph.php`) - Main class that builds dependency graphs using Mermaid.js
- **`Collection`** (`src/Collection.php`) - Manages packages and their relationships
- **`Package`** (`src/Package.php`) - Represents individual packages with dependencies
- **`Helper`** (`src/Helper.php`) - Utility functions for package processing
- **`Commands/Build`** (`src/Commands/Build.php`) - CLI command implementation using JBZoo CLI framework

### Key Dependencies
- **JBZoo Libraries**: Uses `jbzoo/cli`, `jbzoo/mermaid-php`, `jbzoo/data`, `jbzoo/utils`
- **Symfony Console**: For CLI interface (`symfony/console`)
- **Mermaid.js**: Graph visualization via `jbzoo/mermaid-php` wrapper

### Main Flow
1. Parse `composer.json` and `composer.lock` files from specified root directory
2. Build package collection with dependencies, dev dependencies, extensions
3. Generate Mermaid.js graph syntax with nodes and links
4. Output as HTML file with embedded Mermaid visualization or raw Mermaid format

### Binary Entry Points
- `composer-graph` - Shell script wrapper
- `composer-graph.php` - Main PHP entry point
- PHAR file is built for distribution

## File Structure
- `src/` - Main source code (PSR-4: JBZoo\ComposerGraph namespace)
- `tests/` - PHPUnit tests with fixture projects
- `resources/` - Example images and assets
- `build/` - Build artifacts and reports
- `Makefile` - Development commands (includes jbzoo/codestyle tasks)