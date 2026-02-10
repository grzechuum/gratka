1. Można było się zalogować tokenem innego użytkownika. 

sampel: http://localhost:8000/auth/wildlife_pro/9e3c05f8d2aad0fb5f655fc0893a55d0232d4302adf8f6d5be984fa523f64ffa

Od razu to czego mi zabrakło to przycisku do logowania. Proces autoryzacji powinien posiadać własną formatkę email hasło, zaloguj za pomoca google, facebook.


1a. Jestem poczatkujący w Symphony, ale zauwazyłem że zmienne przekazane w url są bezpośrednio podstawiane pod zapytania sql, taki system można w prosty sposób wywalić zapomoca sql injection. Szlak mnie trafia gdy to widzę bo to nie wina programisty, powinna być jakaś warstwwa chroniąca we frameworkach przed tego typu zagraniami, podobny problem napotkałem w jednym z mechanizmów w Prestashop - gdzie atakujący sukcesywnie wywalał serwer wysyłając skrupulatnie przygotowany request. W naszych systemach nagminie korzystamy z trim() i addslashes() stripslashes().

1 i 1a Skorygowane w AuthController.php za pomocą fetchAssociative gdzie pod spodem jest znane mi już PDO i tam są mechanizmy które automatyczcnie escapują wartości zmiennych. Wsumie AI podpowiedział że executeQuery tez escapuje ale trzba użyć bindowania zmiennych a nie przekazywac zmienne  w stringu sql.

PS: pomijam tutaj fatal errory gdy nie przekażemy parametrów username i token do route /auth/{username}/{token}, bo to juz jest oczywiste.

2. Nie podoba mi się to że mimo iż kliknę wyloguj to nadal moge wrócić na stronę profilu. (back history)

3. Like'owanie swoich zdjęć chyba nie jest błędem do wykrycia :)

4. Like'owanie odświeża stronę, co na wersji mobilnej przewija całość do góry i będzie bardzo irytyujące dla usera, ta funkcja powinna być przerobiona na request wysłany za pomoca javascript (np wysłany ajaxem).  

5. Brak responsywnego photo-grida, na poszczególnych breakpointach widok jest obcinany.

dodałem w templates/home/index.html.twig dodatkwoy css z breakpointami dla .photo-grid

6. Okragły przycisk dostępu do profilu nachodzi na logotyp "insta shot" - na widoku mobile < 450 px

7. Pola w bazie w tabeli users typu varchar są zdecydowanie za długie, username, email 255 znaków ??? W późniejszym czasie gdy będzie potrzeba dużo indeksów to niepotrzebnie zabieramy zasoby pamięci podręcznej.