<?php 
require('top.inc.php');

if(isset($_GET['type']) && $_GET['type']!=''){
    $type = get_safe_value($con, $_GET['type']);
    if($type == 'status'){
        $operation = get_safe_value($con, $_GET['operation']);
        $id = get_safe_value($con, $_GET['id']);
        if ($operation=='active')
        {
           $status = '1';
        }
        else{
      $status = '0';
        }
        

        $update_status = "update categories set status='$status' where id= '$id'";
        mysqli_query($con, $update_status);
     
    }
    if($type == 'delete'){
        $id = get_safe_value($con, $_GET['id']);
        $delete_status = "delete from categories where id= '$id'";
        mysqli_query($con, $delete_status);
     
    }
}











$sql = "select * from categories order by categories asc";

$res = mysqli_query($con,$sql);
?>

<div class="content pb-0"style ="" >
            <div class="orders">
               <div class="row">
                  <div class="col-xl-12">
                     <div class="card">
                        <div class="card-body">
                           <h2 class="box-title"style="text-align:center; font-weight:700;">Categories</h2>
                           <h2 class="box-title"style=""><a href="manage_categories.php" style="color: purple;">Add Categories</a></h2>
                        </div>
                        <div class="card-body--">
                           <div class="table-stats order-table ov-h">
                              <table class="table " style="text-align:center;">
                                 <thead>
                                    <tr>
                                       <th class="serial">#</th>
                                       <th>ID</th>
                                       <th>Categories</th>
                                       <th>Status</th>
                                       
                                    </tr>
                                 </thead>
                                 <tbody>
                                   <?php 
                                   $i=1;
                                   while($row=mysqli_fetch_assoc($res)){?>
                                   <tr>
                                   <td class="serial">
                                    <?php echo $i ?>
                                   </td>
                                   <td><?php echo $row['id'] ?></td>
                                   <td><?php echo $row['categories'] ?></td>
                                   <td><?php
                                   if($row['status']==1){
                                    echo "<span class='badge badge-complete'><a href='?type=status&operation=deactive&id=".$row['id']."'>Active</a></span>&nbsp";
                                   }
                                   else{
                                    echo " <span class='badge badge-pending'> <a href='?type=status&operation=active&id=".$row['id']."'>Deactive</a></span>&nbsp";
                                   }
                                   echo "<span class='badge badge-edit bg-primary'><a href='manage_categories.php?id=".$row['id']."'>Edit</a></span>&nbsp";
                                   echo "<span  class='badge text-bg-danger '> <a href='?type=delete&id=".$row['id']."'>Delete</a></span>";
                               
                                   
                                   
                                   
                                   ?></td>
                                  </tr>
                                   
                                   <?php } ?>
                                   

                
               
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
		  </div>
          <?php 
require('footer.inc.php');
?>