<?php

use Phalcon\Mvc\User\Component;

class Helper extends Component {

	public function error403(){

		$response = new \Phalcon\Http\Response();
		$response->setStatusCode(403, 'Forbidden');
		$response->setContent('403 Forbidden');
		$response->send();
		exit;
	}

	public function error404(){

		$response = new \Phalcon\Http\Response();
		$response->setStatusCode(404, 'Not Found');
		$response->setContent('404 Not Found');
		$response->send();
		exit;
	}

	public function paginator($paginator){

		$totalPage = $paginator->total_pages;

		if($totalPage == 1){
			return false;
		}

		$html = '<ul class="uk-pagination uk-flex-center" uk-margin>';

		// prev link
		$current = $paginator->current;
		$before = $paginator->before;
		if($current == $before){
			$html .= '<li class="uk-disabled"><a><span uk-pagination-previous></span></a></li>';
		}
		else{
			$html .= '<li><a href="?page=' . $before . '">'.
				'<span uk-pagination-previous></span></li>';
		}

		$limitPage = 6;
		$threshold = $totalPage / 2;
		$cut = 'none';
		if($totalPage > $limitPage && $threshold >= $current){
			$cut = 'last';
		}
		else if($totalPage > $limitPage && $threshold < $current){
			$cut = 'first';
		}

		$cuted = false;
		for($i = 1; $i <= $totalPage; $i++){

			if($i != $paginator->first && $i != $paginator->last){

				if($cut == 'first' && $i < $threshold){
					if(! $cuted){
						$html .= '<li class="uk-disabled"><span>...</span></li>';
						$cuted = true;
					}
					continue;
				}
				else if($cut == 'last' && $i > $threshold){
					if(! $cuted){
						$html .= '<li class="uk-disabled"><span>...</span></li>';
						$cuted = true;
					}
					continue;
				}
			}

			$class = $current == $i ? 'uk-disabled':'';
			$html .= '<li class="' . $class . '">';
			if(! $class){
				$html .= '<a href="?page=' . $i . '">' . $i . '</a>';
			}
			else{
				$html .= $i;
			}
			$html .= '</li>';
		}

		// next link
		$next = $paginator->next;
		if($current == $next){
			$html .= '<li class="uk-disabled"><a><span uk-pagination-next></span></a></li>';
		}
		else{
			$html .= '<li><a href="?page=' . $next . '">'.
				'<span uk-pagination-next></span></a></li>';
		}
		$html .= '</ul>';

		return $html;
	}


	public function httpPost($url, $param, $header = array(), $json = false, $cookie = null){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		if($json){
			$header[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		}
		else {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
		}

		if(count($header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		if($cookie){
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		}

		$response = curl_exec($ch);
		$errNo = curl_errno($ch);
		$error = curl_error($ch);

		if(CURLE_OK !== $errNo){
			Logger::error([__METHOD__, 'error no: ' . $errNo, $error]);
		}

		curl_close($ch);

		return $response;
	}

	public function httpGet($url, $param = array(), $header = array()){

		$url = count($param)?
			$url . '?' . http_build_query($param) : $url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if(count($header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		$response = curl_exec($ch);
		$errNo = curl_errno($ch);
		$error = curl_error($ch);

		if(CURLE_OK !== $errNo){
			Logger::error([__METHOD__, 'error no: ' . $errNo, $error]);
		}

		curl_close($ch);

		return $response;
	}

	public function respJson($param){

		header("Access-control-allow-origin: *");
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($param);
		exit;
	}

	public function respImg($filePath){

		header('Content-type: image/jpeg');
		readfile($filePath);
		exit;
	}

	public function respDownload($filePath, $fileName){

		header('Content-Disposition: inline; filename="' . $fileName . '"');
		header('Content-Length: ' . filesize($filePath));
		header('Content-Type: application/octet-stream');

		readfile($filePath);
		exit;
	}

	public function sendMail($to, $subject, $body){

		require_once $this->config->application->vendorDir . 'autoload.php';

		\Swift::init(function () {
			\Swift_DependencyContainer::getInstance()
				->register('mime.qpheaderencoder')
				->asAliasOf('mime.base64headerencoder');

			\Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
		});

		$from = $this->config->mail->from;
		$subject = $subject;
		$body = mb_convert_kana($body, 'KV', 'UTF-8');

		$transport = \Swift_MailTransport::newInstance();

		$mailer = \Swift_Mailer::newInstance($transport);
		$message = \Swift_Message::newInstance()
			->setMaxLineLength(0)
			->setCharset('iso-2022-jp')
			->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'))
			->setSubject($subject)
			->setTo($to)
			->setFrom($from)
			->setReturnPath($from)
			->setBody($body);

		return $mailer->send($message);
	}

}

