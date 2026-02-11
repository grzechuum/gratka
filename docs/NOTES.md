Zadanie 1.

1. Można było się zalogować tokenem innego użytkownika. 

sampel: http://localhost:8000/auth/wildlife_pro/9e3c05f8d2aad0fb5f655fc0893a55d0232d4302adf8f6d5be984fa523f64ffa

Od razu to czego mi zabrakło to przycisku do logowania. Proces autoryzacji powinien posiadać własną formatkę email/pass, zaloguj za pomoca google, facebook.


1a. Jestem poczatkujący w Symphony, ale zauwazyłem że zmienne przekazane w url są bezpośrednio podstawiane pod zapytania sql, taki system można w prosty sposób wywalić za pomocą sql injection. Szlak mnie trafia gdy to widzę bo to nie wina programisty, powinna być jakaś warstwwa chroniąca we frameworkach przed tego typu zagraniami, podobny problem napotkałem w jednym z mechanizmów w Prestashop - gdzie atakujący sukcesywnie wywalał serwer wysyłając skrupulatnie przygotowany request. W naszych systemach  korzystamy z trim() addslashes() stripslashes() dla wszystkich zmeinnych $_POST, $_GET a także z PDO.

Punkt 1 .i 1a. Skorygowane w AuthController.php za pomocą fetchAssociative gdzie "pod spodem" jest znane mi już PDO i tam są mechanizmy które automatyczcnie escapują wartości zmiennych. W sumie AI podpowiedział że executeQuery tez escapuje ale trzba użyć bindowania zmiennych a nie przekazywac zmienne  w stringu sql.

PS: pomijam tutaj fatal errory gdy nie przekażemy parametrów username i token do route /auth/{username}/{token}, bo to juz jest oczywiste - nie korygowałem tego.

2. Nie podoba mi się to że mimo iż kliknę wyloguj to nadal moge wrócić na stronę profilu. (back history) tutaj trzeba dodać warunek jak brak sesji to redirectToRoute('home'), też tego nie korygowałem.

3. Like'owanie swoich zdjęć chyba nie jest błędem do wykrycia :)

4. Like'owanie odświeża stronę, co na wersji mobilnej przewija całość do góry i będzie bardzo irytyujące dla usera, ta funkcja powinna być przerobiona na request wysłany za pomoca javascript (np wysłany ajaxem).  

5. Brak responsywnego photo-grida, na poszczególnych breakpointach widok jest obcinany.

dodałem w templates/home/index.html.twig dodatkowy css z breakpointami dla klasy .photo-grid

6. Okragły przycisk dostępu do profilu nachodzi na logotyp "insta shot" - na widoku mobile width < 450 px

7. Pola w bazie w tabeli users typu varchar są zdecydowanie za długie, username, email 255 znaków ??? W późniejszym czasie gdy będzie potrzeba dużo indeksów to niepotrzebnie zabieramy zasoby pamięci podręcznej.


Zadanie 2.
Po urlu sprawdzam czy takie zdjęcie nie zostało już zaimportowane.
Można tutaj w profilu usera rozbudowac formatkę o udostpenione zdjęcia z możliwością kategoryzowania lub usuwania fotek. Formatkę do wgrywania dużej ilośći zdjęć poprzez drop area itp, automatyczne przycinanie, kompresję do webp itp.