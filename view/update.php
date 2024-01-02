<?php
        require '../model/details.php';
        session_start();             
        $detailtb=isset($_SESSION['detailtbl0'])?unserialize($_SESSION['detailtbl0']):new detials();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="../libs/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Details</h2>
                    </div>
                    <p>Please fill this form and submit to add  record in the database.</p>
                    <form action="../index.php?act=update" method="post" >
                        <div class="form-group <?php echo (!empty($detailtb->name_msg)) ? 'has-error' : ''; ?>">
                            <label>Container name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $detailtb->name; ?>">
                            <span class="help-block"><?php echo $detailtb->name_msg;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($detailtb->location_msg)) ? 'has-error' : ''; ?>">
                            <label>Container Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo $detailtb->location; ?> ">
                            <span class="help-block"><?php echo $detailtb->location_msg;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $detailtb->id; ?>"/>
                        <input type="submit" name="updatebtn" class="btn btn-primary" value="Submit">
                        <a href="../index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>