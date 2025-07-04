<!DOCTYPE html>
<html>

<body>
    <h3>Hello {{ $user->name }},</h3>
    <p>Here are your {{ $period }} pending tasks:</p>
    <ul>
        @foreach($tasks as $task)
        <li>
            <strong>{{ $task->title }}</strong> – {{ $task->description }}<br />
            Due: {{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('Y-m-d') : 'No due date' }}<br />
            Status: {{ $task->status }}
        </li>
        @endforeach
    </ul>
    <p>Please make sure to complete your tasks on time.</p>
</body>

</html>