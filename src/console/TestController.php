<?php

namespace markhuot\craftpest\console;

use craft\console\Controller;
use craft\helpers\FileHelper;
use Symfony\Component\Process\Process;
use yii\console\ExitCode;
use function markhuot\craftpest\helpers\base\version_greater_than_or_equal_to;

class TestController extends Controller {

    /**
     * Run the Pest tests
     */
    function actionIndex() {
        $this->runInit();
        $this->runTests();
        return ExitCode::OK;
    }

    /**
     * Install Pest
     */
    function actionInit() {
        $this->runInit();
        return ExitCode::OK;
    }

    /**
     * Do the install
     */
    protected function runInit() {
        if (!file_exists(CRAFT_BASE_PATH . '/phpunit.xml')) {
            $process = new Process(['./vendor/bin/pest', '--init']);
            $process->setTty(true);
            $process->start();

            foreach ($process as $type => $data) {
                if ($type === $process::OUT) {
                    echo $data;
                } else {
                    echo $data;
                }
            }

            copy(__DIR__ . DIRECTORY_SEPARATOR . '../../stubs/init/ExampleTest.php', './tests/ExampleTest.php');
            copy(__DIR__ . DIRECTORY_SEPARATOR . '../../stubs/init/Pest.php', './tests/Pest.php');
        }
    }

    /**
     * Run the tests
     */
    protected function runTests() {
        $process = new Process(['./vendor/bin/pest']);
        $process->setTty(true);
        $process->setTimeout(null);
        $process->start();

        foreach ($process as $type => $data) {
            if ($type === $process::OUT) {
                echo $data;
            } else {
                echo $data;
            }
        }
    }

    function actionCompileTemplates()
    {
        $compiledTemplatesDir = \Craft::$app->path->getCompiledTemplatesPath();
        FileHelper::removeDirectory($compiledTemplatesDir);

        $compileTemplates = function ($path, $base='')
        {
            if (!is_string($path)) {
                return;
            }

            $directory = new \RecursiveDirectoryIterator($path);
            $iterator = new \RecursiveIteratorIterator($directory);
            $regex = new \RegexIterator($iterator, '/^.+\.(html|twig)$/i', \RecursiveRegexIterator::GET_MATCH);
            foreach ($regex as $match) {
                $logicalName = ltrim(substr($match[0], strlen($path)), '/');
                if ($logicalName === 'index.twig' || $logicalName === 'index.html') {
                    $logicalName = '';
                }
                $oldTemplateMode = \Craft::$app->view->getTemplateMode();
                \Craft::$app->view->setTemplateMode('site');
                $twig = \Craft::$app->view->twig;
                if (version_greater_than_or_equal_to(\Craft::$app->version, '3')) {
                    $twig->loadTemplate($logicalName);
                }
                else if (version_greater_than_or_equal_to(\Craft::$app->version, '4')) {
                    $twig->loadTemplate($twig->getTemplateClass($logicalName), $logicalName);
                }
                \Craft::$app->view->setTemplateMode($oldTemplateMode);
            }
        };

        // // Site specific templates
        // foreach (\Craft::$app->sites->getAllSites() as $site) {
        //     $sitePath = implode(DIRECTORY_SEPARATOR, [CRAFT_BASE_PATH, 'templates', $site->handle]);
        //     if (is_dir($sitePath)) {
        //         $compileTemplates($sitePath);
        //     }
        // }
        //
        // // Template Alias
        // $aliasPath = \Craft::getAlias('@templates');
        // if (is_dir($aliasPath)) {
        //     $compileTemplates($aliasPath);
        // }
        //
        // // Template roots
        // foreach (array_filter(array_merge([
        //     \Craft::$app->view->getSiteTemplateRoots(),
        //     \Craft::$app->view->getCpTemplateRoots(),
        // ])) as $templateRoot => $basePath) {
        //     $compileTemplates($basePath, $templateRoot);
        // }

        // hack
        $compileTemplates(\Craft::getAlias('@templates'));

        return 0;
    }
}
