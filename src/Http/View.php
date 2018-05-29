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

namespace O2System\Framework\Http;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Framework\Http\Presenter\Meta;
use O2System\Framework\Http\Router\Datastructures\Page;
use O2System\Gear\Toolbar;
use O2System\Html;
use O2System\Psr\Cache\CacheItemPoolInterface;
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
     * @var \O2System\Kernel\Datastructures\Config
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
     * __get
     *
     * @param $property
     *
     * @return Parser|bool   Returns FALSE when property is not set.
     */
    public function &__get($property)
    {
        $get[ $property ] = false;

        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        return $get[ $property ];
    }

    public function parse($string, array $vars = [])
    {
        parser()->loadString($string);

        return parser()->parse($vars);
    }

    public function with($vars, $value = null)
    {
        if (is_string($vars)) {
            $vars = [$vars => $value];
        }

        presenter()->merge($vars);

        return $this;
    }

    public function load($filename, array $vars = [], $return = false)
    {
        if ($filename instanceof Page) {
            return $this->page($filename->getRealPath(), array_merge($vars, $filename->getVars()));
        }

        if (strpos($filename, 'Pages') !== false) {
            return $this->page($filename, $vars, $return);
        }

        presenter()->merge($vars);

        if (false !== ($filePath = $this->getFilePath($filename))) {
            if ($return === false) {

                $partials = presenter()->get('partials');

                if ($partials->hasPartial('content') === false) {
                    $partials->addPartial('content', $filePath);
                } else {
                    $partials->addPartial(pathinfo($filePath, PATHINFO_FILENAME), $filePath);
                }
            } else {
                parser()->loadFile($filePath);

                return parser()->parse(presenter()->getArrayCopy());
            }
        } else {

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
            include PATH_KERNEL . 'Views' . DIRECTORY_SEPARATOR . 'error.phtml';
            $content = ob_get_contents();
            ob_end_clean();

            if ($return === false) {

                $partials = presenter()->get('partials');

                if ($partials->hasPartial('content') === false) {
                    $partials->addPartial('content', $content);
                } else {
                    $partials->addPartial(pathinfo($filePath, PATHINFO_FILENAME), $content);
                }

            } else {
                return $content;
            }
        }
    }

    public function page($filename, array $vars = [], $return = false)
    {
        if ($filename instanceof Page) {
            return $this->page($filename->getRealPath(), array_merge($vars, $filename->getVars()));
        } else {
            $pageDirectories = modules()->getDirs('Pages');
            foreach ($pageDirectories as $pageDirectory) {
                if (is_file($pageFilePath = $pageDirectory . $filename . '.phtml')) {
                    $filename = $pageFilePath;
                    break;
                }
            }
        }

        presenter()->merge($vars);

        if ($return === false) {
            $partials = presenter()->get('partials');

            if ($partials->hasPartial('content') === false) {
                $partials->addPartial('content', $filename);
            } else {
                $partials->addPartial(pathinfo($filename, PATHINFO_FILENAME), $filename);
            }
        } elseif (parser()->loadFile($filename)) {
            return parser()->parse(presenter()->getArrayCopy());
        }
    }

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

            if (presenter()->theme->use === true) {

                $moduleReplacementPath = presenter()->theme->active->getPathName()
                    . DIRECTORY_SEPARATOR
                    . 'views'
                    . DIRECTORY_SEPARATOR
                    . strtolower(
                        str_replace(PATH_APP, '', modules()->current()->getRealpath())
                    );

                if (is_dir($moduleReplacementPath)) {
                    array_unshift($viewsDirectories, $moduleReplacementPath);

                    // Add Theme File Extensions
                    if (presenter()->theme->active->getConfig()->offsetExists('extension')) {
                        array_unshift($viewsFileExtensions,
                            presenter()->theme->active->getConfig()->offsetGet('extension'));
                    } elseif (presenter()->theme->active->getConfig()->offsetExists('extensions')) {
                        $viewsFileExtensions = array_merge(
                            presenter()->theme->active->getConfig()->offsetGet('extensions'),
                            $viewsFileExtensions
                        );
                    }

                    // Add Theme Parser Engine
                    if (presenter()->theme->active->getConfig()->offsetExists('driver')) {
                        $parserDriverClassName = '\O2System\Parser\Drivers\\' . camelcase(
                                presenter()->theme->active->getConfig()->offsetGet('driver')
                            );

                        if (class_exists($parserDriverClassName)) {
                            parser()->addDriver(
                                new $parserDriverClassName(),
                                presenter()->theme->active->getConfig()->offsetGet('driver')
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

    public function render(array $options = [])
    {
        $htmlOutput = '';
        parser()->loadVars(presenter()->getArrayCopy());

        // set document meta title
        if (presenter()->meta->title instanceof Meta\Title) {
            $this->document->title->text(presenter()->meta->title->__toString());
        }

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

        if (false !== ($controller = controller())) {
            presenter()->meta->offsetSet('module-controller', $controller->getClassInfo()->getParameter());
        }

        $meta = presenter()->meta->getArrayCopy();

        foreach ($meta as $tag) {
            $this->document->metaNodes->createElement($tag->attributes->getArrayCopy());
        }

        if (presenter()->theme->use === true) {
            presenter()->theme->load();
            if (false !== ($layout = presenter()->theme->active->getLayout())) {
                parser()->loadFile($layout->getRealPath());
                $htmlOutput = parser()->parse();
            }
        } else {
            $this->document->find('body')->append(presenter()->partials->__get('content'));
        }

        $this->document->loadHTML(presenter()->assets->parseSourceCode($htmlOutput));

        // Single-Sign-On iFrame
        if (o2system()->hasService('user')) {
            $iframe = o2system()->getService('user')->getIframeCode();

            if ( ! empty($iframe)) {
                $this->document->find('body')->append($iframe);
            }
        }

        profiler()->watch('VIEW_RENDER_END');

        if (input()->env('DEBUG_STAGE') === 'DEVELOPER' and config()->getItem('presenter')->debugToolBar === true) {
            $this->document->find('body')->append((new Toolbar())->__toString());
        }

        $htmlOutput = $this->document->saveHTML();

        if (presenter()->offsetExists('cacheOutput')) {
            $cacheKey = 'o2output_' . underscore(server_request()->getUri()->getSegments()->getString());

            $cacheItemPool = cache()->getItemPool('default');

            if (cache()->hasItemPool('output')) {
                $cacheItemPool = cache()->getItemPool('output');
            }

            if ($cacheItemPool instanceof CacheItemPoolInterface) {
                if (presenter()->cacheOutput > 0) {
                    $cacheItemPool->save(new Item($cacheKey, $htmlOutput, presenter()->cacheOutput));
                }
            }
        }

        // Minify Output
        if ($this->config->output->minify === true) {
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
        if ($this->config->output->minify === false && $this->config->output->beautify === true) {
            $beautifier = new Html\Dom\Beautifier();
            $htmlOutput = $beautifier->format($htmlOutput);
        }

        output()->send($htmlOutput);
    }
}