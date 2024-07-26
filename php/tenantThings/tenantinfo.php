<?php
include '../mysql.php';
// Check if TenantID is set
session_start();
if (isset($_GET['TenantID'])) {
    $TenantID = $_GET['TenantID'];
} else {
    header('Location: tenantTable.php');
}

$sql = "SELECT * FROM tenant WHERE TenantID = $TenantID";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo 'Tenant not found';
    exit();
}

$result->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants</title>
    <link rel="stylesheet" href="../styles/tenants-styles.css">
    <link rel="stylesheet" href="../styles/billing-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        document.querySelector(".alert-dismissible [data-dismiss='alert']").addEventListener("click", function() {
            window.location.href = window.location.href.split("?")[0] + "?";
        });
    </script>
</head>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="../icons/logo.png" alt="Munoz Boarding House Logo">
    </div>

    <ul class="w-100">
        <li><a href="../dashboard.php"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> Dashboard</a></li>
        <li><a href="../tenantTable.php" class="active"><i class="fa fa-address-book-o" aria-hidden="true"></i>Tenants</a></li>
        <li><a href="../tenants.php"><i class="fa fa-bed" aria-hidden="true"></i> Room Logs</a></li>
        <li><a href="#bills"><i class="fa fa-credit-card" aria-hidden="true"></i> Billings</a></li>
        <li><a href="../settings.php"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
    </ul>

    <div class="signout">
        <a href="#"><i class="fa fa-sign-out" aria-hidden="true"></i> Sign out</a>
    </div>
</div>

<div class="Content">
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12" style="position: sticky; top: 0;">

        <div class="cardN">
            <div class="cardN-header">
                <h5 class="cardN-title mb-0">Profile Settings</h5>
            </div>

            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action active" data-toggle="tab" href="#info">
                    Information
                </a>
                <a class="list-group-item list-group-item-action" data-toggle="tab" href="#history">
                    History
                </a>
                <a class="list-group-item list-group-item-action" data-toggle="tab" href="#billings">
                    Billings
                </a>
                <a class="list-group-item list-group-item-action" href="../tenantTable.php">
                    Back
                </a>
            </div>
            
        </div>

    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="tab-content">
            <div class="tab-pane active" id="info">
                <?php
                if (isset($_GET['success'])) {
                    echo '
                    <div class="alert alert-success alert-dismissible fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> Tenant information successufuly updated.
                    </div>';
                }
                if (isset($_GET['error'])) {
                    echo '
                    <div class="alert alert-danger alert-dismissible fade in">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Failed!</strong> Tenant information failed to be updated. Please try again.
                    </div>';
                }
                ?>
                <!-- Information Tab -->
                <?php
                include '../mysql.php';

                $sql = ("SELECT * FROM tenant WHERE TenantID = '$TenantID'");
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                echo '<div class="cardN">

                    <div class="cardN-header">
                        <h5 class="cardN-title mb-0">Profile Settings</h5>
                    </div>

                    
                    <div class="cardN-body">
                        <form action="updateTenants.php" method="POST">
                        <input type="hidden" name="TenantID" value="'. $row["TenantID"]. '">
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h6 class="mb-3 text-primary">Personal Details</h6>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input type="text" class="form-control" name="firstname" placeholder="Enter first name" value="' . $row["TenFname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="middlename">Middle Name</label>
                                    <input type="text" class="form-control" name="middlename" placeholder="Enter middle name" value="' . $row["TenMname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" name="lastname" placeholder="Enter last name" value="' . $row["TenLname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" name="gender">';
                                        if($row["TenGender"] == "M"){
                                             echo '<option value="M" name="tengender" selected>Male</option>
                                             <option value="F" name="tengender">Female</option>';
                                        }else{
                                             echo '<option value="M" name="tengender">Male</option>
                                             <option value="F" name="tengender" selected>Female</option>';
                                        } echo '
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-9 col-md-9 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="birthday">Birthday</label>
                                    <input type="date" class="form-control" name="birthday" value="' . $row["TenBdate"] . '">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email ID" value="' . $row["TenEmail"] . '">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" placeholder="Enter phone number" value="' . $row["TenConNum"] . '">
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h6 class="mb-3 text-primary">Address</h6>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="housenum">House Num</label>
                                    <input type="text" class="form-control" name="housenum" placeholder="Enter house number"  value="' . $row["TenHouseNum"] . '">
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-4 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" class="form-control" name="street" placeholder="Enter street" value="' . $row["TenSt"] . '">
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <input type="text" class="form-control" name="barangay" placeholder="Enter barangay" value="' . $row["TenBarangay"] . '">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" name="city" placeholder="Enter city" value="' . $row["TenCity"] . '">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <input type="text" class="form-control" name="province" placeholder="Enter province" value="' . $row["TenProv"] . '">
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h6 class="mb-3 text-primary">Emergency Contact</h6>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="emconfirstname">First Name</label>
                                    <input type="text" class="form-control" name="emconfirstname" placeholder="Enter first name" value="' . $row["EmConFname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="emconmiddlename">Middle Name</label>
                                    <input type="text" class="form-control" name="emconmiddlename" placeholder="Enter middle name" value="' . $row["EmConMname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="emconlastname">Last Name</label>
                                    <input type="text" class="form-control" name="emconlastname" placeholder="Enter last name" value="' . $row["EmConLname"] . '">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="emconphone">Phone</label>
                                    <input type="text" class="form-control" name="emconphone" placeholder="Enter phone number" value="' . $row["EmConNum"] . '">
                                </div>
                            </div>
                        </div>
                        <div class="row gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="text-right">';
// check here for session errors
                                IF ($_SESSION['staff_role'] == 'Admin' || $_SESSION['staff_role'] == 'admin'){ 
                                echo'    <input type="submit" id="submit" name="action" class="btn btn-primary" value="Update Profile" onclick="return confirm(\'Are you sure you want to update this tenant?\')">';
                                        }
                               echo ' </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>';

                $stmt->close();
                $conn->close();
                ?>



                <?php
                    include '../mysql.php';

                    $sql = "SELECT *, DATEDIFF(o.OccDateEnd, CURDATE()) AS DaysToEnd, o.OccupancyID
                            FROM occupancy o
                            LEFT JOIN tenant t ON t.TenantID = o.TenantID
                            LEFT JOIN appliances a ON o.OccupancyID = a.OccupancyID
                            WHERE t.TenantID = '$TenantID' AND o.OccStatus = 'Active'";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    if($row > 0) {
                        echo '<div class="cardN">
                                <div class="cardN-header">
                                    <h5 class="cardN-title mb-0">Occupancy:</h5>
                                </div>
                                <div class="table-container">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="border-gray-200" scope="col">Start</th>
                                                <th class="border-gray-200" scope="col">End</th>
                                                <th class="border-gray-200" scope="col">Type</th>
                                                <th class="border-gray-200" scope="col">Room</th>
                                                <th class="border-gray-200" scope="col">Status</th>
                                                <th class="border-gray-200" scope="col">Appliances</th>
                                                <th class="border-gray-200" scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>' . $row["OccDateStart"] . '</td>
                                                <td>' . $row["OccDateEnd"] . '</td>
                                                <td>' . $row["Occtype"] . '</td>
                                                <td>' . $row["RoomID"] . '</td>
                                                <td>' . $row["OccStatus"] . '</td>
                                                <form action="updateApps.php" method="post">
                                                    <input type="hidden" name="TenantID" value="' . $row["TenantID"] . '">
                                                    <input type="hidden" name="OccupancyID" value="' . $row["OccupancyID"] . '">
                                                    <input type="hidden" name="AppID" value="' . $row["TenantAppID"] . '">
                                                    <td><input type="number" name="Quantity" value='. $row["Quantity"] . '>';
                        // check here for session errors
if ($_SESSION['staff_role'] == 'Admin' || $_SESSION['staff_role'] == 'admin') {
                        echo '<input type="submit" onclick="return confirm(\'Are you sure you want to deactivate this tenant?\')"></td>
                                                </form>';
}
                                               echo' <form action="../roomDetailsThings/updateTenantDetails.php" method="post">
                                                    <input type="hidden" name="OccupancyID" value="' . $row["OccupancyID"] . '">
                                                    <input type="hidden" name="RoomID" value="' . $row["RoomID"] . '">
                                                    <td>
                                                        <input type="submit" name="action" value="Deactivate" onclick="return confirm(\'Are you sure you want to deactivate this tenant?\')">
                                                        &nbsp;
                                                        <input type="submit" name="action" value="Evict" onclick="return confirm(\'Are you sure you want to evict this tenant?\')">
                                                        &nbsp';
                                                        $daysToEnd = (int) $row['DaysToEnd'];
                                                        if ($daysToEnd <= 0) {
                                                            echo "<span class='glyphicon glyphicon-warning-sign' style='color:#FF0000;'></span>";
                                                        }
                                                        if ($daysToEnd <= 14 && $daysToEnd > 0) {
                                                            echo "<span class='glyphicon glyphicon-warning-sign' style='color:#FF0000;'></span>";
                                                           }
                                                    echo '</td>
                                                </form>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>';
                    } else {
                        echo '<div class="cardN">
                                <div class="cardN-header">
                                    <h5 class="cardN-title mb-0">Occupancy:</h5>
                                </div>
                                <p style="text-align: center; margin-top: 1rem;">No active occupancy found.</p>
                            </div>';
                    }

                $stmt->close();
                $conn->close();
                ?>

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="text-right">
                        <form action="deleteTenant.php" method="POST">
                            <input type="hidden" name="TenantID" value="<?php echo $_GET['TenantID']; ?>">
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to evict this tenant?')">Delete Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            


            <div class="tab-pane" id="history">
            <!-- History Tab -->
            <?php
            include '../mysql.php';

            $sql = "SELECT *, DATEDIFF(o.OccDateEnd, CURDATE()) AS DaysToEnd 
                    FROM occupancy o
                    LEFT JOIN tenant t ON t.TenantID = o.TenantID
                    LEFT JOIN appliances a ON o.OccupancyID = a.OccupancyID
                    WHERE t.TenantID = '$TenantID' 
                    ORDER BY o.OccupancyID DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                echo '<div class="cardN">
                        <div class="cardN-header">
                            <h5 class="cardN-title mb-0">Occupancy:</h5>
                        </div>
                        <div class="table-container">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-gray-200" scope="col">Start</th>
                                        <th class="border-gray-200" scope="col">End</th>
                                        <th class="border-gray-200" scope="col">Type</th>
                                        <th class="border-gray-200" scope="col">Room</th>
                                        <th class="border-gray-200" scope="col">Status</th>
                                        <th class="border-gray-200" scope="col">Appliances</th>
                                        <th class="border-gray-200" scope="col">Date Out</th>
                                    </tr>
                                </thead>
                                <tbody>';
                while($row = $result->fetch_assoc()) {
                    echo '<tr>
                            <td>' . $row["OccDateStart"] . '</td>
                            <td>' . $row["OccDateEnd"] . '</td>
                            <td>' . $row["Occtype"] . '</td>
                            <td>' . $row["RoomID"] . '</td>
                            <td>' . $row["OccStatus"] . '</td>
                            <td>' . $row["Quantity"] . '</td>';
                    if($row["OccStatus"] == 'Active') {
                        echo '<td>Ongoing</td>';
                    } else {
                        echo '<td>' . $row["DateOut"] . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody>
                        </table>
                    </div>
                    </div>';
            } else {
                echo '<div class="cardN">
                        <div class="cardN-header">
                            <h5 class="cardN-title mb-0">Occupancy:</h5>
                        </div>
                        <p style="text-align: center; margin-top: 1rem;">No occupancy history found.</p>
                    </div>';
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>


            
            <div class="tab-pane" id="billings">
                <!-- Billing Tab -->

                <!-- Bill Modal -->
                <div id="billModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal()">×</span>
                        <p id="BillDetails"></p>
                    </div>
                </div>


                <div class="cardN">

                    <div class="cardN-header">
                        <h5 class="cardN-title mb-0">Bills:</h5>
                    </div>

                    <div class="table-container">
                    <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Tenant</th>
                        <th>Date Issued</th>
                        <th>Date For</th>
                        <th>Due Date</th>
                        <th>Occupancy Rate</th>
                        <th>Appliance Amount</th>
                        <th>Additional Appliance</th>
                        <th>Appliance Rate</th>
                        <th>Amount Due</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                        include '../mysql.php';
                        
                        $query = "SELECT * , DATE_FORMAT(b.BillingMonth, '%M') as 'Month'
                                FROM billing b JOIN occupancy o ON b.OccupancyID = o.OccupancyID JOIN tenant t ON o.TenantID = t.TenantID
                                WHERE o.TenantID = '$TenantID'
                                ORDER BY b.BillRefNo DESC";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo   '<tr>
                                        <td>' . $row['BillType'] . '</td>
                                        <td>' . $row['BillStatus'] . '</td>
                                        <td>' . $row['TenFname'] . " " . $row['TenMname'] . " " .$row['TenLname'] . '</td>
                                        <td>' . $row['BillDateIssued'] . '</td>
                                        <td>' . $row['Month'] . '</td>
                                        <td>' . $row['BillDueDate'] . '</td>
                                        <td>' . $row['OccRate'] . '</td>
                                        <td>' . $row['Quantity'] . '</td>
                                        <td>' . $row['AddQuantity'] . '</td>
                                        <td>' . $row['appRate'] . '</td>
                                        <td>' . $row['DueAmount'] . '</td>
                                        <td>
                                        <span style="display: flex;">

                                        <button onclick="openModal(' . $row['BillRefNo'] . ')">View</button>

                                        <form action="billThings/updateBillStatus.php" method="post">
                                        <input type="hidden" name="BillRefNo" value="'. $row['BillRefNo'].'">
                                        <button type="submit" onclick="return confirm(\'Are you sure you want to mark this bill as paid?\')">Paid</button>
                                        </form>

                                        <form action="billThings/deleteBill.php" method="post">
                                        <input type="hidden" name="BillRefNo" value="'. $row['BillRefNo'].'">
                                        <button type="submit" onclick="return confirm(\'Are you sure you want to delete this bill?\')">Delete</button>
                                        </form>

                                        <span>
                                        </td>
                                        </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="12">No bills found.</td></tr>';
                        }
                        ?>
                </tbody>
                </table>
                </div>
                    
                </div>
            </div>


        </div>
    </div>
</div>
</body>

</html>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var links = document.querySelectorAll('.list-group-item');

    links.forEach(function(link) {
        link.addEventListener('click', function() {
            links.forEach(function(l) {
                l.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var closeButtons = document.querySelectorAll('.close');

    closeButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default action of the button

            var alert = this.closest('.alert');
            var url = window.location.href;
            var index = url.indexOf('&');

            if (index !== -1) {
                url = url.substring(0, index);
                window.history.replaceState({}, document.title, url);
            }

            alert.style.display = 'none'; // Hide the alert
        });
    });
});

function openModal(BillRefNo) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var BillDetails = document.getElementById("BillDetails");
                BillDetails.innerHTML = xhr.responseText;

                var modal = document.getElementById("billModal");
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

    xhr.open("GET", "../billThings/getBillingDetails.php?BillRefNo=" + BillRefNo, true);
    xhr.send();
}

</script>
