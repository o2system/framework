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
use O2System\Security\Protections\Csrf;
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

    // ------------------------------------------------------------------------

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
     * View::getPageFilePath
     *
     * @param string $filename
     *
     * @return string Returns FALSE if failed.
     */
    public function getPageFilePath($filename)
    {
        $filename = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filename);

        if (is_file($filename)) {
            return realpath($filename);
        } else {
            $pagesDirectories = [
                PATH_APP . 'Pages' . DIRECTORY_SEPARATOR,
                PATH_APP . 'Pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR,
                PATH_RESOURCES . 'pages' . DIRECTORY_SEPARATOR,
                PATH_RESOURCES . 'pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR,
            ];

            if(globals()->offsetExists('app') and globals()->app->getDirname() !== 'app') {
                array_unshift($pagesDirectories, globals()->app->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, globals()->app->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR);

                if(globals()->offsetExists('module')) {
                    array_unshift($pagesDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . DIRECTORY_SEPARATOR);
                    array_unshift($pagesDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . language()->getDefault() . DIRECTORY_SEPARATOR);
                    array_unshift($pagesDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
                    array_unshift($pagesDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'pages' . language()->getDefault() . DIRECTORY_SEPARATOR);
                }
            } elseif(globals()->offsetExists('module')) {
                array_unshift($pagesDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR);

                array_unshift($pagesDirectories, PATH_RESOURCES . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, PATH_RESOURCES . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'pages' . language()->getDefault() . DIRECTORY_SEPARATOR);
            }

            if(presenter()->theme) {
                array_unshift($pagesDirectories, presenter()->theme->getRealPath() . 'pages' . DIRECTORY_SEPARATOR . language()->getDefault() . DIRECTORY_SEPARATOR);
                array_unshift($pagesDirectories, presenter()->theme->getRealPath() . 'pages' . DIRECTORY_SEPARATOR);
            }

            foreach ($pagesDirectories as $pageDirectory) {
                foreach (['.phtml', '.php', '.html', '.vue', '.jsx'] as $pageExtension) {
                    // Find specific view file for mobile version
                    if (services('userAgent')->isMobile()) {
                        // Find without controller parameter as sub directory
                        if (is_file($filePath = $pageDirectory . $filename . '.mobile' . $pageExtension)) {
                            return realpath($filePath);
                            break; // break extension
                            break; // break directory
                        } elseif (is_file($filePath = $pageDirectory . $filename . DIRECTORY_SEPARATOR . 'index.mobile' . $pageExtension)) {
                            return realpath($filePath);
                            break; // break extension
                            break; // break directory
                        }
                    }

                    if (is_file($filePath = $pageDirectory . $filename . $pageExtension)) {
                        return realpath($filePath);
                        break; // break extension
                        break; // break directory
                    } elseif(is_file($filePath = $pageDirectory . $filename . DIRECTORY_SEPARATOR . 'index' . $pageExtension)) {
                        return realpath($filePath);
                        break; // break extension
                        break; // break directory
                    }
                }
            }
        }

        return false;
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
            $viewsDirectories = [
                PATH_APP . 'Views' . DIRECTORY_SEPARATOR,
                PATH_RESOURCES . 'views' . DIRECTORY_SEPARATOR,
                PATH_FRAMEWORK . 'Views' . DIRECTORY_SEPARATOR,
                PATH_KERNEL . 'Views' . DIRECTORY_SEPARATOR
            ];

            if(globals()->offsetExists('app') and globals()->app->getDirname() !== 'app') {
                array_unshift($viewsDirectories, globals()->app->getPath()  . DIRECTORY_SEPARATOR. 'Views' . DIRECTORY_SEPARATOR);
                array_unshift($viewsDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);

                if(globals()->offsetExists('module')) {
                    array_unshift($viewsDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Views' . DIRECTORY_SEPARATOR);
                    array_unshift($viewsDirectories, PATH_RESOURCES . globals()->app->getParameter() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
                }
            } elseif(globals()->offsetExists('module')) {
                array_unshift($viewsDirectories, globals()->module->getPath()  . DIRECTORY_SEPARATOR. 'Views' . DIRECTORY_SEPARATOR);
                array_unshift($viewsDirectories, PATH_RESOURCES . 'modules' . DIRECTORY_SEPARATOR . globals()->module->getParameter() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
            }

            if(presenter()->theme) {
                array_unshift($viewsDirectories, presenter()->theme->getRealPath() . 'views' . DIRECTORY_SEPARATOR);
            }

            $viewsExtensions = ['.phtml','.php'];

            foreach ($viewsDirectories as $viewDirectory) {
                foreach ($viewsExtensions as $viewExtension) {
                    // Find specific view file for mobile version
                    if (services('userAgent')->isMobile()) {
                        // Find without controller parameter as sub directory
                        if (is_file($filePath = $viewDirectory . $filename . '.mobile' . $viewExtension)) {
                            return realpath($filePath);
                            break; // break extension
                            break; // break directory
                        } elseif (is_file($filePath = $viewDirectory . $filename . DIRECTORY_SEPARATOR . 'index.mobile' . $viewExtension)) {
                            return realpath($filePath);
                            break; // break extension
                            break; // break directory
                        }
                    }

                    if (is_file($filePath = $viewDirectory . $filename . $viewExtension)) {
                        return realpath($filePath);
                        break; // break extension
                        break; // break directory
                    } elseif(is_file($filePath = $viewDirectory . $filename . DIRECTORY_SEPARATOR . 'index' . $viewExtension)) {
                        return realpath($filePath);
                        break; // break extension
                        break; // break directory
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
        if(input()->server('CONTENT_TYPE') == 'application/json' or input()->server('CONTENT_TYPE') == 'application/xml') {
            $result = presenter()->getArrayCopy();
            $removes = ['meta', 'page', 'assets', 'partials', 'widgets', 'theme', 'config', 'language', 'presenter', 'session', 'input'];

            foreach($removes as $key) {
                unset($result[$key]);
            }

            output()->send($result);

            exit(EXIT_SUCCESS);
        }

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
         * Injecting Meta CSRF
         */
        if(config()->security['protection']['csrf']) {
            $csrf = new Csrf();
            presenter()->meta->store('csrf-token', $csrf->getToken());
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

        if(controller()->getReflection()->hasMethod('output')) {
            controller()->getInstance()->output($this->document);
        }

        $htmlOutput = $this->document->saveHTML();

        // Uglify Output
        if ($this->config->output[ 'uglify' ] === true) {
            // remove html comments
            $htmlOutput = preg_replace('/<!--(.|\s)*?-->/', '', $htmlOutput);

            // remove CDATA
            $htmlOutput = preg_replace('~//<!\[CDATA\[\s*|\s*//\]\]>~', '', $htmlOutput);

            // remove javascripts comments
            $htmlOutput = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/[^"\'].*))/', '', $htmlOutput);

            /*
            * remove comments like this
            */
            $htmlOutput = preg_replace('/\/\*[^\/]*\*\//', '', $htmlOutput);

            /**
             * remove comments like this other method
             */
            $htmlOutput = preg_replace('/\/\*\*((\r\n|\n) \*[^\n]*)+(\r\n|\n) \*\//', '', $htmlOutput);

            // remove comments like this
            $htmlOutput = preg_replace('/\n(\s+)?\/\/[^\n]*/', '', $htmlOutput);

            //double spaces
            $htmlOutput = preg_replace('/ (\t| )+/', '', $htmlOutput);

            //double newlines
            $htmlOutput = preg_replace('/([\n])+/', "$1", $htmlOutput);

            // remove tabs, spaces, newlines, etc.
            $htmlOutput = str_replace([PHP_EOL, "\t"], '', $htmlOutput);

            //remove all spaces
            $htmlOutput = preg_replace('|\s\s+|', ' ', $htmlOutput);
        } elseif ($this->config->output[ 'beautify' ] === true) {
            $beautifier = new Html\Dom\Beautifier();
            $htmlOutput = $beautifier->format($htmlOutput);
        }

        if (profiler() !== false) {
            profiler()->watch('Ending View Rendering');
        }

        // Injecting Clickjacking Protection
        if(config()->security['protection']['clickjacking']) {
            output()->sendHeader('X-Frame-Options', 'ALLOW-FROM ' . domain_url());

            $securityPolicy = 'frame-ancestors ';

            if(server_request()->getUri()->domain->getNumOfTlds()) {
                $securityPolicy .= '*.' . server_request()->getUri()->domain->getMainDomain();
            } else {
                $securityPolicy .= "'self'";
            }

            output()->sendHeader('Content-Security-Policy', $securityPolicy);
        }

        return $htmlOutput;
    }
}