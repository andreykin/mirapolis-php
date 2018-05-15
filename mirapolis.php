<?

class Mirapolis {

	public $baseUrl = 'http://YOUR_URL.vr.mirapolis.ru/mira';
	public $appid = 'system'; // ������������� ����������
	public $secretkey = 'YOR_KEY'; // ���� �������

	function signet($url, $params) { // url REST-������� � ������ ����������
		$appid = $this->appid; // ������������� ����������
		$secretkey = $this->secretkey; // ���� �������
		
		$ret_params = $params; // ������ ������������ ����������
		ksort($ret_params); // ���������� ���������� �� ��������
		$ret_params['appid'] = $appid; //��������� � ����� ������� ��������� appid
		
		$signstring="$url?"; // ������������ ������ ��� ������� ������� � url
		foreach ($ret_params as $key => $val) {
			if (($val != "") || (gettype($val) != "string")) {
				$signstring .= "$key=$val&"; // ���������� � ������ ��� ������� ���������� ���������
			}
		}
		$signstring .= "secretkey=$secretkey"; // ���������� ������ ��� ������� ���������� secretkey
		$ret_params['sign'] = strtoupper(md5($signstring)); // ������������ ����� � ���������� ��� �
															// ������ ����������
		return $ret_params;
	}

	function sendrequest($url, $parameters = array(), $method = 'POST', $ret_crange = 0)
	{
		//���������� ������� ���������� ���������� appid � sign (������������ ���� ��������� ������� signit)
		$curl_data = $this->signet($url, $parameters);
		$ch = curl_init();//������������� ����������� �������
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8'); // ������� ��������� �������
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // ������� ����������
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // ������ ��������� ������� �� �������� ������
		curl_setopt($ch, CURLOPT_HEADER, $ret_crange); // ������ ��������� ����������� ��������� ContentRange
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); // ������� ������ �������
		$query = http_build_query($curl_data); // ���������� ������ ����������
		switch ($method) {
			case "PUT": // ��� PUT ���������� ���������� ����� ������ ����������
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen($query)));
			case "POST": // ��������� PUT � POST ���������� � ���� �������
				curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
				break;
			case "GET": // ��� GET � DELETE ��������� ����������� � ���������
			case "DELETE":
				$url .= "?$query";
		}
		curl_setopt($ch, CURLOPT_URL, $url); // ������� url �������
		$curl_response = curl_exec($ch); // ���������� �������
		$response = json_decode($curl_response, true); // ������� �����������
		if (! $response)
			$response = $curl_response; // ���� ��������� �� json
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // ��������� ���� ����������
		curl_close($ch);
		// ������ ������
		if ($code != 200) {
			throw new Exception("������������ HTTP-���: ".$code);
		} else 
			if (is_array($response) && isset($response["errorMessage"])) {
			throw new Exception("���������� ������: ".$response["errorMessage"]);
			} else {
				return $response;
			}
	}
	
	function measuresMembersRegbyemail($measureId,$email) {
		$url = $this->baseUrl."/service/v2/measures/$measureId/members/regbyemail/$email";
		return $this->sendrequest($url);
	}
}