<?php
/**
 * Leeroy Email Test plugin for Craft CMS 3.x
 *
 * A plugin to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroy\leeroyemailtest;

use craft\base\Plugin;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use leeroy\leeroyemailtest\assetbundles\leeroyemailtest\LeeroyEmailTestAsset;
use leeroy\leeroyemailtest\services\LeeroyEmailTestService as LeeroyEmailTestServiceService;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class LeeroyEmailTest
 *
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 *
 * @property  LeeroyEmailTestServiceService $leeroyEmailTestService
 */
class LeeroyEmailTest extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * DraftSharer::$plugin
     *
     * @var LeeroyEmailTest
     */
    public static LeeroyEmailTest $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

//    /**
//     * @inheritdoc
//     */
//    public function __construct($id, $parent = null, array $config = [])
//    {
//        Craft::setAlias('@plugins/leeroyemailtest', $this->getBasePath());
//        $this->controllerNamespace = 'plugins\leeroyemailtest\controllers';
//
//        // Translation category
//        $i18n = Craft::$app->getI18n();
//        /** @noinspection UnSafeIsSetOverArrayInspection */
//        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
//            $i18n->translations[$id] = [
//                'class' => PhpMessageSource::class,
//                'sourceLanguage' => 'en-US',
//                'basePath' => '@plugins/leeroyemailtest/translations',
//                'forceTranslation' => true,
//                'allowOverrides' => true,
//            ];
//        }
//
//        // Base template directory
//        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
//            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
//                $e->roots[$this->id] = $baseDir;
//            }
//        });
//
//        // Set this as the global instance of this module class
//        static::setInstance($this);
//
//        parent::__construct($id, $parent, $config);
//    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                static function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(LeeroyEmailTestAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['email-tests'] = 'leeroy-email-test/admin/email-tests';
            }
        );

        // Add admin navigation item for the notification panel
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function (RegisterCpNavItemsEvent $event) {

                if (Craft::$app->getUser()->getIsAdmin()) {
                    $event->navItems[] = [
                        'url' => 'email-tests',
                        'label' => Craft::t('site', 'Admin:EmailTests'),
                        'icon' => '@plugins/assetbundles/assets/notif.svg',
                    ];
                }
            }
        );

        Craft::info(
            Craft::t(
                'leeroy-email-test',
                'Config:Initialized'
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
