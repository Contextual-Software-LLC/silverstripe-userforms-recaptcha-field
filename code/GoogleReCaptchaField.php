<?php

use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Core\Config\Config;
/**
 * Created by IntelliJ IDEA.
 * User: davidc
 * Date: 8/18/17
 * Time: 10:46 AM
 */
class GoogleReCaptchaField extends EditableFormField {
    // NOTE: remove? in lang now
    private static $singular_name = 'Google reCAPTCHA Field';
    private static $plural_name = 'Google reCAPTCHA Fields';

    /**
     * Mark as literal only
     *
     * @config
     * @var bool
     */
    private static $literal = true;

    /**
     * Get the name of the editor config to use for HTML sanitisation. Defaults to the active config.
     *
     * @var string
     * @config
     */
    private static $editor_config = null;

    private $recaptchaSiteKey = null;
    private $recaptchaSecretKey = null;

    public function __construct($record = null, $isSingleton = false, $model = null)
    {
        parent::__construct($record, $isSingleton, $model);
        $this->recaptchaSiteKey = $this->getRecaptchaSiteKey();
        $this->recaptchaSecretKey = $this->getRecaptchaSecretKey();
        if (empty($this->recaptchaSiteKey) || empty($this->recaptchaSecretKey)) {
            throw new Exception('Google reCAPTCHA site and secret keys required');
        }

        // TODO: throw an exception if UDF module not included?
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(array('Default', 'Validation', 'RightTitle'));

        return $fields;
    }

    public function getFormField()
    {
        Requirements::javascript("https://www.google.com/recaptcha/api.js");

        $callbackRoute = $this->getCallbackRoute();

        Requirements::javascriptTemplate("silverstripe-google-recaptcha/javascript/google-recaptcha-field.js", array(
        	'callbackRoute'	=> $callbackRoute
		));

        // TODO: Refactor the GoogleReCaptchaField model into a regular form field (to match naming convention)
		// TODO: Re-name this class to something that follows the UserForms naming convention
		// TODO: Instantiate a GoogleReCaptchaField here, rather than a LiteralField
        $content = LiteralField::create(
            "LiteralFieldContent-{$this->ID}]",
            "<div class='g-recaptcha' data-sitekey='{$this->recaptchaSiteKey}' data-callback='verifyRecaptcha' data-expired-callback='recaptchaExpired'></div>"
        );

        $field = CompositeField::create($content)
            ->setName($this->Name)
            ->setFieldHolderTemplate('UserFormsRecaptchaField_holder')
            ->addExtraClass('google-recaptcha-holder');

        # is this needed?
        #$this->doUpdateFormField($field);

        return $field;
    }

    private function getCallbackRoute() {
        return '/GoogleRecaptcha/verify';
    }

    protected function updateFormField($field)
    {
        parent::updateFormField($field);

        $this->ExtraClass .= ' nolabel';
    }

    public function showInReports()
    {
        return false;
    }

    public function getIcon()
    {
        return USERFORMS_DIR . '/images/editableliteralfield.png';
    }

    private function getRecaptchaSiteKey() {
        return Config::inst()->get('GoogleReCaptcha', 'site_key');
    }

    private function getRecaptchaSecretKey() {
        return Config::inst()->get('GoogleReCaptcha', 'secret_key');
    }
}
