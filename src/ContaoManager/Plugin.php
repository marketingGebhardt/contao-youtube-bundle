<?php

declare(strict_types=1);

namespace Dreibein\YoutubeBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Dreibein\YoutubeBundle\DreibeinYoutubeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(DreibeinYoutubeBundle::class)->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
