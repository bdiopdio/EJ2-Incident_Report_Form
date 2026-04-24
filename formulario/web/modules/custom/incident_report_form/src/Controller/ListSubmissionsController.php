<?php

declare(strict_types=1);

namespace Drupal\incident_report_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;
use Drupal\incident_report_form\Service\DataService;

/**
 * Returns responses for Incident Report Form routes.
 */
final class ListSubmissionsController extends ControllerBase {

  /**
   * Instance of DataService service.
   * 
   * @var \Drupal\incident_report_form\Service\DataService
   */
  protected $data_service;

  /**
   * Constructs a DataService object.
   *
   * @param \Drupal\incident_report_form\Service\DataService $data_service.
   */
  public function __construct(DataService $data_service) {
    $this->$data_service = $data_service;
  }

  /**
   * Injects custom service.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('incident_report_form.data')
    );
  }

  /**
   * Builds the response.
   */
  public function listSubmissions() {
    $subs = $this->data_service->getSubmissions();
    
    return [
      '#theme' => 'submissions_list',
      '#submissions' => $subs,
      '#current_user' => User::load(\Drupal::currentUser()->id()),
    ];
  }

}
