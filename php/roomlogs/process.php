<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "im2database";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
// Function to retrieve rooms based on availability
function getRooms($conn, $availability) {
    $sql = "SELECT RoomID, RoomType, Capacity, NumofTen, RoomStatus FROM rooms";
    
    if ($availability == 'available') {
        $sql .= " WHERE NumofTen < (CASE WHEN Capacity = 1 THEN 4 ELSE 6 END)";
    } elseif ($availability == 'full') {
        $sql .= " WHERE NumofTen = (CASE WHEN Capacity = 1 THEN 4 ELSE 6 END)";
    } 

    $sql .= " ORDER BY RoomID ASC";
    $result = $conn->query($sql);
    if (!$result) {
        return [];
    }

    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    return $rooms;
}

// Function to delete a room based on room ID
function deleteRoom($conn, $roomId) {
    $roomId = $conn->real_escape_string($roomId);
    $deleteSql = "DELETE FROM rooms WHERE RoomID = '$roomId'";
    if ($conn->query($deleteSql) === TRUE) {
        return "success";
    } else {
        return "error";
    }
}

// Function to update room information
function updateRoom($conn, $oldRoomId, $newRoomId, $roomType, $capacity) {
    $oldRoomId = $conn->real_escape_string($oldRoomId);
    $newRoomId = $conn->real_escape_string($newRoomId);
    $roomType = $conn->real_escape_string($roomType);
    $capacity = $conn->real_escape_string($capacity);

    if (empty($newRoomId)) {
        $newRoomId = $oldRoomId;
    }

    $checkSql = "SELECT COUNT(*) AS count FROM rooms WHERE RoomID = UPPER('$newRoomId') AND RoomID != '$oldRoomId'";
    $checkResult = $conn->query($checkSql);
    $checkRow = $checkResult->fetch_assoc();

    if ($checkRow['count'] > 0) {
        return "error: New Room ID already exists.";
    }

    $updateSql = "UPDATE rooms SET RoomID = UPPER('$newRoomId'), RoomType = '$roomType', Capacity = '$capacity' WHERE RoomID = '$oldRoomId'";
    if ($conn->query($updateSql) === TRUE) {
        return "success";
    } else {
        if ($conn->errno === 1062) {
            return "Error: Duplicate Key Detected!";
        } else {
            return "error: " . $conn->error;
        }
    }
}

// Handle AJAX requests for updating room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['oldRoomId'])) {
    $oldRoomId = $_POST['oldRoomId'];
    $newRoomId = $_POST['newRoomId'];
    $roomType = $_POST['roomType'];
    $capacity = $_POST['capacity'];

    $result = updateRoom($conn, $oldRoomId, $newRoomId, $roomType, $capacity);
    echo $result;
    exit;
}

// Handle AJAX requests for adding room
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['roomInfoId'])) {
        $roomInfoId = $_POST['roomInfoId'];
        $roomInfo = getRoomInfo($conn, $roomInfoId);
        echo json_encode($roomInfo);
        exit;
    }

    if (isset($_POST['roomId']) && isset($_POST['capacity'])) {
        $roomId = $_POST['roomId'];
        $capacity = $_POST['capacity'];

        $roomId = $conn->real_escape_string($roomId);
        $capacity = $conn->real_escape_string($capacity);

        $insertSql = "INSERT INTO rooms (RoomID, Capacity) VALUES (UPPER('$roomId'), '$capacity')";
        if ($conn->query($insertSql) === TRUE) {
            echo "Room added successfully";
        } else {
            if ($conn->errno === 1062) {
                echo "Error: Duplicate Key Detected!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
        exit;
    }

    if (isset($_POST['deleteRoomId'])) {
        $roomId = $_POST['deleteRoomId'];
        $result = deleteRoom($conn, $roomId);
        echo $result;
        exit;
    }
}

// Handle AJAX requests for retrieving room information
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['availability'])) {
    $availability = $_GET['availability'];
    $rooms = getRooms($conn, $availability);

    if (!empty($rooms)) {
        foreach ($rooms as $room) {
            echo "
            <div class='room-card'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='width: 80%; text-align: left; font-weight: bold;'><p>" . htmlspecialchars($room['RoomID']) . "</p></td>
                        <td style='width: 20%; text-align: right;'>
                            <button class='info-btn' data-room-id='" . htmlspecialchars($room['RoomID']) . "'>. . .</button>
                        </td>
                    </tr>
                </table>
                <div style='margin-top: auto;'>";
            
            if ($room['NumofTen'] == 0) {
                echo "<p style='color: green; font-size: large;'>VACANT " . $room['NumofTen'] . "/" . $room['Capacity'] . "</p>";
            } elseif ($room['NumofTen'] < $room['Capacity']) {
                echo "<p style='color: #c2a230; font-size: large;'>AVAILABLE: " . $room['NumofTen'] . "/" . $room['Capacity'] . "</p>";
            } elseif ($room['NumofTen'] == $room['Capacity']) {
                echo "<p style='color: red; font-size: large;'>FULL</p>";
            }

            echo "
                </div>
            </div>";
        }
    } else {
        echo "No rooms found.";
    }
}

// Function to retrieve room information based on room ID
function getRoomInfo($conn, $roomId) {
    $roomId = $conn->real_escape_string($roomId);

    $roomSql = "SELECT RoomID, RoomType, Capacity, NumofTen, RoomStatus FROM rooms WHERE RoomID = '$roomId'";
    $roomResult = $conn->query($roomSql);

    $tenantsql = "SELECT o.TenantID, CONCAT(t.TenFname, ' ', t.TenLname, ' ', t.TenMname) AS Fullname FROM occupancy o JOIN tenant t ON o.TenantID = t.TenantID WHERE o.RoomID = '$roomId' AND o.OccStatus = 'Active'"; 
    $tenantsResult = $conn->query($tenantsql);

    if ($roomResult && $roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();
        echo "<p><h1><b>Room Information for " . htmlspecialchars($room['RoomID']) . "</b></h1></p>";
        echo "<p>Room Type: " . htmlspecialchars($room['RoomType']) . "</p>";
        echo "<p>Capacity: " . htmlspecialchars($room['Capacity']) . "</p>";
        echo "<p>Number of Tenants: " . htmlspecialchars($room['NumofTen']) . "</p>";
        echo "<p>Room Status: " . htmlspecialchars($room['RoomStatus']) . "</p>";
        
        echo "<td><button class='edit-btn' data-room-id='" . htmlspecialchars($room['RoomID']) . "'>Edit</button></td>";
        echo "<td><button class='delete-btn' data-room-id='" . htmlspecialchars($room['RoomID']) . "'>Delete</button></td><br><br>";
        
        if ($tenantsResult && $tenantsResult->num_rows > 0) {
            while ($tenant = $tenantsResult->fetch_assoc()) {
                echo "Tenant Name: ". htmlspecialchars($tenant['Fullname']) . "<br>";
            }
        } else {
            echo "<tr><td colspan='2'>No tenants found.</td></tr>";
        }
        echo "<table>";
    } else {
        echo "No room information found.";
    }
}

// AJAX to receive request for more info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'getRoomInfo') {
    $roomId = $_POST['roomId'];
    getRoomInfo($conn, $roomId);
    exit;
}

// AJAX for edit room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $roomId = $_POST['roomId'];
    edit($conn, $roomId);
    exit;
}

function edit($conn, $roomId) {
    $roomId = $conn->real_escape_string($roomId);
    $roomSql = "SELECT RoomID, RoomType, Capacity, NumofTen, RoomStatus FROM rooms WHERE RoomID = '$roomId'";
    $roomResult = $conn->query($roomSql);

    if ($roomResult === false) {
        echo "Error: " . $conn->error;
        return;
    }

    if ($roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();
        echo "<form id='editRoomForm'>
                <label for='editRoomId'>Room ID:</label>
                <br>
                <input type='text' id='editRoomId' name='roomId' value='" . htmlspecialchars($room['RoomID']) . "' required>
                <br>
                <label for='editRoomType'>Room Type:</label>
                <select id='editRoomType' name='roomType'>
                    <option value='empty'" . ($room['RoomType'] == 'empty' ? ' selected' : '') . ">Empty</option>
                    <option value='shared'" . ($room['RoomType'] == 'shared' ? ' selected' : '') . ">Shared</option>
                    <option value='rented'" . ($room['RoomType'] == 'rented' ? ' selected' : '') . ">Rented</option>
                </select>
                <br>
                <label for='editCapacity'>Capacity:</label>
                <select id='editCapacity' name='capacity'>
                    <option value='1'>4</option>
                    <option value='2'>6</option>
                </select>
                <br>
                <button type='submit' class='btn' data-room-id='" . htmlspecialchars($room['RoomID']) . "'>Save Changes</button>
            </form>";
    } else {
        echo "No room information found.";
    }
}

$conn->close();
?>
