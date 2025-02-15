<!DOCTYPE html>
<html>
<head>
    <title>New Assignment Created</title>
</head>
<body>
    <h1>New Assignment: {{ $assignment->title }}</h1>
    <p>Diskripsi: {{ $assignment->description }}</p>
    <p><strong>Deadline: </strong> {{ $assignment->deadline->format('Y-m-d H:i:s') }}</p>
    <p>Please complete the assignment before the deadline.</p>
</body>
</html>
