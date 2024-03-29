<?php

namespace Modules\Blog\Composers;

use Illuminate\Contracts\View\View;
use Modules\Core\Foundation\Theme\ThemeManager;
use Modules\Blog\Services\FinderService;

class TemplateViewComposer
{
    /**
     * @var ThemeManager
     */
    private ThemeManager $themeManager;

    /**
     * @var FinderService
     */
    private FinderService $finder;

    public function __construct(ThemeManager $themeManager, FinderService $finder)
    {
        $this->themeManager = $themeManager;
        $this->finder = $finder;
    }

    public function compose(View $view)
    {
        $view->with('all_templates_post', $this->getTemplatesPost());
        $view->with('all_templates_category', $this->getTemplatesCategory());
    }

    private function getTemplatesPost()
    {
        $path = $this->getCurrentThemeBasePath();

        $templates = [];

        foreach ($this->finder->excluding(config('encore.blog.config.template-ignored-directories', []))->allFiles($path . '/views/blog/post') as $template) {
            $relativePath = $template->getRelativePath();

            $templateName = $this->getTemplateName($template);
            $file = $this->removeExtensionsFromFilename($template);

            if ($this->hasSubdirectory($relativePath)) {
                $templates[str_replace('/', '.', $relativePath) . '.' . $file] = $templateName;
            } else {
                $templates[$file] = $templateName;
            }
        }

        return $templates;
    }
    private function getTemplatesCategory(): array
    {
        $path = $this->getCurrentThemeBasePath();

        $templates = [];

        foreach ($this->finder->excluding(config('encore.blog.config.template-ignored-directories', []))->allFiles($path . '/views/blog/category') as $template) {
            $relativePath = $template->getRelativePath();

            $templateName = $this->getTemplateName($template);
            $file = $this->removeExtensionsFromFilename($template);

            if ($this->hasSubdirectory($relativePath)) {
                $templates[str_replace('/', '.', $relativePath) . '.' . $file] = $templateName;
            } else {
                $templates[$file] = $templateName;
            }
        }

        return $templates;
    }

    /**
     * Get the base path of the current theme.
     *
     * @return string
     */
    private function getCurrentThemeBasePath(): string
    {
        return $this->themeManager->find(setting('core::template'))->getPath();
    }

    /**
     * Read template name defined in comments.
     *
     * @param $template
     *
     * @return string
     */
    private function getTemplateName($template): string
    {
        preg_match("/{{-- Template: (.*) --}}/", $template->getContents(), $templateName);

        if (count($templateName) > 1) {
            return $templateName[1];
        }

        return $this->getDefaultTemplateName($template);
    }

    /**
     * If the template name is not defined in comments, build a default.
     *
     * @param $template
     *
     * @return mixed
     */
    private function getDefaultTemplateName($template)
    {
        $relativePath = $template->getRelativePath();
        $fileName = $this->removeExtensionsFromFilename($template);

        return $this->hasSubdirectory($relativePath) ? $relativePath . '/' . $fileName : $fileName;
    }

    /**
     * Remove the extension from the filename.
     *
     * @param $template
     *
     * @return mixed
     */
    private function removeExtensionsFromFilename($template)
    {
        return str_replace('.blade.php', '', $template->getFilename());
    }

    /**
     * Check if the relative path is not empty (meaning the template is in a directory).
     *
     * @param $relativePath
     *
     * @return bool
     */
    private function hasSubdirectory($relativePath)
    {
        return ! empty($relativePath);
    }
}
