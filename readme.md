# PHP Mirapolis API

Простейший класс-обертка для использования API Мираполиса. Код функций взят из <a href="http://support.mirapolis.ru/mira-support/#&id=69&type=mediapreview&doaction=Go">официальной документации</a>

# Пример

```
include('mirapolis.php');
$m = new Mirapolis();

// регистрация на мероприятие по email
$measureId = 524; // идентификатор мероприятия в системе Мираполис
$email = 'test@email.ru'; // адрес пользователя

print_r($m->measuresMembersRegbyemail($measureId,$email));
```
