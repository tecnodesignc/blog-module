<?php

namespace Modules\Blog\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Page\Services\FinderService;
use Modules\Workshop\Manager\ThemeManager;
use Tecnodesignc\Stylist\Theme\Exceptions\ThemeNotFoundException;

class PostTemplatesController extends Controller
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

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json($this->getTemplates());
    }

    /**
     * @return Collection
     */
    private function getTemplates(): Collection
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

        return collect($templates);
    }

    /**
     * Get the base path of the current theme.
     *
     * @return string
     * @throws ThemeNotFoundException
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
     * @return string|array
     */
    private function getDefaultTemplateName($template): string|array
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
     * @return string|array
     */
    private function removeExtensionsFromFilename($template): string|array
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
    private function hasSubdirectory($relativePath): bool
    {
        return ! empty($relativePath);
    }
}
