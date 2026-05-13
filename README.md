# Video-Tube - Projekt Edukacyjny

Szybki i nowoczesny klon YouTube stworzony na potrzeby technikum. Projekt koncentruje się na wydajności, czystym kodzie i lokalnym działaniu bez skomplikowanej konfiguracji.

## 🚀 Główne Funkcje (Killer-Features)

*   **Custom HTML5 Video Player**: Własny odtwarzacz w Vanilla JS (Play/Pause, autentyczny pasek postępu, kontrola głośności, pełny ekran).
*   **System Streamingu PHP**: Płynne przewijanie wideo dzięki obsłudze Byte-Range Requests (Partial Content 206).
*   **Live Search (Debounce)**: Wyszukiwanie filmów w czasie rzeczywistym bez przeładowania strony, z optymalizacją zapytań do bazy.
*   **Dark/Light Mode**: Dynamiczna zmiana motywu z zapamiętywaniem wyboru w `localStorage`.
*   **System Subskrypcji i Interakcji**: Funkcjonalne polubienia/dislajki dla filmów i komentarzy oraz śledzenie twórców.
*   **Zarządzanie Profilem**: Możliwość zmiany awatara (upload zdjęć) oraz nazwy użytkownika bezpośrednio z menu ustawień.
*   **Historia i Trendy**: Dynamiczne sekcje oparte na aktywności użytkowników i popularności filmów.
*   **Sticky Mini-Player**: Odtwarzacz przyklejający się do rogu ekranu podczas przewijania do komentarzy.

## 🛠️ Stos Technologiczny

*   **Front-end**: HTML5, CSS3 (Custom Properties, Grid, Flexbox), Vanilla JavaScript.
*   **Back-end**: Czysty PHP (bez frameworków).
*   **Baza Danych**: SQLite3 (lekka, plikowa, brak konieczności konfiguracji serwera bazy).

## 💻 Uruchomienie Projektu

Projekt jest zoptymalizowany pod działanie lokalne.

1.  Upewnij się, że masz zainstalowany PHP.
2.  Uruchom plik `run.bat` znajdujący się w głównym folderze.
3.  Skrypt automatycznie znajdzie PHP, uruchomi serwer i otworzy przeglądarkę pod adresem `http://localhost:8000`.

## 📌 Informacje Techniczne

*   Baza danych (`database.sqlite`) inicjalizuje się automatycznie przy pierwszym uruchomieniu.
*   Kod jest napisany zgodnie z zasadą "No Comments" dla maksymalnej przejrzystości struktury.
*   Wszystkie interakcje (lajki, komentarze, subskrypcje) odbywają się asynchronicznie przez Fetch API.
