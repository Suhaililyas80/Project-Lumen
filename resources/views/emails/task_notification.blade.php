<!DOCTYPE html>
<html>

<body>
    <h3>Task Notification</h3>
    <p>Dear {{ $user->name }},</p>
    <p>
        @if($type === 'assigned')
            A new task has been assigned to you.
        @elseif($type === 'updated')
            A task assigned to you has been updated.
        @else
            Task notification.
        @endif
    </p>
    <ul>
        <li><strong>Title:</strong> {{ $task->title }}</li>
        <li><strong>Description:</strong> {{ $task->description }}</li>
        <li><strong>End Date:</strong> {{ $task->end_date }}</li>
        <li><strong>Status:</strong> {{ $task->status }}</li>
    </ul>
    <p>Please check your dashboard for more details.</p>
</body>

</html>