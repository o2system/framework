<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Framework\Containers\Modules\DataStructures\Module\Theme;
use O2System\Framework\Http\Presenter\Meta;
use O2System\Gear\Toolbar;
use O2System\Html;
use O2System\Psr\Patterns\Structural\Composite\RenderableInterface;
use O2System\Spl\Exceptions\ErrorException;
use O2System\Spl\Traits\Collectors\FileExtensionCollectorTrait;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class View
 *
 * @package O2System
 */
class View implements RenderableInterface
{
    use FilePathCollectorTrait;
    use FileExtensionCollectorTrait;

    /**
     * View Config
     *
     * @var \O2System\Kernel\DataStructures\Config
     */
    protected $config;

    /**
     * View HTML Document
     *
     * @var Html\Document
     */
    protected $document;

    // ------------------------------------------------------------------------

    /**
     * View::__construct
     *
     * @return View
     */
    public function __construct()
    {
        $this->setFileDirName('Views');
        $this->addFilePath(PATH_APP);

        output()->addFilePath(PATH_APP);

        $this->config = config()->loadFile('view', true);

        $this->setFileExtensions(
            [
                '.php',
                '.phtml',
            ]
        );

        if ($this->config->offsetExists('extensions')) {
            $this->setFileExtensions($this->config[ 'extensions' ]);
        }

        $this->document = new Html\Document();
        $this->document->formatOutput = (bool)$this->config->beautify;
    }

    /**
     * View::__get
     *
     * @param string $property
     *
     * @return bool Returns FALSE when property is not set.
     */
    public function &__get($property)
    {
        $get[ $property ] = false;

        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    // ------------------------------------------------------------------------

    /**
     * View::parse
     *
     * @param string $string
     * @param array  $vars
     *
     * @return bool|string Returns FALSE if failed.
     */
    public function parse($string, array $vars = [])
    {
        parser()->loadString($string);

        return parser()->parse($vars);
    }

    // ------------------------------------------------------------------------

    /**
     * View::with
     *
     * @param mixed $vars
     * @param mixed $value
     *
     * @return static
     */
    public function with($vars, $value = null)
    {
        if (is_string($vars)) {
            $vars = [$vars => $value];
        }

        presenter()->merge($vars);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * View::modal
     *
     * @param string $filename
     * @param array  $vars
     */
    public function modal($filename, array $vars = [])
    {
        if (presenter()->theme->hasLayout('modal')) {
            if (presenter()->theme->hasLayout('modal')) {
                presenter()->theme->setLayout('modal');
                echo $this->load($filename, $vars, true);
                exit(EXIT_SUCCESS);
            }
        }

        presenter()->merge($vars);

        if (parser()->loadFile($filename)) {
            output()->send(parser()->parse(presenter()->getArrayCopy()));
        }
    }

    // ------------------------------------------------------------------------

    /**
     * View::load
     *
     * @param string $filename
     * @param array  $vars
     * @param bool   $return
     *
     * @return false|string
     */
    public function load($filename, array $vars = [], $return = false)
    {
        if ($filename instanceof \SplFileInfo) {
            return $this->page($filename->getRealPath(), array_merge($vars, $filename->getVars()));
        }

        if (strpos($filename, 'Pages') !== false) {
            return $this->page($filename, $vars, $return);
        }

        presenter()->merge($vars);

        if (false !== ($filePath = $this->getFilePath($filename))) {
            if ($return === false) {
                if (presenter()->partials->hasPartial('content') === false) {
                    presenter()->partials->addPartial('content', $filePath);
                } else {
                    presenter()->partials->addPartial(pathinfo($filePath, PATHINFO_FILENAME), $filePath);
                }
            } else {
                parser()->loadFile($filePath);

                return parser()->parse(presenter()->getArrayCopy());
            }
        } else {
            $vars = presenter()->getArrayCopy();
            extract($vars);

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            $error = new ErrorException(
                'E_VIEW_NOT_FOUND',
                0,
                @$backtrace[ 0 ][ 'file' ],
                @$backtrace[ 0 ][ 'line' ],
                [trim($filename)]
            );

            unset($backtrace);

            ob_start();
            include output()->getFilePath('error');
            $content = ob_get_contents();
            ob_end_clean();

            if ($return === false) {
                if (presenter()->partials->hasPartial('content') === false) {
                    presenter()->addPartial('content', $content);
                } else {
                    presenter()->addPartial(pathinfo($filePath, PATHINFO_FILENAME), $content);
                }
            } else {
                return $content;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * View::page
     *
     * @param string $filename
     * @param array  $vars
     * @param bool   $return
     *
     * @return bool|string Returns FALSE if failed.
     */
    public function page($filename, array $vars = [], $return = false)
    {
        if ( ! is_file($filename)) {
            $pageDirectories = modules()->getDirs('Pages');
            foreach ($pageDirectories as $pageDirectory) {
                if (is_file($pageFilePath = $pageDirectory . $filename . '.phtml')) {
                    $filename = $pageFilePath;
                    break;
                }
            }
        }

        if (count($vars)) {
            presenter()->merge($vars);
        }

        presenter()->merge(presenter()->page->getVars());

        if ($return === false) {
            if (presenter()->partials->hasPartial('content') === false) {
                presenter()->partials->addPartial('content', $filename);
            } else {
                presenter()->partials->addPartial(pathinfo($filename, PATHINFO_FILENAME), $filename);
            }
        } elseif (parser()->loadFile($filename)) {
            return parser()->parse(presenter()->getArrayCopy());
        }
    }

    // ------------------------------------------------------------------------

    /**
     * View::getFilePath
     *
     * @param string $filename
     *
     * @return bool|string
     */
    public function getFilePath($filename)
    {
        $filename = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filename);

        if (is_file($filename)) {
            return realpath($filename);
        } else {
            $viewsFileExtensions = $this->fileExtensions;
            $viewsDirectories = modules()->getDirs('Views');
            $viewsDirectories = array_merge($viewsDirectories, $this->filePaths);
            $viewsDirectories = array_unique($viewsDirectories);

            $deviceDirectory = null;
            if (services('userAgent')->isMobile()) {
                $deviceDirectory = 'mobile';
            }

            if (presenter()->theme) {
                $moduleReplacementPath = presenter()->theme->getPathName()
                    . DIRECTORY_SEPARATOR
                    . 'views'
                    . DIRECTORY_SEPARATOR
                    . strtolower(
                        str_replace(PATH_APP, '', modules()->current()->getRealpath())
                    );

                if (is_dir($moduleReplacementPath)) {
                    array_unshift($viewsDirectories, $moduleReplacementPath);

                    // Add Theme File Extensions
                    if (presenter()->theme->getPresets()->offsetExists('extension')) {
                        array_unshift($viewsFileExtensions,
                            presenter()->theme->getPresets()->offsetGet('extension'));
                    } elseif (presenter()->theme->getPresets()->offsetExists('extensions')) {
                        $viewsFileExtensions = array_merge(
                            presenter()->theme->getPresets()->offsetGet('extensions'),
                            $viewsFileExtensions
                        );
                    }

                    // Add Theme Parser Engine
                    if (presenter()->theme->getPresets()->offsetExists('driver')) {
                        $parserDriverClassName = '\O2System\Parser\Drivers\\' . camelcase(
                                presenter()->theme->getPresets()->offsetGet('driver')
                            );

                        if (class_exists($parserDriverClassName)) {
                            parser()->addDriver(
                                new $parserDriverClassName(),
                                presenter()->theme->getPresets()->offsetGet('driver')
                            );
                        }
                    }
                }
            }

            foreach ($viewsDirectories as $viewsDirectory) {
                foreach ($viewsFileExtensions as $fileExtension) {
                    $filename = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filename);

                    if (is_file($filePath = $viewsDirectory . $filename . $fileExtension)) {
                        return realpath($filePath);
                        break;
                    }
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * View::render
     *
     * @param array $options
     *
     * @return string
     */
    public function render(array $options = [])
    {
        if (profiler() !== false) {
            profiler()->watch('Starting View Rendering');
        }

        parser()->loadVars(presenter()->getArrayCopy());

        if (false !== ($pagePresets = presenter()->page->getPresets())) {
            /**
             * Sets Page Theme
             */
            if ($pagePresets->offsetExists('theme')) {
                presenter()->setTheme($pagePresets->theme);
            } elseif (false !== ($theme = presenter()->getConfig('theme'))) {
                if (modules()->current()->hasTheme($theme)) {
                    presenter()->setTheme($theme);
                }
            }

            /**
             * Sets Page Layout
             */
            if (presenter()->theme !== false) {
                if ($pagePresets->offsetExists('layout')) {
                    presenter()->theme->setLayout($pagePresets->layout);
                }

                if (false !== ($modulePresets = modules()->current()->getPresets())) {

                    /**
                     * Autoload Module Assets
                     */
                    if ($modulePresets->offsetExists('assets')) {
                        presenter()->assets->autoload($modulePresets->assets);
                    }

                    $moduleAssets = [
                        'main',
                        modules()->current()->getParameter()
                    ];

                    // Autoload Assets
                    presenter()->assets->loadCss($moduleAssets);
                    presenter()->assets->loadJs($moduleAssets);

                    /**
                     * Sets Module Meta
                     */
                    if ($modulePresets->offsetExists('title')) {
                        presenter()->meta->title->append(language()->getLine($modulePresets->title));
                    }

                    if ($modulePresets->offsetExists('pageTitle')) {
                        presenter()->meta->title->replace(language()->getLine($modulePresets->pageTitle));
                    }

                    if ($modulePresets->offsetExists('browserTitle')) {
                        presenter()->meta->title->replace(language()->getLine($modulePresets->browserTitle));
                    }

                    if ($modulePresets->offsetExists('meta')) {
                        foreach ($modulePresets->meta as $name => $content) {
                            presenter()->meta->store($name, $content);
                        }
                    }
                }

                /**
                 * Autoload Page Assets
                 */
                if ($pagePresets->offsetExists('assets')) {
                    presenter()->assets->autoload($pagePresets->assets);
                }

                if (presenter()->page->file instanceof \SplFileInfo) {
                    $pageAssets[] = presenter()->page->file->getDirectoryInfo()->getDirName();
                    $pageAssets[] = implode('/', [
                        presenter()->page->file->getDirectoryInfo()->getDirName(),
                        $pageAssets[] = presenter()->page->file->getFilename(),
                    ]);

                    // Autoload Assets
                    presenter()->assets->loadCss($pageAssets);
                    presenter()->assets->loadJs($pageAssets);
                }

                /**
                 * Sets Page Meta
                 */
                if ($pagePresets->offsetExists('title')) {
                    presenter()->meta->title->append(language()->getLine($pagePresets->title));
                }

                if ($pagePresets->offsetExists('pageTitle')) {
                    presenter()->meta->title->replace(language()->getLine($pagePresets->pageTitle));
                }

                if ($pagePresets->offsetExists('browserTitle')) {
                    presenter()->meta->title->replace(language()->getLine($pagePresets->browserTitle));
                }

                if ($pagePresets->offsetExists('meta')) {
                    foreach ($pagePresets->meta as $name => $content) {
                        presenter()->meta->store($name, $content);
                    }
                }

                if (false !== ($layout = presenter()->theme->getLayout())) {
                    parser()->loadFile($layout->getRealPath());

                    $htmlOutput = parser()->parse();
                    $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);

                    $this->document->loadHTML($htmlOutput);
                }
            } else {
                output()->sendError(204, language()->getLine('E_THEME_NOT_FOUND', [$theme]));
            }
        } elseif (presenter()->theme instanceof Theme) {
            /**
             * Autoload Controller Assets
             */
            $controllerAssets[] = controller()->getParameter();
            $controllerAssets[] = implode('/', [
                controller()->getParameter(),
                controller()->getRequestMethod(),
            ]);

            if (false !== ($layout = presenter()->theme->getLayout())) {
                parser()->loadFile($layout->getRealPath());

                $htmlOutput = parser()->parse();
                $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);

                $this->document->loadHTML($htmlOutput);
            }
        } elseif (false !== ($theme = presenter()->getConfig('theme'))) {
            if (modules()->current()->hasTheme($theme)) {
                presenter()->setTheme($theme);

                /**
                 * Autoload Controller Assets
                 */
                $controllerAssets[] = controller()->getParameter();
                $controllerAssets[] = implode('/', [
                    controller()->getParameter(),
                    controller()->getRequestMethod(),
                ]);

                if (false !== ($layout = presenter()->theme->getLayout())) {
                    parser()->loadFile($layout->getRealPath());

                    $htmlOutput = parser()->parse();
                    $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);

                    $this->document->loadHTML($htmlOutput);
                }
            } else {
                output()->sendError(204, language()->getLine('E_THEME_NOT_FOUND', [$theme]));
            }
        } else {
            $this->document->find('body')->append(presenter()->partials->__get('content'));
        }

        /**
         * Set Document Meta Title
         */
        if (presenter()->meta->title instanceof Meta\Title) {
            $this->document->title->text(presenter()->meta->title->__toString());
        }

        /**
         * Injecting Meta Opengraph
         */
        if (presenter()->meta->opengraph instanceof Meta\Opengraph) {
            // set opengraph title
            if (presenter()->meta->title instanceof Meta\Title) {
                presenter()->meta->opengraph->setTitle(presenter()->meta->title->__toString());
            }

            // set opengraph site name
            if (presenter()->exists('siteName')) {
                presenter()->meta->opengraph->setSiteName(presenter()->offsetGet('siteName'));
            }

            if (presenter()->meta->opengraph->count()) {
                $htmlElement = $this->document->getElementsByTagName('html')->item(0);
                $htmlElement->setAttribute('prefix', 'og: ' . presenter()->meta->opengraph->prefix);

                if (presenter()->meta->opengraph->exists('og:type') === false) {
                    presenter()->meta->opengraph->setType('website');
                }

                $opengraph = presenter()->meta->opengraph->getArrayCopy();

                foreach ($opengraph as $tag) {
                    $this->document->metaNodes->createElement($tag->attributes->getArrayCopy());
                }
            }
        }

        if (presenter()->meta->count()) {
            $meta = presenter()->meta->getArrayCopy();

            foreach ($meta as $tag) {
                $this->document->metaNodes->createElement($tag->attributes->getArrayCopy());
            }
        }

        /**
         * Injecting Single Sign-On (SSO) iFrame
         */
        if (services()->has('user')) {
            $iframe = services()->get('user')->getIframeCode();

            if ( ! empty($iframe)) {
                $this->document->find('body')->append($iframe);
            }
        }

        if (input()->env('DEBUG_STAGE') === 'DEVELOPER' and
            config()->getItem('presenter')->debugToolBar === true and
            services()->has('profiler')
        ) {
            $this->document->find('body')->append((new Toolbar())->__toString());
        }

        /**
         * Injecting Progressive Web Application (PWA) Manifest
         */
        $this->document->linkNodes->createElement([
            'rel'  => 'manifest',
            'href' => '/manifest.json',
        ]);

        $htmlOutput = $this->document->saveHTML();

        // Uglify Output
        if ($this->config->output[ 'uglify' ] === true) {
            $htmlOutput = preg_replace(
                [
                    '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                    '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                    '/(\s)+/s',         // shorten multiple whitespace sequences
                    '/<!--(.|\s)*?-->/', // Remove HTML comments
                    '/<!--(.*)-->/Uis',
                    "/[[:blank:]]+/",
                ],
                [
                    '>',
                    '<',
                    '\\1',
                    '',
                    '',
                    ' ',
                ],
                str_replace(["\n", "\r", "\t"], '', $htmlOutput));
        }

        // Beautify Output
        if ($this->config->output[ 'beautify' ] === true) {
            $beautifier = new Html\Dom\Beautifier();
            $htmlOutput = $beautifier->format($htmlOutput);
        }

        if (profiler() !== false) {
            profiler()->watch('Ending View Rendering');
        }

        return $htmlOutput;
    }
}
