<?php

namespace Pdchaudhary\ChatgptPimcoreBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class ChatgptPimcoreBundle extends AbstractPimcoreBundle  implements PimcoreBundleAdminClassicInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    public function getJsPaths(): array
    {
        return [
            '/bundles/chatgptpimcore/js/pimcore/startup.js',
            '/bundles/chatgptpimcore/js/pimcore/openPopupModal.js',
        ];
    }

    public function getNiceName(): string
    {
        return 'Chatgpt Pimcore Bundle';
    }


    public function getEditmodeJsPaths() : array{
        return [
            '/bundles/chatgptpimcore/js/pimcore/startup.js',
            '/bundles/chatgptpimcore/js/pimcore/openPopupModal.js',
        ];
    }

    public function getCssPaths(): array
    {
        return [
            '/bundles/chatgptpimcore/css/index.css',
        ];
    }

    public function getVersion(): string
    {
        return "2.0";
    }

    
}