<?php

namespace Drupal\apic_addon_appcreds_sync\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

const APPCREDS_SYNC_SETTINGS_FORM_ID = 'appcreds_sync_settings_configuration_form';

class AppcredsSyncSettingsForm extends FormBase {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return APPCREDS_SYNC_SETTINGS_FORM_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state): void {
        // todo
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state): array {

        $config = \Drupal::service('config.factory')->get(APPCREDS_SYNC_SETTINGS_KEY);

        $form[APPCREDS_SYNC_CLIENT_HOST_KEY] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Client Host"),
            '#description' => $this->t('Client Registration Host URL'),
            '#default_value' => $config->get(APPCREDS_SYNC_CLIENT_HOST_KEY)
        );

        $form[APPCREDS_SYNC_CLIENT_PATH_KEY] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Client Path"),
            '#description' => $this->t('Client Registration Path'),
            '#default_value' => $config->get(APPCREDS_SYNC_CLIENT_PATH_KEY)
        );

        $form[APPCREDS_SYNC_INITIAL_ACCESS_TOKEN_KEY] = array(
            '#type' => 'textfield',
            '#size' => 4096,
            '#maxlength' => 4096,
            '#title' => $this->t("Initial Access Token"),
            '#description' => $this->t('Initial Access Token'),
            '#default_value' => $config->get(APPCREDS_SYNC_INITIAL_ACCESS_TOKEN_KEY)
        );

        //
        // these are low-level fields... do not show
        //
//        $form[APPCREDS_SYNC_TEST_KEY] = array(
//            '#type' => 'checkbox',
//            '#title' => $this->t("Enable Test Analytics"),
//            '#description' => $this->t('Send APIm data to analytics service'),
//            '#default_value' => $config->get(APPCREDS_SYNC_TEST_KEY)
//        );
//
//        $form[APPCREDS_SYNC_TEST_URL_KEY] = array(
//            '#type' => 'textfield',
//            '#title' => $this->t("Test Analytics URL"),
//            '#description' => $this->t('Test api that will post apim data to analytics'),
//            '#default_value' => $config->get(APPCREDS_SYNC_TEST_URL_KEY)
//        );
//
//        $form[APPCREDS_SYNC_TEST_CLIENT_ID_KEY] = array(
//            '#type' => 'textfield',
//            '#title' => $this->t("Test Analytics Client Id"),
//            '#description' => $this->t('Client id to call test analytics api'),
//            '#default_value' => $config->get(APPCREDS_SYNC_TEST_CLIENT_ID_KEY)
//        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save configuration'),
            '#button_type' => 'primary',
        );

        // By default, render the form using system-config-form.html.twig.
        $form['#theme'] = 'system_config_form';

        return $form;
    }

    /**
     * {@inheritdoc}.
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void {

        $config = \Drupal::service('config.factory')->getEditable(APPCREDS_SYNC_SETTINGS_KEY);

        $config->set(APPCREDS_SYNC_CLIENT_HOST_KEY, $form_state->getValue(APPCREDS_SYNC_CLIENT_HOST_KEY))
            ->set(APPCREDS_SYNC_CLIENT_PATH_KEY, $form_state->getValue(APPCREDS_SYNC_CLIENT_PATH_KEY))
            ->set(APPCREDS_SYNC_INITIAL_ACCESS_TOKEN_KEY, $form_state->getValue(APPCREDS_SYNC_INITIAL_ACCESS_TOKEN_KEY))
            ->save();

        drupal_set_message($this->t('The configuration options have been saved.'));
    }
}
