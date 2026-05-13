# 📺 Video-Tube - Profesjonalna Platforma Video (Teb Edukacja)

**Autor:** Dmytro Kyrulenko nr 14\
**Projekt:** Zaawansowany klon YouTube stworzony na potrzeby technikum.

Video-Tube to nie tylko prosty odtwarzacz wideo, ale kompletny ekosystem do hostowania i zarządzania treściami multimedialnymi, zbudowany od podstaw w technologii PHP, SQLite i Vanilla JS.

***

## 🚀 Kluczowe Funkcje (Killer-Features)

- **Custom Cinematic Player**: W pełni autorski odtwarzacz wideo. Obsługuje płynne przewijanie, dynamiczne formatowanie czasu i inteligentny pasek postępu (bez migotania).
- **Technologia Smart-Stream**: Wykorzystanie protokołu `206 Partial Content`. Pozwala to na natychmiastowe przewijanie filmów o rozmiarze nawet 2GB bez konieczności buforowania całego pliku.
- **Zaawansowany System Komentarzy**: Obsługa wątków (odpowiedzi na komentarze), polubień pod opiniami oraz asynchronicznego ładowania danych.
- **Dynamiczne Profile Użytkowników**: System awatarów, zmiana nazwy użytkownika w czasie rzeczywistym oraz personalizowane strony "Moje Wideo".
- **Interaktywny Interfejs (SPA-ish)**: Większość akcji (lajki, subskrypcje, komentarze) odbywa się bez przeładowania strony dzięki Fetch API.
- **System Zarządzania Zasobami**: Inteligentne skalowanie limitów PHP bezpośrednio z poziomu skryptu startowego (obsługa plików do 2048MB).

***

## 🏆 Dlaczego Video-Tube jest lepszy od innych projektów?

1. **Zero Konfiguracji (Plug & Play)**: W przeciwieństwie do projektów opartych na MySQL, Video-Tube używa SQLite. Oznacza to, że po pobraniu projektu i kliknięciu `run.bat` wszystko działa od razu – bez ustawiania haseł do bazy czy importowania dumpów.
2. **Profesjonalny Launcher CLI**: Projekt posiada zaawansowany skrypt startowy z interfejsem tekstowym (ASCII Art), weryfikacją licencji i automatycznym wykrywaniem środowiska PHP na całym dysku twardym.
3. **Optymalizacja pod Duże Pliki**: Większość projektów szkolnych "wywala się" przy filmach większych niż 40MB. Video-Tube radzi sobie z plikami 2GB+ dzięki modyfikacji konfiguracji serwera w locie.
4. **Bezpieczeństwo i Prawo**: Projekt zawiera pełne EULA (Umowę Użytkownika) w języku polskim, chroniącą autora przed odpowiedzialnością za treści przesyłane lokalnie.
5. **Czystość Kodu**: Brak zbędnych bibliotek i frameworków (No-Framework Policy). Całość oparta na czystym PHP i JS, co przekłada się na błyskawiczne działanie i łatwość audytu kodu.

***

## 🛠️ Stos Technologiczny

- **Backend**: PHP 8.x (Architektura proceduralna z elementami obiektowymi).
- **Frontend**: Vanilla JavaScript (ES6+), CSS3 (Modern Grid & Flexbox).
- **Baza Danych**: SQLite3 – brak konieczności posiadania serwera bazy danych.
- **Inne**: System Streamingu przez API PHP.

***

## ⚖️ Bezpieczeństwo i Licencja

Projekt został stworzony z myślą o ochronie prawnej autora (Art. 415 Kodeksu Cywilnego). Wszelkie dane są przechowywane lokalnie, co gwarantuje prywatność i brak naruszeń RODO w skali globalnej.

***

## 💻 Instrukcja Uruchomienia

1. Pobierz folder projektu.
2. Uruchom `run.bat`.
3. Przeczytaj i zaakceptuj regulamin w konsoli.
4. System automatycznie otworzy stronę `http://localhost:8000`.

***

*Projekt wykonany z pasją dla Teb Edukacja Technikum.*
