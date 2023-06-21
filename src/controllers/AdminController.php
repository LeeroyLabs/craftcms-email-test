<?php
/**
 * Leeroy Email Test module for Craft CMS 3.x
 *
 * A module to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroy\leeroyemailtest\controllers;

use craft\events\TemplateEvent;
use modules\cqtsmodule\CqtsModule;
use modules\leeroyemailtest\events\CustomEvent;
use modules\leeroyemailtest\LeeroyEmailTest;

use Craft;
use craft\web\Controller;
use modules\Module;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 */
class AdminController extends Controller
{
    // Protected Properties
    // =========================================================================

    const EVENT_EMAIL_TEST_TEMPLATE = 'emailTestTemplate';

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['email-tests'];

    // Public Methods
    // =========================================================================

    /**
     * Post of the edition of a notification
     *
     * @return Response|null
     * @throws BadRequestHttpException
     */
    public function actionSendTest() {
        $site         = $this->request->getBodyParam('sites');
        $user         = $this->request->getBodyParam('users');
        $emailType    = $this->request->getBodyParam('emailType');
        $emailAddress = $this->request->getBodyParam('emailAddress');
        $preview      = 0;

        if ( $this->request->getBodyParam('preview') ) {
            $preview = $this->request->getBodyParam('preview');
        }

        $currentSite     = Craft::$app->sites->getSiteByHandle($site);

        $currentUser = \craft\elements\User::find()
            ->id($user)
            ->one();

        if ( $currentSite->language === "hi-IN" ) {
            $currentSite->language = "iu-CA";
        }

        $currentUser->defaultLanguage = substr($currentSite->language, 0, 2);

        if ( $currentUser->email ) {
            $currentUser->email = $emailAddress;
        }

        $systemMessage = Craft::$app->getSystemMessages()->getMessage($emailType, $currentUser->defaultLanguage);

        $data = [
            'user' => $currentUser,
            'subject' => $systemMessage->subject,
            'body' => $systemMessage->body,
            'emailKey' => $emailType,
            'preview' => $preview
        ];

        $data = $this->beforeRenderEmailTemplate($data);

        if ( isset($preview) && $preview ) {

            if( $data["html"] ) {
                $html = $data["html"];
            }else{
                $html = Craft::$app->view->renderTemplate('_mail/index.twig', $data, Craft::$app->view::TEMPLATE_MODE_SITE);
            }
            return $html;
        }

        if ( !isset($data['noSend']) ) {
            $success = Craft::$app
                ->getMailer()
                ->composeFromKey($emailType)
                ->setTo($currentUser)
                ->send();
        }

        $redirect = "/admin/email-tests";

        return $this->redirectToPostedUrl(null, $redirect);
    }

    public function beforeRenderEmailTemplate( $data )
    {
        $event = new CustomEvent([
            'templateData' => $data
        ]);
        $this->trigger(self::EVENT_EMAIL_TEST_TEMPLATE, $event);

        return $event->templateData;
    }

    /**
     * @return mixed
     */
    public function actionEmailTests()
    {
        $sites      = Craft::$app->sites->getAllSites();
        $users      = \craft\elements\User::find()->all();
        $systemMsgs = Craft::$app->systemMessages->getAllMessages();

        return $this->renderTemplate('leeroy-email-test/email-tests.twig', [
            'sites' => $sites,
            'users' => $users,
            'systemMsgs' => $systemMsgs,
            'defaultEmail' => getenv('SMTP_EMAIL_ADDRESS')
        ], Craft::$app->view::TEMPLATE_MODE_CP);
    }
}
