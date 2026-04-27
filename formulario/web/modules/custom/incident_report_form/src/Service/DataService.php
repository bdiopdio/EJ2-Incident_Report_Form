<?php

namespace Drupal\incident_report_form\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\DatabaseExceptionWrapper;

/**
 * Service to interact with the database.
 */

class DataService {

  /**
   * The HTTP client.
   *
   * @var \Drupal\Core\Database\Database
   */
  protected $database;

  /**
   * Constructs a DataService object.
   * 
   * @param \Drupal\Core\Database\Database $database
   * Databa object.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Constructs a DataService object.
   * 
   * @param \Drupal\Core\Database\Database $database
   * Databa object.
   */
  public function getSubmissions() {
    try {
      $query = $this->database->select('incident_report', 'ir')
        ->fields('ir', ['titulo', 'descripcion', 'email', 'prioridad', 'user', 'created']);
      
      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } 
    catch (\Exception $e) {
      // Registramos el error en el log de Drupal (Watchdog)
      \Drupal::logger('incident_report_form')->error($e->getMessage());
      return []; 
    }
  }

}