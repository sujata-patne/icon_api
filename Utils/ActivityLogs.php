<?php

namespace VOP\Utils;

use VOP\Models\Activity;

class ActivityLogs {

    const ENTITY_ADDED = '%s created a new Entity %s';
    const ENTITY_UPDATED = '%s updated the entity %s';
    const ENTITY_DELETED = '%s deleted the entity %s';
    const ENTITY_PERMISSION_ADDED = 'Access permission to view / to edit the documents is given to %s for the entity %s';
    const ENTITY_PERMISSION_UPDATED = 'Edit / View permission given to %s for entity %s';
    const ENTITY_PERMISSION_REMOVED = 'Permission denied to %s for entity %s';
    const DOCUMENT_UPLOADED = '%s document(s) uploaded in entity &s by %s';
    const DOCUMENT_DOWNLOADED = 'Document successfully downloaded by %s';
    const DOCUMENT_DELETED = '%s document was deleted by %s';
    const DOCUMENT_MOVED = '%s document(s) were moved to %s folder by %s for entity %s';
    const LOGIN = 'Account login from IP %s';

    public function addActivityLog($activity) {

        $activity_obj = new Activity();

        $activity['id'] = $activity_obj->generateActivityId();
        $activity_obj->saveActivity($activity);
    }

}

?>