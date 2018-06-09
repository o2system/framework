<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Cli\Commanders\Make;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commanders\Make;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Theme
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Theme extends Make
{
    public $optionName;

    /**
     * Module::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'CLI_MAKE_THEME_DESC';

    public function optionName($name)
    {
        if(empty($this->optionPath)) {
            $this->optionPath = PATH_PUBLIC . 'themes' . DIRECTORY_SEPARATOR;
        }

        $this->optionName = $name;
    }

    public function execute()
    {
        parent::execute();

        if (empty($this->optionName)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_THEME_E_NAME'))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        $themePath = $this->optionPath . dash($this->optionName) . DIRECTORY_SEPARATOR;

        if ( ! is_dir($themePath)) {
            mkdir($themePath, 0777, true);
        } else {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::DANGER)
                    ->setString(language()->getLine('CLI_MAKE_THEME_E_EXISTS', [$themePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_ERROR);
        }

        // Make default structure
        mkdir($themePath . 'assets' . DIRECTORY_SEPARATOR, 0777, true);

        foreach(['css','js', 'img', 'fonts', 'media', 'packages'] as $assetsDir) {
            mkdir($themePath . 'assets' . DIRECTORY_SEPARATOR . $assetsDir . DIRECTORY_SEPARATOR, 0777, true);
        }

        mkdir($themePath . 'partials' . DIRECTORY_SEPARATOR, 0777, true);

        $jsProps[ 'name' ] = readable(
            $this->optionName,
            true
        );

        $jsProps[ 'created' ] = date('d M Y');

        file_put_contents($themePath . 'theme.jsprop', json_encode($jsProps, JSON_PRETTY_PRINT));

        $themeTemplate = <<<THEME
<!DOCTYPE html>
<html>
<head>
    {{@assets->head}}
</head>
<body class="multipurpose">

<div id="page-content" class="page-content">
    {{@partials->content}}
</div>

{{@assets->body}}
</body>
</html>
THEME;

        file_put_contents($themePath . 'theme.phtml', str_replace('@','$',$themeTemplate));

        if (is_dir($themePath)) {
            output()->write(
                (new Format())
                    ->setContextualClass(Format::SUCCESS)
                    ->setString(language()->getLine('CLI_MAKE_THEME_S_MAKE', [$themePath]))
                    ->setNewLinesAfter(1)
            );

            exit(EXIT_SUCCESS);
        }
    }
}