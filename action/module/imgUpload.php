<?php 
function Img_Upload($img,$directory,$imgName){
	// 임시로 저장된 정보
	$tempFile = $img['tmp_name'];
	// 파일타입 및 확장자 체크
	$fileTypeExt = explode("/", $img['type']);
	// 파일 타입 
	$fileType = $fileTypeExt[0];
	// 파일 확장자
	$fileExt = $fileTypeExt[1];
	// 확장자 검사
	$extStatus = false;
	switch($fileExt){
		case 'jpeg':
		case 'jpg':
		case 'gif':
		case 'bmp':
		case 'png':
			$extStatus = true;
			break;
		
		default:
			echo "<script>alert('이미지 전용 확장자(jpg, bmp, gif, png)외에는 사용이 불가합니다.');window.close();</script>";
			exit;
			break;
	}
	if($fileType == 'image'){
		// 허용할 확장자를 jpg, bmp, gif, png로 정함, 그 외에는 업로드 불가
		if($extStatus){
			// 임시 파일 옮길 디렉토리 및 파일명

			$resFile = "../../assets/img/".$directory."/".$imgName.".".$fileExt;

			// $resFile = "./coach_img/".$imgName.".".$fileExt;

			// 경로랑 이름 변경해야함
			// 임시 저장된 파일을 우리가 저장할 디렉토리 및 파일명으로 옮김
			$imageUpload = move_uploaded_file($tempFile, $resFile);
		
		
			if($imageUpload != true){ // 업로드 성공 여부 확인
				echo "<script>alert('디비에 값 저장 실패');window.close();</script>";
				exit;
			}
			return $imgName.".".$fileExt;
		}	// 확장자가 jpg, bmp, gif, png가 아닌 경우 else문 실행
		else {
			echo "<script>alert('이미지 전용 확장자(jpg, bmp, gif, png)외에는 사용이 불가합니다.');window.close();</script>";
			exit;
		}	
	}
	else {
		echo "<script>alert('이미지 파일이 아닙니다.');window.close();</script>";
		exit;
	}	
}
