# PHP Mirapolis API

���������� �����-������� ��� ������������� API ����������. ��� ������� ���� �� <a href="http://support.mirapolis.ru/mira-support/#&id=69&type=mediapreview&doaction=Go">����������� ������������</a>

# ������

include('mirapolis.php');
$m = new Mirapolis();

// ����������� �� ����������� �� email
$measureId = 524; // ������������� ����������� � ������� ���������
$email = 'test@email.ru'; // ����� ������������

print_r($m->measuresMembersRegbyemail($measureId,$email));
