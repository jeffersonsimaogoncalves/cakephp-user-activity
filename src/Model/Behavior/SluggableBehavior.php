<?php

/**
 * Crabstudio(tm): Cake bake ACE template (http://github.com/crabstudio/ace)
 * Copyright (c) Crabstudio. (http://crabstudio.info)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crabstudio. (http://crabstudio.info)
 * @link          http://github.com/crabstudio/ace Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Backend\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;

class SluggableBehavior extends Behavior {

    /**
     * Default source field is title
     * Default destination field is slu
     * @var string source
     * @var string destination
     */
    protected $_defaultConfig = [
        'sourceField' => 'title',
        'destinationField' => 'slug'
    ];

    /**
     * Unsigned utf-8 characters and make friendly-link-like-this then set to slug field
     * @param Entity $entity
     */
    public function slug(Entity $entity) {
        if ($entity->isNew()) {
            $this->setUniqueSlug($entity, $entity->get($this->_defaultConfig['sourceField']), '');
        }
    }

    /**
     * 
     * @param string $source
     * @param string $extra
     * 
     */
    private function setUniqueSlug($entity, $source, $extra) {
        $slug = str_slug($source);
        $destinationField = $this->_defaultConfig['destinationField'];
        if ($this->_table->exists(['conditions' => ["$destinationField" => $slug]])) {
            $this->setUniqueSlug($entity, $source . '-', $extra + 1);
            return;
        }
        $entity->set($destinationField, $slug);
    }

}
