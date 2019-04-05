<?php

namespace Drupal\hermes_hmail_webform_connexion\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Hmail\Webservice\Rest;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Form\FormInterface;

/**
 * Class AppOriginForm.
 */
class HmailConfigForm extends EntityForm {

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Class constructor.
     *
     * {@inheritdoc}
     */
    public function __construct(EntityTypeManagerInterface $tntiy_manager) {
        $this->entityTypeManager = $tntiy_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function form(array $form, FormStateInterface $form_state)
    {
        $form = parent::form($form, $form_state);
        /* @var $hmail_config \Drupal\hermes_hmail_webform_connexion\Entity\HmailConfig */
        $hmail_config = $this->entity;

        $form['settings'] = array(
            '#type' => 'fieldset',
            '#title' => t('Settings'),
        );
        $form['settings']['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Label'),
            '#maxlength' => 255,
            '#default_value' => $hmail_config->label(),
            '#description' => $this->t("Label for the Hmail config."),
            '#required' => TRUE,
        ];
        $form['settings']['id'] = [
            '#type' => 'machine_name',
            '#default_value' => $hmail_config->id(),
            '#machine_name' => [
                'exists' => '\Drupal\hermes_hmail_webform_connexion\Entity\HmailConfig::load',
            ],
            '#disabled' => !$hmail_config->isNew(),
        ];
        $form['settings']['hmail_base_url'] = [
            '#type' => 'textfield',
            '#default_value' => $hmail_config->get('hmail_base_url'),
            '#title' => $this->t('Hmail base url'),
            '#description' => $this->t('Url of the Hmail instance. Example : http://hmail.ppr-aws.hermes.com - Add pair user/password in case of HT password. Example : http://ht_access_user:ht_access_psswd@hmail.ppr-aws.hermes.com'),
            '#required' => TRUE,
        ];
        $form['settings']['application_origin'] = [
            '#type' => 'textfield',
            '#default_value' => $hmail_config->get('application_origin'),
            '#title' => $this->t('Application origin'),
            '#description' => $this->t('Example : other_brand_puiforcat'),
            '#required' => TRUE,
        ];
        $form['settings']['csv_mapper_id'] = [
            '#type' => 'textfield',
            '#default_value' => $hmail_config->get('csv_mapper_id'),
            '#title' => $this->t('Csv mapper'),
            '#description' => $this->t('Example : import_magento'),
            '#required' => TRUE,
        ];
        $form['settings']['test_email'] = [
            '#type' => 'email',
            '#default_value' => $hmail_config->get('test_email'),
            '#title' => $this->t('Test Email'),
            '#description' => $this->t('Email to subscribe to Hmail to TEST API'),
            '#required' => TRUE,
        ];
        $form['settings']['test_api_result'] = [
            '#type' => 'item',
            '#markup' => '',
            '#prefix' => '<div class="messages visually-hidden">',
            '#suffix' => '</div>',
            '#weight' => 1000,
        ];
        $form['settings']['test_api'] = array(
            '#type' => 'button',
            '#value' => $this->t('Test API'),
            '#ajax' => array(
                'callback' => [$this, 'testApiCallback'],
                'wrapper' => 'test_api_result',
                'effect' => 'fade',
                'progress' => [
                    'type' => 'throbber',
                    'message' => $this->t('Calling Hmail API.'),
                ],
            ),
        );
        $form['mapping'] = array(
            '#type' => 'fieldset',
            '#title' => t('Form mapping'),
            '#prefix' => '<div id="mapping">',
            '#suffix' => '</div>',
        );
        $form['mapping']['altered_webform'] = [
            '#type' => 'textfield',
            '#default_value' => $hmail_config->get('altered_webform'),
            '#title' => $this->t('Altered Webform'),
            '#description' => $this->t('Machine name of the Webform (list usually available at /admin/structure/webform) to apply Hmail registration to.'),
            '#required' => TRUE,
            '#ajax' => array(
                'callback' => [$this, 'optionsCallback'],
                'wrapper' => 'mapping',
                'effect' => 'fade',
                'event' => 'change',
                'progress' => [
                    'type' => 'throbber',
                    'message' => $this->t('Updating fields.'),
                ],
            ),
            '#prefix' => '<div id="altered_webform">',
            '#suffix' => '</div>',
            '#validated' => TRUE,
        ];
        $fields = [];
        if (null !== $hmail_config->get('altered_webform')) {
            $webform_id = $hmail_config->get('altered_webform');
        } else {
            $webform_id = null !== $form_state->getValue('altered_webform') ? $form_state->getValue('altered_webform') : NULL;
        }
        $fields = null !== $webform_id ? $this->get_webform_fields($webform_id) : [];

        $form['mapping']['email'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('email'),
            '#title' => $this->t('Field Email'),
            '#description' => $this->t('Field of the webform to match Hmail field "email"'),
            '#options' => $fields,
            '#required' => TRUE,
            '#validated' => TRUE,
        ];
        $form['mapping']['civility'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('civility'),
            '#title' => $this->t('Field civility'),
            '#description' => $this->t('Field of the webform to match Hmail field "civility"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        $form['mapping']['country'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('country'),
            '#title' => $this->t('Field Country'),
            '#description' => $this->t('Field of the webform to match Hmail field "country"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        $form['mapping']['name'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('name'),
            '#title' => $this->t('Field Last Name'),
            '#description' => $this->t('Field of the webform to match Hmail field "name"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        $form['mapping']['firstname'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('firstname'),
            '#title' => $this->t('Field First name'),
            '#description' => $this->t('Field of the webform to match Hmail field "firstname"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        $form['mapping']['postal_code'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('postal_code'),
            '#title' => $this->t('Field Postal code'),
            '#description' => $this->t('Field of the webform to match Hmail field "postal_code"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        $form['mapping']['further_information'] = [
            '#type' => 'select',
            '#default_value' => $hmail_config->get('further_information'),
            '#title' => $this->t('Field Further information'),
            '#description' => $this->t('Field of the webform to match Hmail field "further information"'),
            '#options' => $fields,
            "#empty_option" => $this->t('None'),
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
//      Check unique field Altered Webform
        $altered_webform = $form_state->getValue('altered_webform');
        if (!empty($altered_webform)) {
            $storage = \Drupal::entityTypeManager()->getStorage('hmail_config');
            $config_hmail =  $storage->getQuery()
                ->condition('altered_webform', $altered_webform, '=')
                ->execute();
            $current_hmail_config = $this->entity;
            $current_hmail_config_id = $current_hmail_config->id();
            if (!null == $config_hmail && !in_array($current_hmail_config_id, $config_hmail)) {
                $form_state->setErrorByName('webform_taken', $this->t('This Webform already has an Hmail Configuration'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state) {
        /* @var $hmail_config \Drupal\hermes_hmail_webform_connexion\Entity\HmailConfig */
        $hmail_config = $this->entity;
        $status = $hmail_config->save();

        if ($status) {
            $this->messenger()->addMessage($this->t('Saved the %label Hmail config.', [
                '%label' => $hmail_config->label(),
            ]));
        }
        else {
            $this->messenger()->addMessage($this->t('The %label Hmail config was not saved.', [
                '%label' => $hmail_config->label(),
            ]), MessengerInterface::TYPE_ERROR);
        }

        $form_state->setRedirect('entity.hmail_config.collection');
    }

    /**
     * Helper function to check whether an HmailConfig entity exists.
     */
    public function exist($id) {
        $entity = $this->entityTypeManager->getStorage('hmail_config')->getQuery()
            ->condition('id', $id)
            ->execute();
        return (bool) $entity;
    }

    public function get_webform_fields($webform_id){
        $moduleHandler = \Drupal::service('module_handler');
        if ($moduleHandler->moduleExists('webform')) {

            $webform_fields = array();
            $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($webform_id);
            if (!null == $webform) {
                $elements = $webform->getElementsOriginalDecoded();
                foreach ($elements as $k => $v) {
                    $keys = array_keys($v);
                    foreach ($keys as $item) {
                        if (strpos($item, "#") !== 0) {
                            $webform_fields[$item] = $item;
                        }
                    }
                }
            }
            return $webform_fields;
        }
        return array();
    }

    public function optionsCallback(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild(TRUE);
        return $form['mapping'];
    }

    public function test_api(array &$form, FormStateInterface $form_state) {
        $hmail_base_url = null !== $form_state->getValue('hmail_base_url')? $form_state->getValue('hmail_base_url') : '';
        $hmail_application_origin = null !== $form_state->getValue('application_origin')? $form_state->getValue('application_origin') : '';
        $hmail_csv_mapper = null !== $form_state->getValue('csv_mapper_id')? $form_state->getValue('csv_mapper_id') : '';
        $hmail_test_mail = null !== $form_state->getValue('test_email')? $form_state->getValue('test_email') : '';

        $data = array(
            "application_origin" => $hmail_application_origin,
            "csv_mapper_id" => $hmail_csv_mapper,
            "email" => $hmail_test_mail,
            "country" => 'WW',
            "language" => 'FR',
        );
        $rest = new Rest($hmail_base_url);

        $status = $rest->testStatus($data);
        if($status == '200' || $status == '201'){
            $message = 'STATUS : ' . $status . ' OK';
            $message .= '</br>';
            $message .= '<a target = "_blank" href="' . $hmail_base_url . '/admin/content/subscription?field_email_value=' . $hmail_test_mail . '">Visit Hmail subscribtion</a>' . ' (needs Hmail permission : "Access the Subscription overview page")';
            $rendered_message = \Drupal\Core\Render\Markup::create($message);
            drupal_set_message($rendered_message);
        }else{
            if($status == '403'){
                $message = 'STATUS : ' . $status . ' ' . 'SERVER ACCESS FORBIDEN';
                drupal_set_message($message , 'error');
            }
            else{
                $response = $rest->subscribe($data);
                if(empty($response['data'])){
                    $message = 'No response from server : '. $hmail_base_url . '. Check Hmail base url.' ;
                    drupal_set_message($message , 'error');
                }else{
                    $message = 'STATUS : ' . $status . ' ' . $response['data']['message'];
                    drupal_set_message($message , 'error');
                }
            }
        }
    }

    public function testApiCallback(array &$form, FormStateInterface $form_state) {
        $response = new AjaxResponse();
        $this->test_api($form, $form_state);
        $status_messages = array('#type' => 'status_messages');
        $messages = \Drupal::service('renderer')->renderRoot($status_messages);

        if (!empty($messages)) {
            $response->addCommand(new ReplaceCommand('.messages', $messages));
        }
        return $response;
    }
}
