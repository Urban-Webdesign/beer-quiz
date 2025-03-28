<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pioneer Beer Quiz</title>

    <meta name="description" content="Soutěž, která prověří, jak se vyznáš v pivu, ve chmelu a v historii Žatce!">

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn...) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://beer-quiz.filipurban.cz/">
    <meta property="og:title" content="Pioneer Beer Kvíz Žatec">
    <meta property="og:description" content="Soutěž, která prověří, jak se vyznáš v pivu, ve chmelu a v historii Žatce!">
    <meta property="og:image" content="{{ asset('/images/og_image.jpg') }}">
    <meta property="og:site_name" content="Pioneer Beer Kvíz Žatec">
    <meta property="og:locale" content="cs_CZ">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://beer-quiz.filipurban.cz/">
    <meta name="twitter:title" content="Pioneer Beer Kvíz Žatec">
    <meta name="twitter:description" content="Soutěž, která prověří, jak se vyznáš v pivu, ve chmelu a v historii Žatce!">
    <meta name="twitter:image" content="{{ asset('/images/og_image.jpg') }}">

    <!-- Schema.org (pro lepší zobrazení ve vyhledávačích) -->
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Event",
          "name": "Pioneer Beer Kvíz Žatec",
          "description": "Soutěž, která prověří, jak se vyznáš v pivu, ve chmelu a v historii Žatce!",
          "image": "{{ asset('/images/og_image.jpg') }}",
          "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
          "eventStatus": "https://schema.org/EventScheduled",
          "location": {
            "@type": "Place",
            "name": "Pioneer Beer",
            "address": {
              "@type": "PostalAddress",
              "streetAddress": "nám. Prokopa Velkého 303",
              "addressLocality": "Žatec",
              "postalCode": "43801",
              "addressCountry": "CZ"
            }
          },
          "offers": {
            "@type": "Offer",
            "price": "150",
            "priceCurrency": "CZK",
            "url": "https://beer-quiz.filipurban.cz/",
            "availability": "https://schema.org/InStock"
          }
        }
    </script>

    <!-- Canonical URL (pro SEO) -->
    <link rel="canonical" href="https://beer-quiz.filipurban.cz/">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Pioneer Beer Quiz" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <div id="app" class="bg-primary min-h-[100vh] w-full"></div>
</body>

</html>
