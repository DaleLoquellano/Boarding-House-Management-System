<!DOCTYPE html>
<html lang="en">

<head>
<?php session_start();?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants</title>
    <link href="https://fonts.googleapis.com/css2?family=Mallanna&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/tenants-styles.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script defer src="script.js"></script>
</head>

<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="logo">
            <img src="icons/logo.png" alt="Munoz Boarding House Logo">
        </div>

        <ul class="w-100">
            <li><a href="dashboard.php"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> Dashboard</a></li>
            <li><a href="tenants.php" class="active"><i class="fa fa-address-book-o" aria-hidden="true"></i> Tenants</a></li>
            <li><a href="roomLogs/index.php"><i class="fa fa-bed" aria-hidden="true"></i> Rooms</a></li>
            <li><a href="billing.php"><i class="fa fa-credit-card" aria-hidden="true"></i> Billings</a></li>
            <li><a href="settings.php"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
        </ul>
    </div>

    <div class="Content">
        <div class="main-content">
            <div class="header">
                <h1>Tenants List</h1>
                <p>View list of tenants and detailed tenant information!</p>

                <div class="buttons">
                    <!-- Add Tenant Modal -->
                    <button id="addTenant">Add Tenant</button>
                    <div id="Tenadd" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Add New Tenant</h2>

                            <form method="post" action='addNew/addTenant.php'>
                                <div class="form-group">
                                    <label for="firstName">Name:</label>
                                    <input type="text" id="firstName" name="TenFname" placeholder="First Name" required>
                                    <input type="text" id="middleName" name="TenMname" placeholder="Middle Name" required>
                                    <input type="text" id="lastName" name="TenLname" placeholder="Last Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="birthDate">Birth Date:</label>
                                    <input type="date" name="TenBdate" id="birthDate" required>
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender:</label>
                                    <select id="gender" name="TenGender" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="M">M</option>
                                        <option value="F">F</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="contactNumber">Contact Number:</label>
                                    <input type="text" id="contactNumber" name="TenConNum" placeholder="+63" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="TenEmail" placeholder="sample@mail.com" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <input type="text" id="houseNumber" name="TenHouseNum" placeholder="House No." required>
                                    <input type="text" id="street" name="TenSt" placeholder="Street" required>
                                    <input type="text" id="barangay" name="TenBarangay" placeholder="Barangay" required>
                                    <input type="text" id="city" name="TenCity" placeholder="City" required>
                                    <input type="text" id="province" name="TenProv" placeholder="Province" required>
                                </div>

                                <div class="form-group">
                                    <label for="emergencyContactName">Emergency Contact Name:</label>
                                    <input type="text" id="emergencyContactFirstName" name="EmConFname" placeholder="First Name" required>
                                    <input type="text" id="emergencyContactMiddleName" name="EmConMname" placeholder="Middle Name" required>
                                    <input type="text" id="emergencyContactLastName" name="EmConLname" placeholder="Last Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="emergencyContactNumber">Emergency Contact Number:</label>
                                    <input type="text" id="emergencyContactNumber" name="EmConNum" placeholder="+63" required>
                                </div>

                                <button type="submit" onclick='return confirm("Are you sure you want to add this Tenant?")'>Add Tenant</button>
                            </form>
                        </div>
                    </div>

                    <!-- Create Occupancy Modal -->
                    <button id="addRent">Create Occupancy</button>
                    <div id="Rentadd" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Create Occupancy</h2>
                            <form method="post" action='addNew/addOccupant.php'>
                                <label for="tenant">Tenant Assigned:</label>
                                <select id="tenantID" name="TenantID" required>
                                    <option value="" disabled selected>Select Tenant</option>

                                    <?php
                                    include 'mysql.php';

                                    // SQL code to fetch tenants without active occupancy
                                    $sql = "SELECT DISTINCT t.TenantID, CONCAT(t.TenFname, ' ', t.TenMname, ' ', t.TenLname) AS name
                                        FROM tenant t
                                        LEFT JOIN occupancy o ON t.TenantID = o.TenantID
                                        WHERE t.TenantID NOT IN (
                                            SELECT TenantID
                                            FROM occupancy
                                            WHERE OccStatus = 'Active'
                                        );";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['TenantID'] . "'>" . $row['name'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No options available</option>";
                                    }
                                    $conn->close();
                                    ?>
                                </select>

                                <label for="room-code">Occupancy Details:</label>
                                <select id="occupancy-type" name="occupancy-type" onchange="RentFunctions()" required>
                                    <option value="" disabled selected>Select Occupancy Type</option>

                                    <?php
                                    include 'mysql.php';

                                    // SQL query to fetch occupancy types and their rates
                                    $sql = "SELECT Occtype, OccRate FROM occtype";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['Occtype'] . "'>" . $row['Occtype'] . " - ₱" . $row['OccRate'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No options available</option>";
                                    }
                                    $conn->close();
                                    ?>
                                </select>

                                <div id="spacer" style="display: none;">
                                    <select id="bedspacer" name="RoomID">
                                        <option value="" disabled selected>Select Room</option>

                                        <?php
                                        include 'mysql.php';

                                        // SQL query to fetch empty or shared rooms
                                        $sql = "SELECT RoomID, Capacity, NumofTen, RoomType FROM rooms WHERE RoomType = 'Empty' OR RoomType = 'Shared'";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                if ($row['Capacity'] == $row['NumofTen'] || $row['RoomType'] == 'Rented') {
                                                    echo "<option value='" . $row['RoomID'] . "' disabled>" . "(" . $row['RoomType'] . ")" . ' ' . $row['RoomID'] . ' - ' . $row['Capacity'] . '/' . $row['NumofTen'] . "</option>";
                                                } else {
                                                    echo "<option value='" . $row['RoomID'] . "'>" . "(" . $row['RoomType'] . ")" . ' ' . $row['RoomID'] . ' - ' . $row['Capacity'] . '/' . $row['NumofTen'] . "</option>";
                                                }
                                            }
                                        } else {
                                            echo "<option value=''>No options available</option>";
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>

                                <div id="roomer4" style="display: none;">
                                    <select id="room4" name="RoomID">
                                        <option value="" disabled selected>Select Room</option>

                                        <?php
                                        include 'mysql.php';

                                        // SQL query to fetch empty rooms with capacity of 4
                                        $sql = "SELECT RoomID, Capacity, NumofTen, RoomType FROM rooms WHERE RoomType = 'Empty' AND Capacity = '4'";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['RoomID'] . "'>" . "(" . $row['RoomType'] . ")" . ' ' . $row['RoomID'] . ' - ' . $row['Capacity'] . '/' . $row['NumofTen'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No options available</option>";
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>

                                <div id="roomer6" style="display: none;">
                                    <select id="room6" name="RoomID">
                                        <option value="" disabled selected>Select Room</option>

                                        <?php
                                        include 'mysql.php';

                                        // SQL query to fetch empty rooms with capacity of 6
                                        $sql = "SELECT RoomID, Capacity, NumofTen, RoomType FROM rooms WHERE RoomType = 'Empty' AND Capacity = '6'";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['RoomID'] . "'>" . "(" . $row['RoomType'] . ")" . ' ' . $row['RoomID'] . ' - ' . $row['Capacity'] . '/' . $row['NumofTen'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No options available</option>";
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>

                                <div id="sharer" style="display: none;">
                                    <select id="sharerID" name="RoomID">
                                        <option value="" disabled selected>Select Roomer</option>

                                        <?php
                                        include 'mysql.php';

                                        // SQL query to fetch active sharers                                        
                                        $sql = "SELECT CONCAT(t.TenFname, ' ', t.TenMname, ' ', t.TenLname) AS name, r.Capacity, r.NumofTen, r.RoomID
                                        FROM occupancy o JOIN tenant t ON o.TenantID = t.TenantID JOIN rooms r ON o.RoomID = r.RoomID
                                        WHERE (o.OccStatus = 'Active' AND o.Occtype = 'Room(4 beds)') OR (o.OccStatus = 'Active' AND o.Occtype = 'Room(6 beds)')
                                        ORDER BY r.RoomID ASC";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['RoomID'] . "'>" . $row['name'] . ' ' . $row['RoomID'] . ' - ' . $row['Capacity'] . '/' . $row['NumofTen'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No options available</option>";
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>

                                <div id="nosharer">
                                    <label for="ending-date">Ending Date:</label>
                                    <input type="date" id="ending-date" name="ending-date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <label for="payment-total">Payment Total:</label>
                                <input type="text" id="payment-total" name="payment-total" readonly>
                                <button type="submit" onclick='return confirm("Are you sure you want to add this Occupancy?")'>Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="search-bar-container">
                    <input type="text" id="searchInput" class="search-bar" placeholder="Search by Tenant Name or ID">
                    <button id="searchButton" class="search-button">Search</button>
                </div>

                <?php
                include 'mysql.php';
                $search = isset($_GET['search']) ? $_GET['search'] : '';

                // SQL code to search for tenants
                $sql = "SELECT t.TenantID, CONCAT(t.TenFname, ' ', t.TenMname, ' ', t.TenLname) AS name,
                        MAX(o.RoomID) AS RoomID, MAX(o.OccDateEnd) AS OccDateEnd
                    FROM tenant t
                    LEFT JOIN occupancy o ON t.TenantID = o.TenantID
                    WHERE t.TenantID LIKE ? OR CONCAT(t.TenFname, ' ', t.TenMname, ' ', t.TenLname) LIKE ?
                    GROUP BY t.TenantID
                    ORDER BY name ASC"; 

                $stmt = $conn->prepare($sql);
                $searchTerm = "%$search%";
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>

                <div class="cardN">
                    <div class="cardN-header"></div>
                    <div class="table-container">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="border-gray-200" scope="col">Tenant Name</th>
                                    <th class="border-gray-200" scope="col">Room</th>
                                    <th class="border-gray-200" scope="col">End of Occupancy</th>
                                    <th class="border-gray-200" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["RoomID"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["OccDateEnd"]); ?></td>
                                        <td><a href="tenantThings/tenantinfo.php?TenantID=<?php echo htmlspecialchars($row["TenantID"]); ?>">View</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchQuery = document.getElementById('searchInput').value;
            window.location.search = `search=${encodeURIComponent(searchQuery)}`;
        });

        document.getElementById("addTenant").addEventListener("click", addTenant);
        document.getElementById("addRent").addEventListener("click", addRent);

        // Function to open Add Tenant Modal
        function addTenant() {
            var modal = document.getElementById("Tenadd");
            var span = modal.getElementsByClassName("close")[0];

            modal.style.display = "block";
            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        // Function to open Create Occupancy Modal
        function addRent() {
            var modal = document.getElementById("Rentadd");
            var span = modal.getElementsByClassName("close")[0];

            modal.style.display = "block";
            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        // Function to update Payment Total based on selected Occupancy Type
        function updatePaymentTotal() {
            var occTypeSelect = document.getElementById("occupancy-type");
            var selectedType = occTypeSelect.options[occTypeSelect.selectedIndex].value;

            // Make an AJAX request to fetch OccRate based on selectedType
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            var paymentTotalInput = document.getElementById("payment-total");
                            paymentTotalInput.value = "₱" + response.occRate;
                        } else {
                            console.error('Error fetching rate:', response.error);
                        }
                    } else {
                        console.error('Error fetching rate:', xhr.status);
                    }
                }
            };

            xhr.open("GET", "addNew/occrate.php?occtype=" + encodeURIComponent(selectedType), true);
            xhr.send();
        }

        // Function to handle various form displays based on Occupancy Type
        function RentFunctions() {
            updatePaymentTotal();
            toggleRoomerForm();
        }

        // Function to toggle form display based on Occupancy Type
        function toggleRoomerForm() {
            var occupancyType = document.getElementById("occupancy-type").value;
            var sharerForm = document.getElementById("sharer");
            var sharerForm2 = document.getElementById("sharerID");
            var roomer4Form = document.getElementById("roomer4");
            var roomer4Form2 = document.getElementById("room4");
            var roomer6Form = document.getElementById("roomer6");
            var roomer6Form2 = document.getElementById("room6");
            var bedspacerForm = document.getElementById("spacer");
            var bedspacerForm2 = document.getElementById("bedspacer");
            var noSharerDate = document.getElementById("nosharer");

            if (occupancyType == "Bedspacer") {
                bedspacerForm.style.display = "inline";
                bedspacerForm2.required = true;
                roomer4Form.style.display = "none";
                roomer4Form2.required = false;
                roomer6Form.style.display = "none";
                roomer6Form2.required = false;
                sharerForm.style.display = "none";
                sharerForm2.required = false;
                noSharerDate.style.display = "inline";
            } else if (occupancyType == "Room(4 beds)") {
                bedspacerForm.style.display = "none";
                bedspacerForm2.required = false;
                roomer4Form.style.display = "inline";
                roomer4Form2.required = true;
                roomer6Form.style.display = "none";
                roomer6Form2.required = false;
                sharerForm.style.display = "none";
                sharerForm2.required = false;
                noSharerDate.style.display = "inline";
            } else if (occupancyType == "Room(6 beds)") {
                bedspacerForm.style.display = "none";
                bedspacerForm2.required = false;
                roomer4Form.style.display = "none";
                roomer4Form2.required = false;
                roomer6Form.style.display = "inline";
                roomer6Form2.required = true;
                sharerForm.style.display = "none";
                sharerForm2.required = false;
                noSharerDate.style.display = "inline";
            } else if (occupancyType == "Sharer") {
                bedspacerForm.style.display = "none";
                bedspacerForm2.required = false;
                roomer4Form.style.display = "none";
                roomer4Form2.required = false;
                roomer6Form.style.display = "none";
                roomer6Form2.required = false;
                sharerForm.style.display = "inline";
                sharerForm2.required = true;
                noSharerDate.style.display = "none";
            }
        }

        // Function to open Room Details Modal
        function openModal(roomID) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var roomDetails = document.getElementById("roomDetails");
                        roomDetails.innerHTML = xhr.responseText;

                        var modal = document.getElementById("roomModal");
                        var span = modal.getElementsByClassName("close")[0];

                        modal.style.display = "block";
                        span.onclick = function() {
                            modal.style.display = "none";
                        }
                        window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                        }
                    } else {
                        console.error('Error fetching room details:', xhr.status);
                    }
                }
            };

            xhr.open("GET", "roomDetailsThings/getRoomDetails.php?RoomID=" + roomID, true);
            xhr.send();
        }

        // Function to close Room Details Modal
        function closeModal() {
            document.getElementById("roomModal").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('roomModal')) {
                document.getElementById('roomModal').style.display = 'none';
            }
        }
    </script>
</body>

</html>
