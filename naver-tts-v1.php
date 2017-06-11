<?php
  $client_id = "YOUR_CLIENT_ID";
  $client_secret = "YOUR_CLIENT_SECRET";
  $encText = urlencode("반갑습니다.");
  $postvars = "speaker=mijin&speed=0&text=".$encText;
  $url = "https://openapi.naver.com/v1/voice/tts.bin";
  $is_post = true;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, $is_post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
  $headers = array();
  $headers[] = "X-Naver-Client-Id: ".$client_id;
  $headers[] = "X-Naver-Client-Secret: ".$client_secret;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec ($ch);
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  echo "status_code:".$status_code."<br>";
  curl_close ($ch);
  if($status_code == 200) {
    //echo $response;
    $fp = fopen("intro.mp3", "w+");
    fwrite($fp, $response);
    fclose($fp);
    // convert from mp3 to wav 8Khz, mono
    shell_exec('rm -rf intro.mp3')
    shell_exec('sox intro.mp3 -c1 -r8000  intro.wav');
    //echo "<a href='intro.wav'>TTS재생</a>";
  } else {
    echo "Error 내용:".$response;
  }
?>
