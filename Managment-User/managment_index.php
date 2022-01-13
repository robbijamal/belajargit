
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "library/PHPMailer.php";
require_once "library/Exception.php";
require_once "library/OAuth.php";
require_once "library/POP3.php";
require_once "library/SMTP.php";
require_once "library/class.phpmailer.php";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notadinas_gen";

// Create connection
$conn= mysqli_connect($servername,$username,$password,$dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected Successfully."."<br>";

$query = "SELECT id,perihal,penerima_internal,penerima_internal_tembusan FROM nota where is_email=0";
$result = mysqli_query( $conn, $query );
$jmlemailsend = mysqli_num_rows($result);
$idnota = array();
//By Ahmad Robbi Al Jamal
	$mail = new PHPMailer;
 
	//Enable SMTP debugging. 
//	$mail->SMTPDebug = 3;                               
	//Set PHPMailer to use SMTP.
	$mail->isSMTP();            
	//Set SMTP host name                          
	$mail->Host = "smtp.gmail.com"; //host mail server
	$mail->SMTPAuth = true;  
	$mail->SMTPSecure = "tls";  
	$mail->Port = 587;  
	//Set this to true if SMTP host requires authentication to send email
                        
	//Provide username and password     
	$mail->Username = "robbiblast@gmail.com";   //nama-email smtp          
	$mail->Password = "robbiblast811566";           //password email smtp
	//If SMTP requires TLS encryption then set it
	                         
	//Set TCP port to connect to 
	//$mail->Port = 587;
	
	$mail->From = "robbiblast@gmail.com"; //email pengirim
	$mail->FromName = "Robbi"; //nama pengirim
	$subject = '';
	while( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {
		$penerima_in = json_decode($row['penerima_internal'],true);
		$penerima_in_temb = json_decode($row['penerima_internal_tembusan'],true);
		$subject = $row['perihal'];
	//echo "<pre>";
	//print_r($penerima_in);
	
	if(!empty($penerima_in)){
		array_push($idnota,$row['id']);
		// if($penerima_in[0]["type"]=='U'||$penerima_in[0]["type"]=='J'){
		$jml = count($penerima_in);
		for($i=0;$i<$jml;$i++){
			if($penerima_in[$i]["type"]=='U'||$penerima_in[$i]["type"]=='J'){
				
					$q2 = "SELECT * from users where id=".$penerima_in[$i]['id'];
					$rslt2 = mysqli_query( $conn, $q2 );
					$r2 = mysqli_fetch_row($rslt2);
					
					$mail->addAddress($r2[4]); //email penerima
					echo "Email Tujuan(iduser:".$penerima_in[$i]['id'].") : ".$r2[4]."<br>";
					
			}
		}
				
		// }
		
	}
	if(!empty($penerima_in_temb)){
		array_push($idnota,$row['id']);
		// if($penerima_in[0]["type"]=='U'||$penerima_in[0]["type"]=='J'){
		$jml2 = count($penerima_in_temb);
		for($j=0;$j<$jml2;$j++){
		if($penerima_in_temb[$j]["type"]=='U'||$penerima_in_temb[$j]["type"]=='J'){
			
				$q3 = "SELECT * from users where id=".$penerima_in_temb[$j]['id'];
				$rslt3 = mysqli_query( $conn, $q3 );
				$r3 = mysqli_fetch_row($rslt3);
				
				
				//echo $r2[4]."<br>";
				$mail->addAddress($r3[4]); //email penerima
				echo "Email Tujuan(iduser:".$penerima_in_temb[$j]['id'].") : ".$r3[4]."<br>";
				
		}
		}
		
	
	}
	
	}

	if(!empty($idnota)){
			$idnotauniq = array_unique($idnota);
			$dikirim = implode(",",$idnotauniq);
			$mail->isHTML(true);		
			$mail->Subject = $subject; 
			$mail->Body    = '<p>Silahkan login untuk melihatnya <br><br><a href="https://notadinas.mdmedia.co.id">https://notadinas.mdmedia.co.id</a><br><br>Salam,<br><br>Admin</p>'; //isi email
		if(!$mail->send()) 
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
		} 
		else 
		{
			echo "Id Nota = ".$dikirim."<br>";
			$email_terkirim = "UPDATE nota set is_email=1 where id in(".$dikirim.")";
			//echo $email_terkirim."<br>";
			mysqli_query( $conn, $email_terkirim );
			//echo "Email from MDMEDIA"."<br>";
			echo "Subject : ".$subject."<br>";
			//echo "Email Tujuan : ".$r2[4]."<br>";
			echo "Isi : ".$mail->Body;
			echo "Status : <u>Terkirim</u> <br><br>";
		
		}
	}
	else{
		echo "Alamat email tujuan kosong!";
	}
mysqli_close($conn);

//Stop the 
?>
