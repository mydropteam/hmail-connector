<?php

namespace Drupal\hermes_hmail_webform_connexion\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\hermes_hmail_webform_connexion\HmailConfigInterface;

/**
 * Defines the Hmail config entity.
 *
 * @ConfigEntityType(
 *   id = "hmail_config",
 *   label = @Translation("Hmail config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\hermes_hmail_webform_connexion\HmailConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\hermes_hmail_webform_connexion\Form\HmailConfigForm",
 *       "edit" = "Drupal\hermes_hmail_webform_connexion\Form\HmailConfigForm",
 *       "delete" = "Drupal\hermes_hmail_webform_connexion\Form\HmailConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\hermes_hmail_webform_connexion\HmailConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "hmail_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "source_type" = "source_type",
 *     "altered_webform" = "altered_webform",
 *     "hmail_base_url" = "hmail_base_url",
 *     "application_origin" = "application_origin",
 *     "csv_mapper_id" = "csv_mapper_id",
 *     "test_email" = "test_email",
 *     "civility" = "civility",
 *     "email" = "email",
 *     "country" = "country",
 *     "name" = "name",
 *     "firstname" = "firstname",
 *     "postal_code" = "postal_code",
 *     "further_information" = "further_information"
 *   },
 *   links = {
 *     "canonical" = "/hmail_config/{hmail_config}",
 *     "add-form" = "/hmail_config/add",
 *     "edit-form" = "/hmail_config/{hmail_config}/edit",
 *     "delete-form" = "/hmail_config/{hmail_config}/delete",
 *     "collection" = "/admin/config/hmail_config"
 *   }
 * )
 */
class HmailConfig extends ConfigEntityBase implements HmailConfigInterface {

    /**
     * The Hmail config ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Hmail config label.
     *
     * @var string
     */
    protected $label;

    /**
     * The Hmail config source type.
     *
     * @var string
     */
    protected $source_type;

    /**
     * The Hmail config altered Webform.
     *
     * @var string
     */
    protected $altered_webform;

    /**
     * The Hmail config base url.
     *
     * @var array
     */
    protected $hmail_base_url;

    /**
     * The Hmail config Application origin.
     *
     * @var string
     */
    protected $application_origin;

    /**
     * The Hmail config CSV mapper.
     *
     * @var string
     */
    protected $csv_mapper_id;

    /**
     * The Hmail config Test email.
     *
     * @var string
     */
    protected $test_email;

    /**
     * The Hmail config Field civility.
     *
     * @var string
     */
    protected $civility;

    /**
     * The Hmail config Field email.
     *
     * @var string
     */
    protected $email;

    /**
     * The Hmail config Field country.
     *
     * @var string
     */
    protected $country;

    /**
     * The Hmail config Field name.
     *
     * @var string
     */
    protected $name;

    /**
     * The Hmail config Field firstname.
     *
     * @var string
     */
    protected $firstname;

    /**
     * The Hmail config Field postal code.
     *
     * @var string
     */
    protected $postal_code;

    /**
     * The Hmail config Field postal further information.
     *
     * @var string
     */
    protected $further_information;
}
