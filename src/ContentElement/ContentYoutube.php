<?php

declare(strict_types=1);

namespace Dreibein\YoutubeBundle\ContentElement;

use Contao\ContentYouTube as ContaoYoutube;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\PageModel;

/**
 * Class ContentYoutube.
 *
 * @property string $youtube_name
 * @property string $youtube_description
 * @property int    $youtube_upload_date
 */
class ContentYoutube extends ContaoYoutube
{
    /**
     * @var string Name of the template with the javascript code
     */
    protected $jsTemplate = 'js_youtube';

    protected function compile(): void
    {
        parent::compile();

        // Add the javascript to the page
        $jsTemplate = new FrontendTemplate($this->jsTemplate);
        $GLOBALS['TL_BODY']['youtube'] = $jsTemplate->parse();

        /** @var PageModel $page */
        $page = $GLOBALS['objPage'];

        // If not checked, use the youtube preview image
        // Override the Template value
        if (!$this->splashImage) {
            $this->setSplashImage();
        }

        // Initialize the Template property
        $this->Template->schemaOrg = [];

        // Get the current root page
        $rootPage = PageModel::findById($page->rootId);
        if (null === $rootPage) {
            return;
        }

        // youtube description
        $description = str_replace(PHP_EOL, ' ', (string) $this->youtube_description);

        // youtube upload date
        $date = new \DateTimeImmutable();
        $date = $date->setTimestamp((int) $this->youtube_upload_date);

        // Collect schema.org data
        $schemaOrg = [
            'name' => (string) $this->youtube_name,
            'description' => $description,
            'caption' => $this->caption,
            'thumbnailUrl' => $rootPage->getAbsoluteUrl() . '/' . $this->Template->splashImage->src,
            'uploadDate' => $date->format('Y-m-d'),
            'duration' => '',
            'contentUrl' => $this->Template->src,
        ];
        $this->Template->schemaOrg = $schemaOrg;

        // find the data-protection page and generate the URL to it
        $dataProtectionPage = PageModel::findById((int) $rootPage->dataProtectionPage);
        if (null === $dataProtectionPage) {
            return;
        }

        $dataProtectionUrl = $dataProtectionPage->getFrontendUrl();

        $this->Template->dataProtectionUrl = $dataProtectionUrl;
    }

    /**
     * Set the youtube preview image as splash image.
     */
    protected function setSplashImage(): void
    {
        // Add the youtube splash image
        $file = FilesModel::findByUuid($this->singleSRC);

        if (null !== $file && is_file(TL_ROOT . '/' . $file->path)) {
            $this->singleSRC = $file->path;

            // reset size entry
            $this->arrData['size'] = null;

            $splashTemplate = new \stdClass();
            self::addImageToTemplate($splashTemplate, $this->arrData, null, null, $file);
            $this->Template->splashImage = $splashTemplate;
        }
    }
}
