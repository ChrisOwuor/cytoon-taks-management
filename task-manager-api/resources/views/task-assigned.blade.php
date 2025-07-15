<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Task</title>
</head>

<body>
    <h2>Hello {{ $task->assignee->name ?? 'User' }},</h2>
    <p>You’ve been assigned a new task:</p>

    <ul>
        <li><strong>Title:</strong> {{ $task->title }}</li>
        <li><strong>Deadline:</strong> {{ $task->deadline }}</li>
        <li><strong>Description:</strong> {{ $task->description }}</li>
    </ul>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>

</html>