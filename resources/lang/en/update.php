<?php

return [
    'update' => 'Note: The update will update the current application files.',
    'update' => 'Note: The update will restore the previous application files.',
    'confirm' => 'Do you want to continue?',
    'validate' => [
        'backup' => 'Checking an existing backup...',
    ],
    'errors' => [
        'backup' => [
            'exists' => 'There is already a backup for this version. You cannot re-update files that have already been updated once. Firstly restore the files to their previous state and delete the backup copy.',
            'no_exists' => 'There is no backup for this version.'
        ]
    ],
    'backup' => 'Creating a backup...',
    'update_files' => 'Updating files...',
    'rollback_files' => 'Restoring files...'
];
