<?php
include 'db.php';

/* DELETE */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM events WHERE id=$id");
    header("Location: index.php");
    exit();
}

/* ADD */
if (isset($_POST['add'])) {
    $date = $_POST['date'];
    $time = !empty($_POST['time']) ? $_POST['time'] : NULL;
    $name = $_POST['name'];

    $timeValue = $time ? "'$time'" : "NULL";

    $conn->query("INSERT INTO events (event_date, event_time, event_name)
                  VALUES ('$date', $timeValue, '$name')");

    header("Location: index.php");
    exit();
}

/* UPDATE */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $time = !empty($_POST['time']) ? $_POST['time'] : NULL;
    $name = $_POST['name'];

    $timeValue = $time ? "'$time'" : "NULL";

    $conn->query("UPDATE events
                  SET event_date='$date',
                      event_time=$timeValue,
                      event_name='$name'
                  WHERE id=$id");

    header("Location: index.php");
    exit();
}

/* EDIT MODE */
$editMode = false;
$editRow = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $editResult = $conn->query("SELECT * FROM events WHERE id=$id");
    $editRow = $editResult->fetch_assoc();
}

/* SORTING */
$sort = $_GET['sort'] ?? 'date';

if ($sort === 'name') {
    $order = "event_name ASC";
} elseif ($sort === 'time') {
    $order = "event_time ASC";
} else {
    $order = "event_date ASC, event_time ASC";
}

$result = $conn->query("SELECT * FROM events ORDER BY $order");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group 19 Event Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #222;
        }

        .container {
            width: 85%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        h2 {
            margin-top: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
        }

        form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 25px;
        }

        input[type="date"],
        input[type="time"],
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input[type="text"] {
            flex: 1;
            min-width: 220px;
        }

        button {
            padding: 10px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .cancel {
            text-decoration: none;
            padding: 10px 16px;
            background: #e5e7eb;
            color: #111;
            border-radius: 8px;
        }

        .cancel:hover {
            background: #d1d5db;
        }

        .sort-links {
            margin: 15px 0;
            font-size: 14px;
        }

        .sort-links a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            margin-right: 12px;
        }

        .sort-links a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            overflow: hidden;
            border-radius: 8px;
        }

        th {
            background: #1f2937;
            color: white;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f9fafb;
        }

        .actions a {
            text-decoration: none;
            margin-right: 10px;
            font-weight: bold;
        }

        .edit {
            color: #2563eb;
        }

        .delete {
            color: #dc2626;
        }

        .empty {
            text-align: center;
            color: #777;
            padding: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Group 19 Event Manager</h1>
    <p class="subtitle">A PHP and MySQL CRUD application for storing event data</p>

    <h2><?php echo $editMode ? "Edit Event" : "Add New Event"; ?></h2>

    <form method="POST">
        <?php if ($editMode) { ?>
            <input type="hidden" name="id" value="<?php echo $editRow['id']; ?>">
        <?php } ?>

        <input type="date" name="date" required
               value="<?php echo $editMode ? $editRow['event_date'] : ''; ?>">

        <input type="time" name="time"
               value="<?php echo $editMode ? $editRow['event_time'] : ''; ?>">

        <input type="text" name="name" placeholder="Enter event name" required
               value="<?php echo $editMode ? $editRow['event_name'] : ''; ?>">

        <?php if ($editMode) { ?>
            <button type="submit" name="update">Update Event</button>
            <a class="cancel" href="index.php">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add">Add Event</button>
        <?php } ?>
    </form>

    <h2>Saved Events</h2>

    <div class="sort-links">
        Sort:
        <a href="?sort=date">By Date</a>
        <a href="?sort=time">By Time</a>
        <a href="?sort=name">By Event Name</a>
    </div>

    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Event</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['event_date']; ?></td>
                    <td>
                        <?php
                        echo $row['event_time']
                            ? date("g:i A", strtotime($row['event_time']))
                            : "-";
                        ?>
                    </td>
                    <td><?php echo $row['event_name']; ?></td>
                    <td class="actions">
                        <a class="edit" href="?edit=<?php echo $row['id']; ?>">Edit</a>
                        <a class="delete" href="?delete=<?php echo $row['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this event?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td class="empty" colspan="4">No events have been added yet.</td>
            </tr>
        <?php } ?>
    </table>

    <div class="footer">
        CWL 207 Coding Group Project · Group 19
    </div>
</div>

</body>
</html>