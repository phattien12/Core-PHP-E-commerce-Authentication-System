<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
    require_once("connection.php");
    $error = "";
    $msg = "";
    if(isset($_POST['action'])){
        $action = $_POST['action'];
        if($action === 'delete-product' && isset($_POST['product'])){
            $product = $_POST['product'];
            $res = delete_product($product);
            if($res['code'] != 0){
                $error = $res['error'];
            }
            else{
                $msg = $res['msg'];
            }
        }
        else if($action === 'edit-product' && isset($_POST['product']) && isset($_POST['product-name']) && isset($_POST['product-price']) && isset($_POST['product-desc'])){
            $id = $_POST['product'];
            $pName = $_POST['product-name'];
            $pPrice = $_POST['product-price'];
            $pDesc = $_POST['product-desc'];

            $res = edit_product($id, $pName, $pPrice, $pDesc);
            if($res['code'] != 0){
                $error = $res['error'];
            }
            else{
                $msg = $res['msg'];
            }
        }
    }

    $name = $_SESSION['name'];
    $data = "";
    $result = fetch_product();
    if($result['code'] != 0){
        $error = $result['error'];
    }
    else{
        $data = $result['data'];
    }    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Danh sách sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        td {
            vertical-align: middle;
        }
        img {
            max-height: 100px;
        }
    </style>
</head>
<body>
<div class = "wrapper" style = "position: relative">
<div class="container">
    <div class="row justify-content-center">
        <div class="col col-md-10">
            <h3 class="my-4 text-center">Product List</h3>
            <div class="d-flex justify-content-between">
                <a class="btn btn-sm btn-secondary mb-4" href="add_product.php">Add Product</a>
                <div>
                    <span class = "mr-3">Hello, <span class = "text-success"><?=$name?></span></span>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            <?php
                if(!empty($error)){
                    ?>
                        <div class='alert alert-danger text-center'><?=$error?></div>
                    <?php
                }
                else{
                    ?>
                    <table class="table-bordered table table-hover text-center">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>

                    <?php
                    while($d = $data->fetch_assoc()){
                    ?>
                        <tr class="item">
                            <td class="align-middle"><img src="images/<?=$d['image']?>"></td>
                            <td class="align-middle"><?=$d['name']?></td>
                            <td class="align-middle"><?=number_format( $d['price'],0,",", ".")?> VND</td>
                            <td class="align-middle"><?=$d['description']?></td>
                            <td class="align-middle">
                                <button onclick="editProduct(<?=$d['id']?>, '<?=$d['name']?>', ['<?=$d['name']?>', <?=$d['price']?>, '<?=$d['description']?>'])" class="btn btn-sm btn-primary mr-1 edit-btn"><i class="fas fa-pen"></i></button>
                                <button onclick="deleteProduct(<?=$d['id']?>, '<?=$d['name']?>')" class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </table>
                    <?php
                }
            ?>
            <p class="text-right">Total products: <strong><?=$data->num_rows?></strong></p>
        </div>
    </div>
</div>

    <!-- Delete Confirm Modal -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <form method = "post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <hp class="modal-title">Delete a Product</hp>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id = "product-to-delete">iPhone XS MAX</strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <input type = "hidden" name = "action" value = "delete-product">
                        <input type = "hidden" name = "product" id = "product-to-delete-input">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- Edit Confirm Modal -->
    <div id="editModal" class="modal fade" role="dialog">
        <form method = "post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <hp class="modal-title">Update product <strong id = "product-to-edit">iPhone XS MAX</strong></hp>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="name">Product Name</label>
                                <input id="name" name = "product-name" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input id="price" name = "product-price" type="number" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="desc">Description</label>
                                <input id="desc" name = "product-desc" type="text" class="form-control">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name = "action" value = "edit-product">
                        <input type="hidden" name = "product" id = "product-to-edit-input">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php
        if(!empty($msg)){
            ?>
            <div class="alert alert-success alert-dismissable text-center" style="width: 25%; display: block; position: absolute; top:80vh; right: 0; left: 0; margin-left: auto; margin-right: auto" id = "success-pop-up">
                <a href="#" class="close" data-dismiss="alert" aria-label="close"
                >&times;</a
                >
                <strong>Success!</strong> <?=$msg?>.
            </div>
            <?php
        }
    ?>
</div>
    <script>
        
        $(document).ready(function () {

            // show delete confirm
            $(".delete-btn").click(function () {
                $('#deleteModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            // show edit confirm
            $(".edit-btn").click(function () {
                $('#editModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });
            setTimeout(() => {
                $("#success-pop-up").remove();
            }, 4500);
            $("#success-pop-up").fadeOut(4000);
        });

        function deleteProduct(id, name){
            let inputProduct = $("#product-to-delete-input");
            let productName = $("#product-to-delete");
            productName.html(name);
            inputProduct.val(id);
        };
        
        function editProduct(id, name, arr){
            let inputProduct = $("#product-to-edit-input");
            let productName = $("#product-to-edit");
            let pName = $("#name");
            let productPrice = $("#price");
            let productDesc = $("#desc");
            pName.val(arr[0]);
            productPrice.val(arr[1]);
            productDesc.val(arr[2]);
            inputProduct.val(id);
            productName.html(name);
        };

        fetch
    </script>

</body>
</html>