<?php

namespace Pdchaudhary\ChatgptPimcoreBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class ChatgptPimcoreBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/chatgptpimcore/js/pimcore/startup.js',
            '/bundles/chatgptpimcore/js/pimcore/openPopupModal.js',
        ];
    }

    public function getNiceName()
    {
        return 'Chatgpt Pimcore Bundle';
    }


    public function getEditmodeJsPaths(){
        return [
            '/bundles/chatgptpimcore/js/pimcore/startup.js',
            '/bundles/chatgptpimcore/js/pimcore/openPopupModal.js',
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/chatgptpimcore/css/index.css',
        ];
    }

    public function getVersion(){
        return "1.0";
    }
}