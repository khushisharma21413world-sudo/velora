<?php
function adminLogin(){
  session_start();
  if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
    echo "<script>window.location.href='index.php';</script>"; exit();
  }
}
function redirect($url){ echo "<script>window.location.href='$url';</script>"; }
function alert($type,$msg){
  $cls = $type=='success' ? 'alert-success' : 'alert-danger';
  echo "<div class='alert $cls alert-dismissible fade show custom-alert' role='alert'><strong>$msg</strong><button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}
function filteration($data){
  foreach($data as $k=>$v){ $data[$k]=strip_tags(htmlspecialchars(stripslashes(trim($v)))); }
  return $data;
}
function select($sql,$values,$datatypes){
  $con=$GLOBALS['con'];
  $stmt=mysqli_prepare($con,$sql);
  mysqli_stmt_bind_param($stmt,$datatypes,...$values);
  mysqli_stmt_execute($stmt);
  $res=mysqli_stmt_get_result($stmt);
  mysqli_stmt_close($stmt);
  return $res;
}
function insert($sql,$values,$datatypes){
  $con=$GLOBALS['con'];
  $datatypes=preg_replace('/\s+/','',$datatypes);
  if($stmt=mysqli_prepare($con,$sql)){
    mysqli_stmt_bind_param($stmt,$datatypes,...$values);
    $r=mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $r;
  }
  return false;
}
function update($sql,$values,$datatypes){
  $con=$GLOBALS['con'];
  $datatypes=preg_replace('/\s+/','',$datatypes);
  if($stmt=mysqli_prepare($con,$sql)){
    mysqli_stmt_bind_param($stmt,$datatypes,...$values);
    $r=mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $r;
  }
  return false;
}
