<?php
/**
 * Leeroy Email Test module for Craft CMS 3.x
 *
 * A module to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroyemailtest;

use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use leeroyemailtest\assetbundles\leeroyemailtest\LeeroyEmailTestAsset;
use leeroyemailtest\services\LeeroyEmailTestService as LeeroyEmailTestServiceService;
use leeroyemailtest\variables\LeeroyEmailTestVariable;
use leeroyemailtest\twigextensions\LeeroyEmailTestTwigExtension;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class LeeroyEmailTest
 *
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 *
 * @property  LeeroyEmailTestServiceService $leeroyEmailTestService
 */
class LeeroyEmailTest extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * @var LeeroyEmailTest
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@modules/leeroyemailtest', $this->getBasePath());
        $this->controllerNamespace = 'modules\leeroyemailtest\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@modules/leeroyemailtest/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
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

        Craft::$app->view->registerTwigExtension(new LeeroyEmailTestTwigExtension());

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\leeroyemailtest\console\controllers';
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'leeroy-email-test/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'leeroy-email-test/default/do-something';
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
                        'icon' => '@modules/assetbundles/assets/notif.svg',
                    ];
                }
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('leeroyEmailTest', LeeroyEmailTestVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'leeroy-email-test',
                '{name} module loaded',
                ['name' => 'Leeroy Email Test']
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
