<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\TypeFactory;
use Cake\Event\Event;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 *
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EventsController extends AppController
{
  //Necessario per gestire la risposta in json della view
  public function initialize(): void
  {
    parent::initialize();
    //$this->Authentication->allowUnauthenticated(['getList','subscribe']);
  }




  public function getList($allowedEvents = null)
  {
    $conditions = [];
    if (!empty($allowedEvents)) {
      $conditions['event_id'] = $allowedEvents;
    }
    $events = $this->Events->find('all', ['fields' => ['id', 'title'], 'limit' => 200, 'conditions' => $conditions]);
    $this->set('events', $events);

    //Mando la risposta in ajax
    if ($this->request->isAjax()) {
      $this->set('_serialize', 'events');
      $this->RequestHandler->renderAs($this, 'json');
    }
  }

  //Metodo per far iscrivere gli utenti ad un evento
  public function subscribe($slug)
  {
    $event = $this->Events->findBySlug($slug)
      ->firstOrFail();

    $siti = $this->Events->Destinations->find('list', [
      'conditions' => ['show_in_list' => 1, 'chiuso' => 0],
      'order' => 'Name'
    ]);
    $this->set('siti', $siti);
    $this->set('event', $event);
  }
}
