<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'process.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Mallanna&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../icons/logo.png" alt="Munoz Boarding House Logo">
        </div>

        <ul class="w-100">
            <li><a href="../dashboard.php"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> Dashboard</a></li>
            <li><a href="../tenants.php"><i class="fa fa-address-book-o" aria-hidden="true"></i> Tenants</a></li>
            <li><a href="index.php" class="active"><i class="fa fa-bed" aria-hidden="true"></i> Rooms</a></li>
            <li><a href="../billing.php"><i class="fa fa-credit-card" aria-hidden="true"></i> Billings</a></li>
            <li><a href="../settings.php"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
        </ul>
    </div>

    <div class="Content">
        <div class="main-content">
            <div class="header">
                <h1><b>Rooms</b></h1>
                <p>View list of available rooms and active tenants!</p>

                <div class="header-buttons">
                    <div class="buttons"> <?php
if ($_SESSION['staff_role'] == 'Admin' || $_SESSION['staff_role'] == 'admin') {
    echo '<button id="addRoomBtn" class="btn">Add Room</button>';
}
?>
                        <div id="addRoomModal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2><b>Add Room</b></h2>

                                <form id="addRoomForm">
                                    <div class="form-group">
                                        <label for="roomId">Room ID:</label>
                                        <input type="text" id="roomId" name="roomId" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="capacity">Capacity:</label>
                                        <select id="capacity" name="capacity" required>
                                            <option value="1">4</option>
                                            <option value="2">6</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn">Add Room</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="filter-container">
                        <select id="sortorder">
                            <option value="all" selected>All</option>
                            <option value="available">Available</option>
                            <option value="full">Full</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="room-container">
                <div id="roomLogsContent" class="CB1"></div>
            </div>
        </div>
    </div>

    <div id="editRoomModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="editRoomDetails"></div>
        </div>
    </div>

    <div id="moreInfoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="roomDetails"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>
