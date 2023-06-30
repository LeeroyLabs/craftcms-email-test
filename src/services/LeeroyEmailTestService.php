<?php
/**
 * Leeroy Email Test module for Craft CMS 3.x
 *
 * A module to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroy\leeroyemailtest\services;

use modules\leeroyemailtest\LeeroyEmailTest;

use Craft;
use craft\base\Component;

/**
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 */
class LeeroyEmailTestService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }
}
