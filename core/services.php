<?php
global $conn;


//INCLUDE FILES
include_once('lang.php');
include_once('cone.php');

//PARAMETERS AND VARIABLES
$response = (object) array();
$response->success = false;
$errors = $lang->errors;

//FUNCTIONS DEFINITIONS
function auth($user, $token, $role){
	global $conn;
	$sql = "SELECT * FROM vw_sys_users WHERE username = '".$user."' AND token = '".$token."' AND role = '".$role."'";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    return true;
	} 
	else{
	    return false;
	}
}

//CORE
if(isset($_GET['user'])){
	if(isset($_GET['token'])){
		if(isset($_GET['role'])){
			$user = $_GET['user'];
			$token = $_GET['token'];
			$role = $_GET['role'];
			if(auth($user, $token, $role)){
				if(isset($_GET['service'])){
					$service = $_GET['service'];
					global $conn;
					switch ($service) {
						case 'getMenu':
							$sql = "SELECT pages.name AS name, pages.url AS url, pages.icon AS icon FROM sys_pages pages INNER JOIN sys_roles_pages roles ON roles.id_page = pages.id WHERE roles.id_role = '".$role."'";
							$result = mysqli_query($conn, $sql);	
							if(mysqli_num_rows($result) > 0) {
								$page = array();
								$i=0;
								while($row = mysqli_fetch_assoc($result)){
									$page[$i]['name'] = utf8_encode($row['name']);
									$page[$i]['url'] = $row['url'];
									$page[$i]['icon'] = $row['icon'];
									$i++;
								}
								$response->menu = $page;
								$response->success = true;
							} 
							break;
						case 'auth':
							if(isset($_GET['url'])){
								$page_url = $_GET['url'];
								$sql = "SELECT * FROM sys_pages pages INNER JOIN sys_roles_pages roles ON roles.id_page = pages.id WHERE roles.id_role = '".$role."' AND pages.url = '".$page_url."'";
								$result = mysqli_query($conn, $sql);	
								if(mysqli_num_rows($result) > 0) {
									while($row = mysqli_fetch_assoc($result)){
										$pageContent = $page_url;
									}
									$response->page = $pageContent;
									$response->success = true;
								} 
							}
							else{
								$response->message = $errors->service_auth_missing_url;
							}
							break;	
						default:
							$response->success = false;
							break;
					}
				}
				else{
					$response->message = $errors->service_missing;
				}	
			}
			else{
				$response->message = $errors->user_token_is_wrong;
			}
		}
		else{
			$response->message = $errors->missing_role;
		}	
	}
	else{
		$response->message = $errors->missing_token;
	}
}
else{
	$response->message = $errors->missing_user;
}

mysqli_close($conn);
//PRINT RESULT
echo json_encode($response, JSON_PRETTY_PRINT);
?>
