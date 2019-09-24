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
use O2System\Spl\Patterns\Structural\Composite\RenderableInterface;
use O2System\Spl\DataStructures\SplArrayObject;
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
        $this->setFileDirName('views');
        $this->addFilePath(PATH_RESOURCES);

        output()->addFilePath(PATH_RESOURCES);

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

        if ($content = $this->load($filename, $vars, true)) {
            echo $content;
            exit(EXIT_SUCCESS);
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

            // Load Assets
            presenter()->assets->addFilePath(dirname($filePath) . DIRECTORY_SEPARATOR);

            presenter()->assets->loadCss(pathinfo($filePath, PATHINFO_FILENAME));
            presenter()->assets->loadJs(pathinfo($filePath, PATHINFO_FILENAME));

            if ($return === false) {
                if (presenter()->partials->hasPartial('content') === false) {
                    if (is_ajax()) {
                        parser()->loadFile($filePath);
                        $content = parser()->parse(presenter()->getArrayCopy());

                        presenter()->partials->addPartial('content', $content);
                    } else {
                        presenter()->partials->addPartial('content', $filePath);
                    }
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
            $pageDirectories = modules()->getResourcesDirs('pages');
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
            $viewsDirectories = array_merge([
                PATH_KERNEL . 'Views' . DIRECTORY_SEPARATOR,
                PATH_FRAMEWORK . 'Views' . DIRECTORY_SEPARATOR,
            ], $this->filePaths);

            $viewsDirectories = array_unique($viewsDirectories);
            $viewsDirectories = array_reverse($viewsDirectories);

            $controllerSubDir = null;
            if($controller = services('controller')) {
                $controllerSubDir = services('controller')->getParameter() . DIRECTORY_SEPARATOR;
            }

            foreach ($viewsDirectories as $viewsDirectory) {
                $filename = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filename);

                // Find specific view file for mobile version
                if (services('userAgent')->isMobile()) {
                    // Find without controller parameter as sub directory
                    if (is_file($filePath = $viewsDirectory . $filename . '.mobile.phtml')) {
                        return realpath($filePath);
                        break;
                    }

                    // Find without controller parameter as sub directory
                    if (is_file($filePath = $viewsDirectory . $controllerSubDir . $filename . '.mobile.phtml')) {
                        return realpath($filePath);
                        break;
                    }
                }

                // Find without controller parameter as sub directory
                if (is_file($filePath = $viewsDirectory . $filename . '.phtml')) {
                    return realpath($filePath);
                    break;
                }

                // Find without controller parameter as sub directory
                if (is_file($filePath = $viewsDirectory . $controllerSubDir . $filename . '.phtml')) {
                    return realpath($filePath);
                    break;
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

        if (presenter()->page->file instanceof \SplFileInfo) {

            if (false === ($pagePresets = presenter()->page->getPresets())) {
                if (presenter()->page->file->getFilename() === 'index') {
                    $title = presenter()->page->file->getDirectoryInfo()->getDirName();
                } else {
                    $titles[] = presenter()->page->file->getDirectoryInfo()->getDirName();
                    $titles[] = presenter()->page->file->getFilename();

                    $title = implode(' - ', array_unique($titles));
                }

                $pagePresets = new SplArrayObject([
                    'title'  => readable($title, true),
                    'access' => 'public',
                ]);
            }

            /**
             * Sets Page Theme
             */
            if ($pagePresets->offsetExists('theme')) {
                presenter()->setTheme($pagePresets->theme);
            } elseif (false !== ($theme = presenter()->getConfig('theme'))) {
                if (modules()->top()->hasTheme($theme)) {
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

                /**
                 * Autoload Theme Assets
                 */
                presenter()->theme->load();

                if (false !== ($modulePresets = modules()->top()->getPresets())) {

                    /**
                     * Autoload Module Assets
                     */
                    if ($modulePresets->offsetExists('assets')) {
                        presenter()->assets->autoload($modulePresets->assets);
                    }

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

                $moduleAssets = [
                    'app',
                    'module',
                    modules()->top()->getParameter(),
                ];

                // Autoload Assets
                presenter()->assets->loadCss($moduleAssets);
                presenter()->assets->loadJs($moduleAssets);

                /**
                 * Autoload Page Assets
                 */
                if ($pagePresets->offsetExists('assets')) {
                    presenter()->assets->autoload($pagePresets->assets);
                }

                if (presenter()->page->file instanceof \SplFileInfo) {
                    $pageDir = presenter()->page->file->getRealPath();
                    $pageDir = str_replace('.' . pathinfo($pageDir, PATHINFO_EXTENSION), '', $pageDir);

                    $pageDirParts = explode('pages' . DIRECTORY_SEPARATOR,
                        str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pageDir));
                    $pageDir = end($pageDirParts);

                    presenter()->assets->addFilePath(reset($pageDirParts) . 'pages' . DIRECTORY_SEPARATOR);

                    $pageDir = rtrim($pageDir, DIRECTORY_SEPARATOR);
                    $pageDirParts = explode(DIRECTORY_SEPARATOR, $pageDir);

                    $totalParts = count($pageDirParts);

                    for ($i = 0; $i < $totalParts; $i++) {
                        $pageAssets[] = implode(DIRECTORY_SEPARATOR, array_slice($pageDirParts, 0, ($totalParts - $i)));
                    }

                    $pageAssets[] = implode(DIRECTORY_SEPARATOR, [end($pageAssets), end($pageAssets)]);

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
             * Autoload Theme Assets
             */
            presenter()->theme->load();

            if (false !== ($modulePresets = modules()->top()->getPresets())) {

                /**
                 * Autoload Module Assets
                 */
                if ($modulePresets->offsetExists('assets')) {
                    presenter()->assets->autoload($modulePresets->assets);
                }

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

            $moduleAssets = [
                'app',
                'module',
                modules()->top()->getParameter(),
            ];

            // Autoload Assets
            presenter()->assets->loadCss($moduleAssets);
            presenter()->assets->loadJs($moduleAssets);

            /**
             * Autoload Controller Assets
             */
            $controllerFilename = str_replace([modules()->top()->getDir('Controllers'), '.php'], '',
                controller()->getFileInfo()->getRealPath());
            $controllerFilename = dash($controllerFilename);
            $controllerAssets[] = $controllerFilename;
            $controllerAssets[] = implode('/', [
                $controllerFilename,
                controller()->getRequestMethod(),
            ]);

            presenter()->assets->loadCss($controllerAssets);
            presenter()->assets->loadJs($controllerAssets);

            if (false !== ($layout = presenter()->theme->getLayout())) {
                parser()->loadFile($layout->getRealPath());

                $htmlOutput = parser()->parse();
                $htmlOutput = presenter()->assets->parseSourceCode($htmlOutput);

                $this->document->loadHTML($htmlOutput);
            }
        } elseif (false !== ($theme = presenter()->getConfig('theme'))) {
            if (modules()->top()->hasTheme($theme)) {
                presenter()->setTheme($theme);

                /**
                 * Autoload Theme Assets
                 */
                presenter()->theme->load();

                if (false !== ($modulePresets = modules()->top()->getPresets())) {

                    /**
                     * Autoload Module Assets
                     */
                    if ($modulePresets->offsetExists('assets')) {
                        presenter()->assets->autoload($modulePresets->assets);
                    }

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

                $moduleAssets = [
                    'app',
                    'module',
                    modules()->top()->getParameter(),
                ];

                // Autoload Assets
                presenter()->assets->loadCss($moduleAssets);
                presenter()->assets->loadJs($moduleAssets);

                /**
                 * Autoload Controller Assets
                 */
                $controllerFilename = str_replace([modules()->top()->getDir('Controllers'), '.php'], '',
                    controller()->getFileInfo()->getRealPath());
                $controllerFilename = dash($controllerFilename);
                $controllerAssets[] = $controllerFilename;
                $controllerAssets[] = implode('/', [
                    $controllerFilename,
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
         * Inject body attributes
         */
        $body = $this->document->getElementsByTagName('body');
        $body->item(0)->setAttribute('module', modules()->top()->getParameter());

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
            presenter()->getConfig('debugToolBar') === true and
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