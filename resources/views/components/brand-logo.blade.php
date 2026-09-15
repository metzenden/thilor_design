@props(['size' => 44])

{{--
    Icône de marque : aiguille + fil doré, dans l'esprit du logo fourni par
    le client (aiguille noire, fil doré bouclé façon bobine). Recréation
    vectorielle — pas une reproduction du fichier original, qui n'était pas
    accessible comme fichier dans cet environnement. À remplacer par le
    vrai logo dès qu'il est disponible en fichier (voir README).
--}}
<svg
    {{ $attributes->merge(['class' => '']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 48 48"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <!-- Fil doré bouclé -->
    <path
        d="M 26 32 C 33 30 38 24 36 17 C 34.5 12 29 10.5 26.5 14.5 C 24.5 18 27.5 21 31 19.5"
        stroke="#C89B3C"
        stroke-width="2.2"
        stroke-linecap="round"
        fill="none"
    />

    <!-- Aiguille -->
    <line x1="9" y1="39" x2="33" y2="9" stroke="#1A1410" stroke-width="2.4" stroke-linecap="round" />
    <ellipse cx="31" cy="11.5" rx="3.1" ry="4.4" transform="rotate(-38 31 11.5)" stroke="#1A1410" stroke-width="2" fill="none" />

    <!-- Bobine de fil -->
    <circle cx="16" cy="32" r="7.5" stroke="#C89B3C" stroke-width="2.2" fill="none" />
    <circle cx="16" cy="32" r="2.2" fill="#C89B3C" />
    <line x1="16" y1="26" x2="16" y2="38" stroke="#C89B3C" stroke-width="1.2" opacity="0.6" />
    <line x1="10.5" y1="32" x2="21.5" y2="32" stroke="#C89B3C" stroke-width="1.2" opacity="0.6" />
</svg>
