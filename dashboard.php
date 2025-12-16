<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION["username"];
$dataFile = "data.json";
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

function saveData($data, $file)
{
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT));
}

function getAutoStatus($date, $manualStatus)
{
    $today = date('Y-m-d');
    if ($manualStatus === 'done' || $manualStatus === 'cancelled') {
        return $manualStatus;
    }
    if ($date > $today) {
        return 'upcoming';
    }
    if ($date === $today) {
        return 'ongoing';
    }
    if ($date < $today) {
        return 'expired';
    }
    return 'unknown';
}

function displayStatus($status)
{
    switch ($status) {
        case 'done':
            return '<span style="color:#4caf50; font-weight:600;">Done</span>';
        case 'cancelled':
            return '<span style="color:#f44336; font-weight:600;">Cancelled</span>';
        case 'ongoing':
            return '<span style="color:#2196f3; font-weight:600;">Ongoing</span>';
        case 'expired':
            return '<span style="color:#9e9e9e; font-weight:600;">Expired</span>';
        case 'upcoming':
            return '<span style="color:#ffc107; font-weight:600;">Upcoming</span>';
        default:
            return '<span style="color:#ccc; font-weight:600;">Unknown</span>';
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['edit_id']) && !isset($_POST['status_change_id'])) {
    $date = trim($_POST["date"] ?? '');
    $purpose = trim($_POST["purpose"] ?? '');
    $location = trim($_POST["location"] ?? '');

    if ($date && $purpose && $location) {
        $data[] = [
            "id" => uniqid(),
            "username" => $currentUser,
            "date" => $date,
            "purpose" => $purpose,
            "location" => $location,
            "status" => null,
        ];
        saveData($data, $dataFile);
        echo '<div style="color:#82b1ff; margin-bottom: 1rem; font-weight: 500;">Reservation added</div>';
    } else {
        echo '<div style="color:#b00020; margin-bottom: 1rem; font-weight: 500;">Please fill all fields</div>';
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $date = trim($_POST["date"] ?? '');
    $purpose = trim($_POST["purpose"] ?? '');
    $location = trim($_POST["location"] ?? '');

    if ($date && $purpose && $location) {
        foreach ($data as &$item) {
            if ($item["id"] === $editId && $item["username"] === $currentUser) {
                $item["date"] = $date;
                $item["purpose"] = $purpose;
                $item["location"] = $location;
                saveData($data, $dataFile);
                echo '<div style="color:#82b1ff; margin-bottom: 1rem; font-weight: 500;">Reservation updated</div>';
                break;
            }
        }
        unset($item);
    } else {
        echo '<div style="color:#b00020; margin-bottom: 1rem; font-weight: 500;">Please fill all fields</div>';
    }
}

if (isset($_GET["id"]) && !isset($_GET['mark'])) {
    $id = $_GET["id"];
    $data = array_filter($data, function ($item) use ($id, $currentUser) {
        return !($item["id"] === $id && $item["username"] === $currentUser);
    });
    saveData($data, $dataFile);
}

if (isset($_GET['mark']) && isset($_GET['id'])) {
    $mark = $_GET['mark'];
    $id = $_GET['id'];
    if (in_array($mark, ['done', 'cancelled'])) {
        foreach ($data as &$item) {
            if ($item["id"] === $id && $item["username"] === $currentUser) {
                $item["status"] = $mark;
                saveData($data, $dataFile);
                echo '<div style="color:#82b1ff; margin-bottom: 1rem; font-weight: 500;">Status updated to ' . ucfirst($mark) . '</div>';
                break;
            }
        }
        unset($item);
    }
}

$editingId = $_GET['edit'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reservation Tracker</title>
    <style>
        body {
            margin: 0;
            background-color: #121a26;
            background-image: radial-gradient(circle, #1e2538 1px, transparent 1px);
            background-size: 40px 40px;
            color: #e1e5eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem;
            min-height: 100vh;
        }

        h1 {
            font-weight: 500;
            font-size: 3rem;
            color: #448aff;
            margin-bottom: 1rem;
        }

        h2 {
            font-weight: 500;
            font-size: 1.75rem;
            color: #a0aec0;
            margin-bottom: 1rem;
        }

        .add-reservation,
        .edit-reservation {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 640px;
        }

        input,
        textarea {
            font-size: 1rem;
            border-radius: 4px;
            border: 1.5px solid #2a3a5a;
            background-color: #1e2538;
            color: #e1e5eb;
            outline: none;
            padding: 0.6em 0.8em;
        }

        input[type="text"],
        input[type="date"] {
            flex: 1 1 200px;
        }

        textarea {
            flex: 1 1 100%;
            resize: none;
            min-height: 60px;
        }

        input:focus,
        textarea:focus {
            border-color: #82b1ff;
            box-shadow: 0 0 8px rgba(130, 177, 255, 0.7);
        }

        button.front-link {
            background-color: #2a3a5a;
            border: none;
            color: #e1e5eb;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.75em 1.5em;
            cursor: pointer;
            border-radius: 4px;
            user-select: none;
            align-self: flex-start;
        }

        button.front-link:hover,
        button.front-link:focus {
            background-color: #82b1ff;
            color: #121a26;
            outline: none;
        }

        table {
            border-collapse: collapse;
            width: 75%;
            max-width: 75%;
            color: #e1e5eb;
        }

        th,
        td {
            border: 1px solid #2a3a5a;
            padding: 0.75em 1em;
            text-align: left;
            vertical-align: middle;
        }

        .purpose-td {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .purpose-td:hover {
            max-width: 250px;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            text-overflow: clip;
        }

        th {
            background-color: #2a3a5a;
        }

        a.delete-link,
        a.edit-link {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 500;
            margin-right: 1rem;
        }

        a.delete-link:hover,
        a.delete-link:focus,
        a.edit-link:hover,
        a.edit-link:focus {
            text-decoration: underline;
            outline: none;
        }

        .logout-link {
            margin-top: 2rem;
        }

        .logout-link>a {
            background-color: #2a3a5a;
            color: #e1e5eb;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.75em 2.25em;
            border-radius: 4px;
            text-decoration: none;
            user-select: none;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .logout-link>a:hover,
        .logout-link>a:focus {
            background-color: #82b1ff;
            color: #121a26;
            outline: none;
        }

        .scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #2a3a5a #121a26;
        }

        .scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .scrollbar::-webkit-scrollbar-track {
            background: #121a26;
        }

        .scrollbar::-webkit-scrollbar-thumb {
            background: #2a3a5a;
            border-radius: 4px;
        }

        .scrollbar::-webkit-scrollbar-thumb:hover {
            background: #82b1ff;
        }
    </style>
</head>

<body>

    <h1>Welcome <?php echo htmlspecialchars($currentUser); ?></h1>

    <h2>Reservations</h2>

    <?php if ($editingId) :
        $editItem = null;
        foreach ($data as $item) {
            if ($item['id'] === $editingId && $item['username'] === $currentUser) {
                $editItem = $item;
                break;
            }
        }
        if ($editItem) : ?>
            <form method="POST" action="dashboard.php" class="edit-reservation">
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($editItem['id']); ?>" />
                <input type="date" name="date" required value="<?php echo htmlspecialchars($editItem['date'] ?? date('Y-m-d', strtotime('+1 day'))); ?>" />
                <textarea name="purpose" required><?php echo htmlspecialchars($editItem['purpose']); ?></textarea>
                <input type="text" name="location" required value="<?php echo htmlspecialchars($editItem['location']); ?>" />
                <button type="submit" class="front-link">Save changes</button>
                <a href="dashboard.php" class="front-link" style="margin-left: 1rem; text-align:center; padding: 0.75em 1em; text-decoration:none;">Cancel</a>
            </form>
        <?php else : ?>
            <div style="color:#b00020; margin-bottom: 1rem; font-weight: 500;">
                Reservation not found or you don't have permission to edit it.
            </div>
            <a href="dashboard.php" class="front-link" style="text-align:center; padding: 0.75em 1em; text-decoration:none; display:inline-block;">Back to Reservations</a>
        <?php endif; ?>
    <?php else : ?>
        <form method="POST" action="dashboard.php" class="add-reservation">
            <input type="date" name="date" required value="<?php echo date('Y-m-d', strtotime('+1 day')) ?>" />
            <textarea name="purpose" placeholder="Purpose" required></textarea>
            <input type="text" name="location" placeholder="Location" required />
            <button type="submit" class="front-link">Add reservation</button>
        </form>
    <?php endif; ?>
    <hr style="width: 75%; opacity: 0.25; margin-bottom: 2rem;" color='#a0aec0'>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Purpose</th>
                <th>Location</th>
                <th style="white-space: nowrap; width: 1%; min-width: max-content;">Created By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $item) : 
                $manualStatus = $item['status'] ?? null;
                $status = getAutoStatus($item['date'], $manualStatus);
                ?> 
                <tr>
                    <td style="white-space: nowrap; width: 1%; min-width: max-content;"><?php echo htmlspecialchars($item["date"]); ?></td>
                    <td class="purpose-td scrollbar"><?php echo htmlspecialchars($item["purpose"]); ?></td>
                    <td style="white-space: nowrap; width: 1%; min-width: max-content;"><?php echo htmlspecialchars($item["location"]); ?></td>
                    <td style="white-space: nowrap; width: 1%; min-width: max-content;" ><?php echo htmlspecialchars($item["username"]); ?></td>
                    <td style="white-space: nowrap; width: 1%; min-width: max-content;" ><?php echo displayStatus($status); ?></td>
                    <td style="white-space: nowrap; width: 1%; min-width: max-content;">
                        <?php if ($item["username"] === $currentUser): ?>
                            <?php if (!$manualStatus || ($manualStatus !== 'done' && $manualStatus !== 'cancelled')): ?>
                                <?php if ($status !== "expired"): ?>
                                    <a style="color: #4caf50" href="?mark=done&id=<?php echo urlencode($item["id"]); ?>" class="edit-link" onclick="return confirm('Are you sure you want to mark this as Done?')">Done</a>
                                    <a style="color: #f44336" href="?mark=cancelled&id=<?php echo urlencode($item["id"]); ?>" class="edit-link" onclick="return confirm('Are you sure you want to mark this as Cancelled?')">Cancel</a>
                                    <a style="color: #448aff" href="?edit=<?php echo urlencode($item["id"]); ?>" class="edit-link">Edit</a>
                                    <?php else: ?>
                                        <a style="color: #448aff" href="?edit=<?php echo urlencode($item["id"]); ?>" class="edit-link">Reschedule</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a style="color: #ff6b6b" href="?id=<?php echo urlencode($item["id"]); ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this reservation?')">Delete</a>
                        <?php else: ?>
                            <span>[View Only]</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="logout-link">
        <a href="logout.php">Logout</a>
    </div>
</body>

</html>
