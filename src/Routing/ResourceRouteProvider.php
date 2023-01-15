<?php

namespace Drupal\bas\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for BAS Resources.
 */
class BasResourceRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    // define the list route
    $collection->add('entity.bas_resource.collection', $this->getCollectionRoute($entity_type));

    // define the add form route
    $add_form_route = $this->getAddFormRoute($entity_type);
    $collection->add('entity.bas_resource.add_form', $add_form_route);

    // define the add page route
    $add_page_route = $this->getAddPageRoute($entity_type);
    $collection->add('entity.bas_resource.add_page', $add_page_route);

    // define the edit form route
    $edit_form_route = $this->getEditFormRoute($entity_type);
    $collection->add('entity.bas_resource.edit_form', $edit_form_route);

    // define the delete form route
    $delete_form_route = $this->getDeleteFormRoute($entity_type);
    $collection->add('entity.bas_resource.delete_form', $delete_form_route);

    return $collection;
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route
        ->setDefaults([
          '_entity_list' => $entity_type_id,
          '_title' => "{$entity_type->getLabel()} list",
        ])
        ->setRequirement('_permission', 'access BAS Resource overview')
        ->setOption('_admin_route', TRUE);

      return $route;
    }
  }

  /**
   * Retrieve a resource from the database
   *
   * @param int $resource_id
   *   The ID of the resource to retrieve
   *
   * @return array|null
   *   An array containing the resource data, or null if no resource is found
   */
  public function getResource($resource_id) {
    $query = \Drupal::database()->select('bas_resources', 'r');
    $query->fields('r');
    $query->condition('r.id', $resource_id);
    $result = $query->execute()->fetchAssoc();
  
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function create() {
      // Create a new BAS Resource.
      $bas_resource = new BAS Resource();
  
      // Get the current user.
      $current_user = \Drupal::currentUser();
  
      // Set the bas resource's owner.
      $bas_resource->setOwnerId($current_user->id());
  
      // Build the form.
      $form = \Drupal::formBuilder()->getForm(BASResourceForm::class, $resource);
  
      return $form;
    }
}
