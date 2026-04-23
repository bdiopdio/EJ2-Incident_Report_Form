<?php

declare(strict_types=1);

namespace Drupal\incident_report_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Incident report form form.
 */
final class IncidentReportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'incident_report_form_incident_report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['titulo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título'),
      '#size' => 60,
      '#placeholder' => $this->t('Ingrese un título breve para el incidente.'),
    ];

    $form['descripcion'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Descripción'),
      '#cols' => 60,
      '#placeholder' => $this->t('Describe el incidente con el mayor detalle posible.'),
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#placeholder' => $this->t('Dirección de correo.'),
    ];

    $form['prioridad'] = [
      '#type' => 'select',
      '#title' => $this->t('Prioridad'),
      '#options' => [
        'Urgente' => '1',
        'Alta' => '2',
        'Media' => '3',
        'Normal' => '4',
        'Baja' => '5',
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Enviar'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Title field validations
    if (mb_strlen(trim($form_state->getValue('titulo'))) < 5) {
      $form_state->setErrorByName(
        'titulo',
        $this->t('El título debe contener un mínimo de 5 caracteres.'),
      );
    } else if (mb_strlen(trim($form_state->getValue('titulo'))) > 500) {
      $form_state->setErrorByName(
        'titulo',
        $this->t('El título debe contener un máximo de 60 caracteres.'),
      );
    } else if (mb_strlen(trim($form_state->getValue('titulo'))) === '') {
      $form_state->setErrorByName(
        'titulo',
        $this->t('El campo título es obligatorio.'),
      );
    }

    // Description field validations
    if (mb_strlen(trim($form_state->getValue('descripcion'))) < 20 ) {
      $form_state->setErrorByName(
        'descripcion',
        $this->t('La descripción debe tener un mínimo de 20 caracteres.'),
      );
    } else if (mb_strlen(trim($form_state->getValue('descripcion'))) > 200) {
      $form_state->setErrorByName(
        'descripcion',
        $this->t('La descripción no puede pasar de 200 caracteres.'),
      );
    } else if (mb_strlen(trim($form_state->getValue('descripcion'))) === '') {
      $form_state->setErrorByName(
        'descripcion',
        $this->t('El campo descripción es obligatorio.'),
      );
    }

    // Email field validations
    if (!\Drupal::service('email.validator')->isValid($form_state->getValue('email'))) {
      $form_state->setErrorByName(
        'email',
        $this->t('Introduzca una dirección de correo válida.'),
      );
    }

    // Disable submit button if entered data is invalid
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('Gracias por reportar la incidencia.'));
    $form_state->setRedirect('incident_report_form.report_incident');
  }

}
