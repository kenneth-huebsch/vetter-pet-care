<?php

return array(


    //
    // Email Messages
    //
    
    'workflow_publisher_notification_heading' => 'When an editor submits entry for approval:',
    'workflow_publisher_notification_subject' => '"{{ submission.owner.title }}" has been submitted for approval on {{ siteName }}.',
    'workflow_publisher_notification_body' => "Hey {{ user.friendlyName }},\n\n" .
        "{{ submission.editor }} has submitted the entry \"{{ submission.owner.title }}\" for approval on {{ siteName }}.\n\n" .
        "To review it please log into your control panel.\n\n" .
        "{{ submission.cpEditUrl }}",

    'workflow_editor_notification_heading' => 'When a publisher approves or rejects an editor submission:',
    'workflow_editor_notification_subject' => 'Your submission for "{{ submission.owner.title }}" has been {{ submission.status }} on {{ siteName }}.',
    'workflow_editor_notification_body' => "Hey {{ user.friendlyName }},\n\n" .
        "Your submission for {{ submission.owner.title }} has been {{ submission.status }} {{ (submission.status == 'approved') ? submission.dateApproved | date : submission.dateRejected | date }} on {{ siteName }}.\n\n" .
        "{% if submission.notes %}Notes: \"{{ submission.notes }}\"\n\n{% endif %}" .
        "View your submission by logging into your control panel.\n\n" .
        "{{ submission.cpEditUrl }}",
);