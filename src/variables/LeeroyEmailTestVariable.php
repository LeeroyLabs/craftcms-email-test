<?php
/**
 * Leeroy Email Test module for Craft CMS 3.x
 *
 * A module to test the templates of emails
 *
 * @link      https://github.com/1543132
 * @copyright Copyright (c) 2022 Antoine Chouinard
 */

namespace leeroyemailtest\variables;

use modules\leeroyemailtest\LeeroyEmailTest;

use Craft;

/**
 * @author    Antoine Chouinard
 * @package   LeeroyEmailTest
 * @since     0.0.1
 */
class LeeroyEmailTestVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
