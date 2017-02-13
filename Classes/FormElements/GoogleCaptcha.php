<?php

namespace ObisConcept\NeosBootstrapForm\FormElements;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\SystemLoggerInterface;

/**
 * Class GoogleCaptcha
 * @package ObisConcept\NeosBootstrapForm\FormElements
 */
class GoogleCaptcha extends \Neos\Form\Core\Model\AbstractFormElement {

    /**
     * @var array
     */
    protected $settings;

    /**
     * Inject the settings
     *
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * On form submit
     *
     * @param \Neos\Form\Core\Runtime\FormRuntime $formRuntime
     * @param mixed $elementValue
     * @return void
     */
    public function onSubmit(\Neos\Form\Core\Runtime\FormRuntime $formRuntime, &$elementValue) {
        $error = false;
        if (isset($_POST['g-recaptcha-response'])) {

            $captcha = $_POST['g-recaptcha-response'];

            $secretKey = (isset($this->settings['googleCaptcha'][$formRuntime->getIdentifier()]['secretkey']))?$this->settings['googleCaptcha'][$formRuntime->getIdentifier()]['secretkey']:'';
            $ip = $_SERVER['REMOTE_ADDR'];
            $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$captcha.'&remoteip='.$ip);
            $responseKeys = json_decode($response, true);

            if (intval($responseKeys['success']) !== 1) {

                $error = true;

            }

        } else {

            $error = true;

        }

        if ($error) {

            $processingRule = $this->getRootForm()->getProcessingRule($this->getIdentifier());
            $processingRule->getProcessingMessages()->addError(new \Neos\Error\Messages\Error('Captcha isn\'t correct', 8734423749));

        }
    }

}