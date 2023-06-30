<?php
/**
 * Leeroy Email Test module for Craft CMS 3.x
 *
 * A module to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroy\leeroyemailtest\assetbundles\leeroyemailtest;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 */
class LeeroyEmailTestAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@modules/leeroyemailtest/assetbundles/leeroyemailtest/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/LeeroyEmailTest.js',
        ];

        $this->css = [
            'css/LeeroyEmailTest.css',
        ];

        parent::init();
    }
}
